<?php
/**
 *Template Name: Post project
 */
if(  class_exists('acf_pro') )
	acf_form_head();
?>
<?php get_header(); ?>

<div class="container site-container">
	<div class="site-content" id="content" >

			<?php the_post(); ?>
			<?php
				if( is_user_logged_in() ){
					get_template_part( 'templates/project/form-post', 'project' );
				} else {
					_e('Please login to post project','boxtheme');
				}
			?>

	</div>
</div>

<div class="modal fade" id="puPaymentGateways" tabindex="-1" role="dialog" aria-labelledby="puPaymentGateways">
	<div class="modal-dialog" role="document">
		<div class="modal-content">



		</div>
	</div>
</div>
<script type="text/html" id="tmpl-frm_pay_premium_job">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h2 class="modal-title" id="freMarkAsCompleteh2"><?php _e('Pay to upgrade job','boxtheme');?></h2>
	</div>
	<div class="modal-body">
	  	<form id="frmPayPremiumJob" class="frmPayPremiumJob frm-main-checkout step">
			<?php $label = __('Select payment gateways','boxtheme');?>
			<?php bo_list_paymentgateways($label);?>
			<input type="hidden" name="package_id" id="package_id" value="{{{data.ID}}}" />
			<input type="hidden" name="project_id" id="project_id" value="{{{data.project_id}}}" />
			<div class="col-sm-12  full">
				<div class="col-sm-9 text-rigt pull-right">
					<button class="btn-pay-job btn btn-main-act text-rigt pull-right" type="submit"><?php _e('Pay Now','boxtheme');?></button>
				</div>
			</div>

		</form>
	</div>
</script>

<?php
	$step = isset($_GET['step']) ? $_GET['step'] : '';
	if($step == 'checkout'){ ?>
	<script type="text/javascript">
		(function($){
			$('#puPaymentGateways').modal().show();
		})(jQuery);
	</script><?php
	}
?>

<?php get_footer();?>

