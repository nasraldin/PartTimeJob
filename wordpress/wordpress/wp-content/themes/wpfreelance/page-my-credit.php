<?php
/**
 *	Template Name: My Credit
 */
?>
<?php get_header(); ?>



	<div class="container site-container">
<?php

global $user_ID;
$bank_account = (OBJECT) array('account_name' => '', 'account_number' => '', 'bank_name'=>'' );
$ins_credit = BX_Credit::get_instance();

$credit = $ins_credit->get_ballance($user_ID);
$withdraw_info = $ins_credit->get_withdraw_info($user_ID);

$paypal_email= $account_number = '';

if( ! empty ($withdraw_info->paypal_email) )
	$paypal_email = $withdraw_info->paypal_email;
if( ! empty ($withdraw_info->bank_account) ){
	$bank_account = (object) $withdraw_info->bank_account;
	if( ! empty( $bank_account->account_number ) )
		$account_number = $bank_account->account_number;
}
global $symbol, $checkout_mode;

$pargs = array(
	'post_type' => 'transaction',
	'post_status' => 'pending',
	'posts_per_page' => -1,
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key'     => 'receiver_id',
			'value'   => $user_ID,
			),
		// array(
		// 	'key'     => 'is_realmode',
		// 	'value'   => $checkout_mode,
		// ),
	),
);
$pending_transaction = new WP_Query($pargs);
$pending_credit =0;
if($pending_transaction->have_posts()){
	while($pending_transaction->have_posts()){
		$pending_transaction->the_post();
		global $post;
		$fre_receive = (float) get_post_meta($post->ID,'fre_receive', true);
		$pending_credit = $pending_credit + $fre_receive;
	}
}
wp_reset_query();
$mode = '';



$wargs = array(
	'author' 			=> $user_ID,
	'post_type' 		=> 'withdrawal',
	'post_status' 		=> array('pending','publish'),
	'posts_per_page' 	=> -1,
	);

$widthdraws  = new WP_Query($wargs);
$withdrawing =0;
$withdrawn =0;

if($widthdraws->have_posts()){
	while($widthdraws->have_posts()){
		$widthdraws->the_post();
		global $post;
		$amount = (float) get_post_meta($post->ID,'amount', true);
		if($post->post_status == 'pending'){
			$withdrawing = $withdrawing + $amount;
		} else if($post->post_status == 'publish' ){
			$withdrawn = $withdrawn + $amount;
		}

	}
}
wp_reset_query();

?>

		<div  id="content" class="row site-content page-credit">
			<div class="col-md-12 line-item credit-info">

					<center><h2><?php _e('Credit Information', 'boxtheme' );?></h2> <br /></center>
					<div class="col-md-sec5">
						<div class="form-group">
							<center><h3><?php _e('Available','boxtheme');?></h3></center>
						<center><span class="full text-center price"><?php echo  box_get_price_format($credit->available);?></span></center>
						<hr />
						</div>

					</div>
					<div class="col-md-sec5">
						<div class="form-group">
							<center><h3><?php _e('Pending','boxtheme');?></h3></center>
							<center><span class="full text-center price"><?php echo box_get_price_format($credit->pending);?></span></center>
							<hr />
						</div>
					</div>
					<div class="col-md-sec5">
						<div class="form-group">
							<center><h3><?php _e('Processing','boxtheme');?></h3></center>
						<center><span class="full text-center price"><?php echo box_get_price_format($pending_credit);?></span></center>
						<hr />
						</div>
					</div>

					<div class="col-md-sec5 col-withdrawing">
						<div class="form-group">
							<center><h3><?php _e('Withdrawing','boxtheme');?></h3></center>
						<center><span class="full text-center price"><?php echo  box_get_price_format($withdrawing);?></span></center>
						<hr />
						</div>

					</div>
					<div class="col-md-sec5 col-withdraw">
						<div class="form-group">
							<center><h3><?php _e('Withdrawn','boxtheme');?></h3></center>
							<center><span class="full text-center price"><?php echo box_get_price_format($withdrawn);?></span></center>
							<hr />
						</div>
					</div>


				<div class="col-md-12 ">
						<a class="btn btn-radius btn-buy-credit" style="float: right;margin-right: 25px;" href="<?php echo box_get_static_link('deposit');?>"><?php _e('Deposit Credit','boxtheme');?> </a>
				</div>
			</div>
			<div class="col-md-12 line-item">
				<h2><?php _e('Withdrawal Setting','boxtheme');?></h2> <br />
				<ul class="nav nav-tabs">
				  <li class="active"><a href="#withdraw"><?php _e('Withdraw','boxtheme');?></a></li>
				  <li><a href="#paypal" href="#"><?php _e('PayPal','boxtheme');?></a></li>
				  <li><a href="#bank_info"><?php _e('Bank account','boxtheme');?></a></li>
				</ul>
				<div class="tab-content">
					<div id="withdraw" class="tab-content-item">
						<?php

						if( $credit->available >= 10  ){
							if(  empty( $paypal_email ) && empty( $account_number )  ){
								echo '<p>&nbsp;</p>';
								_e(' Please setup paypal email or bank account to withdraw.','boxtheme');
							} else {?>
								<form id="frm_withdraw" class="">
									<div class="form-group">
										<label for="withdraw_amount"><?php _e('Amount','boxtheme');?></label>
										<input type="number" class="form-control required" required id="withdraw_amount" name="withdraw_amount" laceholder="<?php _e('How much you want to withdraw?','boxtheme');?>">
									</div>
									<div class="form-group">
										<label for="withdraw_type"><?php _e('Select Method','boxtheme');?></label>
										<select class="form-control required" required name="withdraw_method">
											<?php if( !empty( $paypal_email ) ) { ?>
												<option value="paypal_email"><?php _e('PayPal','boxtheme');?></option>
											<?php } ?>
											<?php if( !empty( $account_number ) ) { ?>
												<option value="bank_account"><?php _e('Bank Account','boxtheme');?></option>
											<?php } ?>

										</select>
									</div>
									<div class="form-group">
										<label for="withdraw_type"><?php _e('Note','boxtheme');?></label>
										<textarea class="form-control" name="withdraw_note" required></textarea>
										<small><?php _e('Add your phone number or note some tips to help admin transfer money easily to you.','boxtheme');?></small>
									</div>
									<button type="submit" class="btn btn-primary"><?php _e('Send request','boxtheme');?></button>
								</form>
							<?php } ?>
						<?php } else {?>
							<p></p>
							<?php _e('Your balance is not enough to withdraw','boxtheme');?>
						<?php } ?>
					</div>
					<div id="paypal" class="tab-content-item hidding">
						<form id="frm_paypal" class="withdraw-info">
							<span class="btn-edit-self 111"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
							<div class="form-group is-view">
								<span for="paypal_email"><strong><?php _e('PayPal Email','boxtheme');?></strong></span> <p><?php echo $paypal_email;?></p>
							</div>
							<div class="full is-edit">
								<div class="form-group">
									<label for="paypal_email"><?php _e('PayPal Email:','boxtheme');?></label>
									<input type="text" class="form-control required" id="paypal_email" name="paypal_email" required aria-describedby="paypal_email" value="<?php echo $paypal_email;?>" placeholder="<?php _e('Your PayPal Email','boxtheme');?>">

								</div>
								<button type="submit" class="btn btn-primary is-edit"><?php _e('Save','boxtheme');?></button>
							</div>

						</form>
					</div>

					<div id="bank_info" class=" tab-content-item hidding">
						<form id="frm_bank_info" class="withdraw-info">
							<div class="form-group"><h3><?php _e('Setup your bank account','boxtheme');?> <span class="btn-edit-self "><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span></h3></div>

							<div class="full is-view">
								<div class="form-group">
									<label for="account_name"><?php _e('Name on account','boxtheme');?></label>
									<p><span><?php echo !empty($bank_account->account_name) ? $bank_account->account_name : 'Not set';?></span></p>

								</div>
								<div class="form-group">
									<label for="account_number"><?php _e('Account number or IBAN','boxtheme');?></label>
									<p><?php echo !empty($bank_account->account_number) ? $bank_account->account_number : 'Not set'; ?></p>
								</div>

								<div class="form-group">
									<label for="exampleInputPassword1"><?php _e('Bank name','boxtheme');?></label>
									<p><?php echo !empty($bank_account->bank_name) ? $bank_account->bank_name :'Not set';?></p>
								</div>
							</div>

							<div class="full is-edit">
								<div class="form-group">
									<label for="account_name"><?php _e('Name on account','boxtheme');?></label>
									<input type="text" class="form-control required" id="account_name" required name="account_name" aria-describedby="account_name" value="<?php echo $bank_account->account_name;?>" placeholder="<?php _e('Name on account','boxtheme');?>">
									<small id="emailHelp" class="form-text text-muted"><?php _e('Your bank account name','boxtheme');?></small>
								</div>
								<div class="form-group">
									<label for="account_number"><?php _e('Account number or IBAN','boxtheme');?></label>
									<input type="text" class="form-control required" required id="account_number" name="account_number" value="<?php echo $bank_account->account_number;?>" aria-describedby="" placeholder="<?php _e('Account number or IBAN','boxtheme');?>">
								</div>

								<div class="form-group">
									<label for="exampleInputPassword1"><?php _e('Bank name','boxtheme');?></label>
									<input type="text" class="form-control required" id="bank_name" name="bank_name" value="<?php echo $bank_account->bank_name;?>" placeholder="<?php _e('Bank name','boxtheme');?>">
								</div>
								<button type="submit" class="btn btn-primary"><?php _e('Save','boxtheme');?></button>
							</div>
						</form>
					</div>
				</div>


			</div>

			<div id="profile" class="col-md-12 line-item history-order-section"> <!-- start left !-->

			     <?php get_template_part( 'templates/dashboard/list', 'order' ); ?>
			</div> <!-- end left !-->
		</div>

	</div>

<?php get_footer();?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){

			$(".nav-tabs a").click(function(event){
				$(".nav-tabs li").removeClass('active');
				var _this = $(event.currentTarget);
				_this.closest("li").addClass('active');
				var section = _this.attr('href');
				$(".tab-content-item").addClass('hidding');
				$(section).removeClass('hidding');
				return false;4
			});
			$(".btn-edit-self").click(function(event){
				var _this = $(event.currentTarget);
				_this.closest("form").toggleClass('is-edit');

			})

		})

	})(jQuery);
</script>
