<?php
/**
 *	Template Name: Empty page
 */
?>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link href="https://fonts.googleapis.com/css?family=Lato|PT+Sans|Raleway:200,300|Noto+Sans|Roboto|Josefin+Sans" rel="stylesheet">
	<style type="text/css">
		body{
			font-family: 'Roboto', sans-serif;
			font-size: 14px;
			color: #666;
			/*font-family: 'Josefin Sans', sans-serif !important;
			font-family: 'Noto Sans', sans-serif !important;
			font-family: 'Lato', sans-serif !important;
			*/
		}
	</style>
	<script type="text/javascript">
		var bx_global = {
			'home_url' : '<?php echo home_url() ?>',
			'admin_url': '<?php echo admin_url() ?>',
			'ajax_url' : '<?php echo admin_url().'admin-ajax.php'; ?>',
			'selected_local' : '',
			'is_free_submit_job' : true,

		}
	</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="row-nav full-width header">
	<div class="container">
		<div class="row">
			<div class="col-md-3 f-right align-right no-padding-left header-action">

					<ul class="main-login">
						<li class="login text-center desktop-only dropdown text-center">
							<a rel="nofollow" class="dropdown-toggle" data-toggle="dropdown" href="#">Sign in <span class="caret"></span></a>
							<div class="dropdown-menu width-7">
								tesst 123
							</div>
						</li>
						<li class="sign-up desktop-only">
							<a href="<?php echo box_get_static_link('signup');?>" class="btn btn-account btn-signup"> <?php _e('Sign up','boxtheme');?></a>
						</li>
						<li class="mobile-only">
							<button type="button" class="btn btn-login" data-toggle="modal" data-target="#loginModal">
		  						<span class="glyphicon glyphicon-user login-icon"></span>
							</button>
						</li>
					</ul>

			</div> <!-- .header-action !-->
		</div>
	</div>	<!-- .navigation-top -->
</div>

<div class="container page-nosidebar site-container">
	<div class="row">
		<div class="dropdown">
		 	<ul>
			 	<li class="login dropdown text-center">
					<a rel="nofollow" class="dropdown-toggle" data-toggle="dropdown" href="https://freelancerviet.vn/dang-nhap.html" aria-expanded="true">Đăng nhập <span class="caret"></span></a>
					<div class="dropdown-menu width-7">
						<div class="col-md-12">
							<form method="post" action="" class="form login-form login-form-ajax">
								<div class="alert alert-warning" style="display:none;">
									<p></p>
								</div>
								<div class="form-group"><input type="text" class="form-control email" placeholder="Địa chỉ email"></div>
								<div class="form-group"><input type="password" class="form-control password" placeholder="Mật khẩu"></div>

								<button type="button" class="btn btn-raised btn-success btn-block" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading..." onclick="login_ajax_submit(this);return false;">Đăng nhập</button>
								<div class="divider"></div>

							</form>
						</div>
					</div>

				</li>
		 	</ul>
		</div>
	</div>
</div>
<?php wp_footer();?>
</body>

