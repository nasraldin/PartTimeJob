<?php
/**
 * single-project-detail-bid-form.php
 * keyword bid-form.php,bid_form
 */

global $project, $class_bidded, $bidding, $symbol;
$budget = (float) $project->_budget;


$pay_ifo = box_get_pay_info($budget);
$cms_fee = $pay_ifo->cms_fee;
$emp_pay = $pay_ifo->emp_pay;
$fre_receive = $pay_ifo->fre_receive;

$label_text = __('Freelancer pay this fee','boxtheme');

if( $emp_pay > $budget ) {
	$label_text = __('Employer pay this fee','boxtheme');
}
if ( ! $bidding && $project->post_status == 'publish' ) { ?>
  <form id="bid_form" class="bid-form" <?php echo $class_bidded;?>>
      <h2> <?php _e('Bid on project','boxtheme');?></h2>
     	<div class="form-group row bd-bottom">
        	<div class="col-md-6">
        		<label  class="full col-form-label"><?php _e('Bid price','boxtheme') ;?></label>
        		<small class="full"><?php _e('Total amount the client will see on your proposal','boxthem');?></small>
        	</div>

        	<div class="col-sm-6 bid-col-right">
        		<span class="f-left col-md-1"><?php echo $symbol;?></span>
          	<input type="number" size="6" class="col-md-10 pull-right inline input-price col-xs-11" step="any" id="_bid_price" name="_bid_price" aria-describedby="" placeholder="<?php _e('Your budget','boxtheme');?>" value="<?php echo $budget;?>">
        </div>
     	</div>
     	<div class="form-group row bd-bottom">
        	<label for="inputEmail3" class="col-sm-6 col-form-label"><?php _e('Fee Service','boxtheme');?> <span class="tooltip" title="<?php echo $label_text;?>">?</span> </label>
        	<div class="col-sm-6 bid-col-right">
           	<span class="f-left col-md-1"><?php echo $symbol;?></span> <input type="text" class="col-xs-11 col-md-10 pull-right" readonly id="fee_servicce" placeholder="<?php _e('Fee service','boxtheme');?>" value="<?php echo $cms_fee;?>" />
        	</div>
     	</div>
     	<div class="form-group row bd-bottom">
        	<div for="inputEmail5" class="col-sm-6 col-form-label">
        		<label> <?php _e('You\'ll be paid','boxtheme');?>  </label>
        		<small class="text-left  full "><?php _e('This fund will auto-release to your balance after the project is finished.','boxtheme');?></small>
        	</div>

        	<div class="col-sm-6 bid-col-right">
          	<span class="f-left col-md-1"><?php echo $symbol;?></span> <input type="text" class="col-md-10 col-xs-11 input-price pull-right" readonly id="_bid_receive" name="_bid_receive" value="<?php echo max(0,$fre_receive);?>" />
        	</div>
      </div>

     	<div class="form-group">
        <label for="exampleTextarea"><?php _e('Cover Letter','boxtheme');?></label>
        <textarea class="form-control no-radius" id="post_content" required name="post_content" rows="8"><?php if($bidding) echo $bidding->post_content;?></textarea>
     	</div>

     	<div class="form-group">
        	<label for="bid_dealine"><?php _e('How long will this project take?','boxtheme');?></label>
        	<select class="form-control" id="bid_dealine" name="_dealine">
        		<option><?php _e('Select duration','boxtheme');?></option>
  	        <?php
  	        $list = list_dealine();
  	        foreach ($list as $key => $value) { ?>
  	        	<option value="<?php echo $key;?>"> <?php echo $value;?> </option>
  	        <?php } ?>
        	</select>
     	</div>
     	<?php wp_nonce_field( 'sync_bid', 'nonce_bid_field' ); ?>
     	<div class="form-group hide">
        	<label for="attach_file"><?php _e('Attachments (optional)','boxtheme');?></label>
        	<input type="file" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
        	<small id="fileHelp" class="form-text text-muted"><?php _e('Send this file to project\'s owner.','boxtheme');?></small>
    	</div>
    	<button type="submit" class="btn btn-action btn-bid pull-left"><?php _e('Bid','boxtheme');?> &nbsp;</button>
    	<a href="<?php the_permalink( );?>" class="btn-cancel btn-cancel-bid"><?php _e('Cancel','boxtheme');?> &nbsp;</a>
    	<input type="hidden"  name="post_parent" value="<?php echo $project->ID; ?>" />
  </form>
  <?php
}
if($class_bidded){
    echo '<br />';
}
?>