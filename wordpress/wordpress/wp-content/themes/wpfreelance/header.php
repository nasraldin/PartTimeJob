<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://boxthemes.net *
 * @package BoxThemes
 * @subpackage BoxThemes
 * @since 1.0
 * @version 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link href="https://fonts.googleapis.com/css?family=Lato|PT+Sans|Raleway|Noto+Sans|Roboto|Josefin+Sans|Crimson+Text" rel="stylesheet">
	<style type="text/css">
		body{
			font-family: 'Roboto', sans-serif;
			font-size: 14px !important;
			color: #666;
		}
	</style>
	<script type="text/javascript">
		<?php
		global $app_api;

		$gg_captcha = (object) $app_api->gg_captcha;
		$enable 	= (int) $gg_captcha->enable; ?>
		var bx_global = {
			'home_url' : '<?php echo home_url() ?>',
			'admin_url': '<?php echo admin_url() ?>',
			'ajax_url' : '<?php echo admin_url().'admin-ajax.php'; ?>',
			'selected_local' : '',
			'is_archive': <?php echo is_archive() ? 1:0;?>,
			'is_free_submit_job' : true,
			'user_ID':<?php global $user_ID; echo $user_ID ?>,
			'enable_capthca': <?php echo $enable;?>,
			'current_paged':'<?php echo get_query_var('paged');?>',
			'theme_url': '<?php echo  get_template_directory_uri();?>',
			'first_gateway': '<?php echo get_first_gateway_id();?>',
			'is_archive_profile': <?php echo ( is_post_type_archive('profile') ) ? 1 : 0 ;?>,

		}
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php

	if( is_front_page() ){?>
		<div class="full cover-img"><div class="full opacity11"></div><?php }?>
			<div class="row-nav full-width header" id="full_header">
				<div class="container">
					<div class="row">
						<div  itemscope itemtype="http://schema.org/Organization" class="col-logo">
							<?php box_logo();?>
						</div>
						<div class=" col-nav ">
							<?php if ( has_nav_menu( 'top' ) ) { get_template_part( 'templates/navigation', 'top' ); } ?>
						</div>
						<div class="col-searchform hidden-sm-down hidden-md-down hidden-xs">
							<form method="GET" action="<?php echo get_post_type_archive_link('project');?>">
								<input class="keyword" type="text" name="s" placeholder="<?php _e('Search','boxtheme');?>"  /><i class="fa fa-search" aria-hidden="true"></i>
							</form>
						</div>
						<div class="col-account-menu">
							<div class="f-right align-right no-padding-left header-action">
								<?php
									if ( is_user_logged_in() ) {
										box_account_dropdow_menu();
									} else { ?>
										<ul class="main-login">
											<li class="login text-center desktop-only ">
												<a href="<?php echo box_get_static_link('login');?>" class="sign-text btn btn-login"><?php _e('Log In','boxtheme');?></a>
											</li>
											<li class=" sign-up desktop-only">
												<a href="<?php echo box_get_static_link('signup');?>" class="btn btn-signup sign-text"> <?php _e('Sign Up','boxtheme');?></a>
											</li>
											<li class=" mobile-only">
												<button type="button" class="btn btn-login " data-toggle="modal" data-target="#loginModal">
							  						<i class="fa fa-user-circle-o login-icon" aria-hidden="true"></i>
												</button>
											</li>
										</ul> <?php
									}?>
							</div>
						</div>

					</div>
				</div>
			</div> <?php
		if( is_front_page() ){

			global $role_active, $box_general;
			$role_active = get_role_active();

			$slogan 		= isset( $box_general->slogan ) ? $box_general->slogan : __('#JOIN OUR FREELANCE COMMUNITY','boxtheme');
			$banner_text 	= isset($box_general->banner_text) ? $box_general->banner_text : 'We know it\'s hard to find a online expert when you need one, which is why we\'ve set on a mission to bring them all to one place.';
			$i_wthire 		= isset($box_general->i_wthire) ? $box_general->i_wthire : __('I want to hire','boxtheme');
			$i_wtwork 		= isset($box_general->i_wtwork) ? $box_general->i_wtwork : __('I want to work','boxtheme');
			$find_fre 		= isset($box_general->find_fre) ? $box_general->find_fre : __('Find a Freelancer','boxtheme');
			$find_ajob 		= isset($box_general->find_ajob) ? $box_general->find_ajob : __('Find a Job','boxtheme');
			$post_ajob 		= isset($box_general->post_ajob) ? $box_general->post_ajob : __('Post a Job','boxtheme'); ?>

			<div class="full-width cover-content">
				<div class="container landing-three">

					<div class="heading-aligner ">
						<h1><?php echo $slogan;?></h1>
				        <div class="col-md-7 no-padding-left banner-txt">

				        	<p><?php echo $banner_text;?></p>
				    	</div>
				        <!-- CREATE PRODILE BUTTON -->

				        	<?php if ( ! is_user_logged_in() ) { ?>
				        	<div class="full">
				        		<div class="col-md-6 no-padding-left">
				        			<a href="<?php echo box_get_static_link('signup');?>?role=hire" class="btn-banner btn-primary-bg btn-iwthire"><?php echo $i_wthire;?></a>
				        		</div>
				        	</div>
				        	<div class="full">
				        		<div class="col-md-6 no-padding-left">
				        		<a href="<?php echo box_get_static_link('signup');?>?role=work" class="btn-banner btn-primary-bg btn-iwt-work"> <?php echo $i_wtwork; ?></a>
				        		</div>
				        	</div>

				        	<?php } else { ?>

					        	<?php if($role_active == EMPLOYER){ ?>
						        	<div class="full">
						        		<div class="col-md-6 no-padding-left">
							        		<a href="<?php echo get_post_type_archive_link(PROFILE);?>" class="btn-banner btn-primary-bg btn-findfre"><?php echo $find_fre;?></a>
							        	</div>
							        </div>
					            <?php } else { ?>
						            <div class="full">
						        		<div class="col-md-6 no-padding-left">
							            	<a href="<?php echo get_post_type_archive_link(PROJECT);?>" class="btn-banner btn-primary-bg btn-finjob"><?php echo $find_ajob;?></a>
							            </div>
							        </div>
					            <?php }?>
					        <?php } ?>

				        <!-- POST A PROJECT BUTTON -->

			        	<?php if( is_user_logged_in() ){ ?>
				        	<div class="full">
					        	<div class="col-md-6 no-padding-left">
						        	<?php if( $role_active == EMPLOYER  ){?>
						            	<a href="<?php echo box_get_static_link("post-project");?>" class="btn-banner btn-primary-bg btn-post-job"><?php echo $post_ajob;?></a>
						            <?php } ?>
					            </div>
					        </div>
			            <?php }?>
				    </div>
				</div>
			</div><?php
		}
	if( is_front_page() ){ ?>
		</div> <?php
	}?>
<div class="full-width main-archive"> <!-- open div for the main content. Finish before footer start !-->