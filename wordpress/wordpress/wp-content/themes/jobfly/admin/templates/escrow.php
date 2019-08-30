<?php

$group_option = "escrow";
$option = BX_Option::get_instance();
$escrow = $option->get_escrow_setting();
$commision = (object) $escrow->commision;
//echo '<pre>';
//var_dump($escrow);
// var_dump($commision);
//echo '</pre>';

?>
<div id="<?php echo $group_option;?>" class="main-group">
	<div class="sub-section " id="commision" >
		<h2> <?php _e('Config Escrow system','boxtheme');?> </h2> <br />
	   	<div class="sub-item" id="commision">
			<form >
				<div class="form-group row">
					<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('Commision','boxtheme');?></label>
					<div class="col-md-9"><input class="form-control auto-save" type="number" value="<?php echo $commision->number;?>" name = "number" min="1"  level="1" step="any" id="example-text-input"></div>
				</div>
				<div class="form-group row">
					<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('Commistion type','boxtheme');?></label>
					<div class="col-md-9">
						<select class="form-control auto-save" name="type" id="exampleSelect2"  level="1">
							<option value="fix" <?php selected( $commision->type, 'emp' ); ?> > <?php _e('Fix number','boxtheme');?></option>
							<option value="percent" <?php selected( $commision->type, 'fre' ); ?> ><?php _e('Percent','boxtheme');?></option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('Who is pay commision','boxtheme');?></label>
					<div class="col-md-9">
						<select class="form-control auto-save" name="user_pay" id="exampleSelect2"  level="1">
							<option value="emp" <?php selected( $commision->user_pay, 'emp' ); ?> >Employer</option>
							<option value="fre"<?php selected( $commision->user_pay, 'fre' ); ?> >Freelancer</option>
							<option value="share" <?php selected( $commision->user_pay, 'share' ); ?> >50/50</option>
						</select>
					</div>
				</div>
			</form>
		</div>

		<?php

		$opt_credit = BX_Option::get_instance()->get_group_option('opt_credit');
		$active = 'credit';
		if ( isset( $escrow->active ) ) {
			$active = $escrow->active;
		}

		$hide_credit = $hide_pp = '';
		if($active == 'credit'){
			$hide_pp = ' hide ';
		}
		if( $active == 'paypal_adaptive'){
			$hide_credit = ' hide ';
		}

		?>
		<div id="<?php echo $group_option;?>" class="main-group ">
			<div class="row">
				<div class="col-md-3">
					<h2> <?php _e('Select the Eccrow system','boxtheme');?> </h2>
				</div>
				<div class="col-md-9" style="padding-top: 20px;">
					<select class="form-control auto-save" name="active">
						<option value="credit" <?php selected( $active,'credit' ) ?> ><?php _e('Credit System','boxtheme');?></option>
						<option value="paypal_adaptive" <?php selected( $active,'paypal_adaptive' ) ?>><?php _e('PayPal Adaptive','boxtheme');?></option>
					</select>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="sub-section <?php echo $hide_credit;?>" id="opt_credit" >

	<h2> <?php _e('Credit System','boxtheme');?> </h2>
   	<div class="sub-item" id="opt_credit">
		<form >
			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('Number Credit Auto Deposit for new account','boxtheme');?></label>
				<div class="col-md-9"><input class="form-control auto-save" type="number_credit_default" multi="0" value="<?php echo $opt_credit->number_credit_default;?>" name = "number_credit_default" id="number_credit_default"></div>
			</div>
		</form>
	</div>
</div>
<div class="main-group <?php echo $hide_pp;?>" id="paypal_adaptive" >
	<?php



	$api_appid = $api_userid = $app_signarute = $api_userpassword = '';
	$sandbox_mode = 1;
	$api_appid_name = 'api_appid_sandbox';
	$api_userid_name = 'api_userid_sandbox';
	$api_useremail_name = 'api_useremail'; // user will receive commision fee.
	$app_signarute_name = 'app_signarute_sandbox';
	$api_userpassword_name = 'api_userpassword_sandbox';
	$paypal_adaptive = (OBJECT) BX_Option::get_instance()->get_group_option('paypal_adaptive');

	if( ! empty( $paypal_adaptive ) ){
		if(  isset( $paypal_adaptive->sandbox_mode) )
			$sandbox_mode = (int) $paypal_adaptive->sandbox_mode;

		if( $sandbox_mode ){

			$api_appid = $paypal_adaptive->api_appid_sandbox;
			$api_appid = 'APP-80W284485P519543T';
			$api_userid = $paypal_adaptive->api_userid_sandbox;
			$app_signarute = $paypal_adaptive->app_signarute_sandbox;
			$api_userpassword = $paypal_adaptive->api_userpassword_sandbox;
			$api_useremail = $paypal_adaptive->api_useremail_sandbox; // account will receive commison fee.

		} else {
			$api_appid_name = 'api_appid';
			$api_userid_name = 'api_userid';
			$api_useremail_name = 'api_useremail';
			$app_signarute_name = 'app_signarute';
			$api_userpassword_name = 'api_userpassword';


			$api_appid = $paypal_adaptive->api_appid;
			$api_userid = $paypal_adaptive->api_userid;
			$api_useremail = $paypal_adaptive->api_useremail;
			$app_signarute = $paypal_adaptive->app_signarute;
			$api_userpassword = $paypal_adaptive->api_userpassword;
		}

	}

	?>

   	<div class="sub-item" id="paypal_adaptive">
		<form style="padding-top: 50px;">
			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><h2><?php _e('Sandbox Mode','boxtheme');?> </h2></label>
				<div class="col-md-9" style="padding-top: 15px;"><?php bx_swap_button('paypal_adaptive','sandbox_mode', $sandbox_mode, 0);?>
					<?php if( !$sandbox_mode ){?>
						<small class="full row-explain text-left"> <?php _e('Live mode is enabling.','boxtheme');?> </small>
					<?php } else {?>
					<small class="full row-explain text-left"> <?php _e('Sandbox mode is enabling.','boxtheme');?> </small>
					<?php } ?>
				</div>
			</div>
			<div class="form-group">
				<h2> <?php if($sandbox_mode)  _e('PayPal Adaptive Sandbox Mode Settings','boxtheme'); else _e('PayPal Adaptive Live Mode Settings','boxtheme'); ?> </h2> <br />
			</div>
			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('PayPal Account','boxtheme');?></label>
				<div class="col-md-9">
					<input class="form-control auto-save" type="text" multi="0" value="<?php echo $api_useremail;?>" name = "<?php echo $api_useremail_name;?>" id="api_useremail">

				</div>
				<div class="col-md-9 pull-right">
					<small class="full row-explain text-right"> <?php _e('Account use to get/set API and use to receive the commision fee if have.','boxtheme');?> </small>
				</div>
			</div>
			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('API User ID','boxtheme');?></label>
				<div class="col-md-9"><input class="form-control auto-save" type="text" multi="0" value="<?php echo $api_userid;?>" name = "<?php echo $api_userid_name;?>" id="api_userid"></div>
			</div>

			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('API Password','boxtheme');?></label>
				<div class="col-md-9"><input class="form-control auto-save" type="api_userpassword" multi="0" value="<?php echo $api_userpassword;?>" name = "<?php echo $api_userpassword_name;?>" id="api_userpassword"></div>
			</div>
			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('API Signarute','boxtheme');?></label>
				<div class="col-md-9"><input class="form-control auto-save" type="text" multi="0" value="<?php echo $app_signarute;?>" name = "<?php echo $app_signarute_name;?>" id="app_signarute"></div>
			</div>
			<div class="form-group row">
				<label for="example-text-input" class="col-md-3 col-form-label"><?php _e('API App ID','boxtheme');?></label>
				<div class="col-md-9">
					<input class="form-control auto-save" type="text" multi="0" <?php if($sandbox_mode) echo 'disabled';?>  value="<?php echo $api_appid;?>" name = "<?php echo $api_appid_name;?>" id="api_appid">
					<?php if($sandbox_mode){?>
						<small class="full row-explain text-right"> <?php _e('APP-80W284485P519543T is default APP ID for sandbox mode.','boxtheme');?> </small>
					<?php }?>
				</div>
			</div>
		</form>
	</div>
</div>
