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


global $role; // visitor, FREELANCER, EMPLOYER, administrator;
$role = bx_get_user_role();

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link href="https://fonts.googleapis.com/css?family=Lato|PT+Sans|Raleway|Noto+Sans|Roboto|Josefin+Sans" rel="stylesheet">
	<style type="text/css">
		body{
			1font-family: 'Raleway', sans-serif;
			font-family: 'Roboto', sans-serif;
			font-size: 14px;
			color: #666;
			/*font-family: 'Josefin Sans', sans-serif !important;
			font-family: 'Noto Sans', sans-serif !important;
			font-family: 'Lato', sans-serif !important;
			*/
		}
		.flag.flag-ad, .flag.flag-ae, .flag.flag-af, .flag.flag-ag, .flag.flag-ai, .flag.flag-al, .flag.flag-am, .flag.flag-ao, .flag.flag-ar, .flag.flag-as, .flag.flag-at, .flag.flag-au, .flag.flag-aw, .flag.flag-ax, .flag.flag-az, .flag.flag-ba, .flag.flag-bb, .flag.flag-bd, .flag.flag-be, .flag.flag-bf, .flag.flag-bg, .flag.flag-bh, .flag.flag-bi, .flag.flag-bj, .flag.flag-bm, .flag.flag-bn, .flag.flag-bo, .flag.flag-br, .flag.flag-bs, .flag.flag-bt, .flag.flag-bw, .flag.flag-by, .flag.flag-bz, .flag.flag-ca, .flag.flag-cc, .flag.flag-cd, .flag.flag-cf, .flag.flag-cg, .flag.flag-ch, .flag.flag-ci, .flag.flag-ck, .flag.flag-cl, .flag.flag-cm, .flag.flag-cn, .flag.flag-co, .flag.flag-cr, .flag.flag-cu, .flag.flag-cv, .flag.flag-cw, .flag.flag-cx, .flag.flag-cy, .flag.flag-cz, .flag.flag-de, .flag.flag-dj, .flag.flag-dk, .flag.flag-dm, .flag.flag-do, .flag.flag-dz, .flag.flag-ec, .flag.flag-ee, .flag.flag-eg, .flag.flag-er, .flag.flag-es, .flag.flag-et, .flag.flag-fi, .flag.flag-fj, .flag.flag-fk, .flag.flag-fm, .flag.flag-fo, .flag.flag-fr, .flag.flag-ga, .flag.flag-gb, .flag.flag-gd, .flag.flag-ge, .flag.flag-gg, .flag.flag-gh, .flag.flag-gi, .flag.flag-gl, .flag.flag-gm, .flag.flag-gn, .flag.flag-gq, .flag.flag-gr, .flag.flag-gs, .flag.flag-gt, .flag.flag-gu, .flag.flag-gw, .flag.flag-gy, .flag.flag-hk, .flag.flag-hn, .flag.flag-hr, .flag.flag-ht, .flag.flag-hu, .flag.flag-id, .flag.flag-ie, .flag.flag-il, .flag.flag-im, .flag.flag-in, .flag.flag-io, .flag.flag-iq, .flag.flag-ir, .flag.flag-is, .flag.flag-it, .flag.flag-je, .flag.flag-jm, .flag.flag-jo, .flag.flag-jp, .flag.flag-ke, .flag.flag-kg, .flag.flag-kh, .flag.flag-ki, .flag.flag-km, .flag.flag-kn, .flag.flag-kp, .flag.flag-kr, .flag.flag-kw, .flag.flag-ky, .flag.flag-kz, .flag.flag-la, .flag.flag-lb, .flag.flag-lc, .flag.flag-li, .flag.flag-lk, .flag.flag-lr, .flag.flag-ls, .flag.flag-lt, .flag.flag-lu, .flag.flag-lv, .flag.flag-ly, .flag.flag-ma, .flag.flag-mc, .flag.flag-md, .flag.flag-me, .flag.flag-mg, .flag.flag-mh, .flag.flag-mk, .flag.flag-ml, .flag.flag-mm, .flag.flag-mn, .flag.flag-mo, .flag.flag-mp, .flag.flag-mq, .flag.flag-mr, .flag.flag-ms, .flag.flag-mt, .flag.flag-mu, .flag.flag-mv, .flag.flag-mw, .flag.flag-mx, .flag.flag-my, .flag.flag-mz, .flag.flag-na, .flag.flag-nc, .flag.flag-ne, .flag.flag-nf, .flag.flag-ng, .flag.flag-ni, .flag.flag-nl, .flag.flag-no, .flag.flag-np, .flag.flag-nr, .flag.flag-nu, .flag.flag-nz, .flag.flag-om, .flag.flag-pa, .flag.flag-pe, .flag.flag-pf, .flag.flag-pg, .flag.flag-ph, .flag.flag-pk, .flag.flag-pl, .flag.flag-pn, .flag.flag-pr, .flag.flag-ps, .flag.flag-pt, .flag.flag-pw, .flag.flag-py, .flag.flag-qa, .flag.flag-re, .flag.flag-ro, .flag.flag-rs, .flag.flag-ru, .flag.flag-rw, .flag.flag-sa, .flag.flag-sb, .flag.flag-sc, .flag.flag-sd, .flag.flag-se, .flag.flag-sg, .flag.flag-sh, .flag.flag-si, .flag.flag-sk, .flag.flag-sl, .flag.flag-sm, .flag.flag-sn, .flag.flag-so, .flag.flag-sr, .flag.flag-ss, .flag.flag-st, .flag.flag-sv, .flag.flag-sx, .flag.flag-sy, .flag.flag-sz, .flag.flag-tc, .flag.flag-td, .flag.flag-tf, .flag.flag-tg, .flag.flag-th, .flag.flag-tj, .flag.flag-tk, .flag.flag-tl, .flag.flag-tm, .flag.flag-tn, .flag.flag-to, .flag.flag-tr, .flag.flag-tt, .flag.flag-tv, .flag.flag-tw, .flag.flag-tz, .flag.flag-ua, .flag.flag-ug, .flag.flag-us, .flag.flag-uy, .flag.flag-uz, .flag.flag-va, .flag.flag-vc, .flag.flag-ve, .flag.flag-vg, .flag.flag-vi, .flag.flag-vn, .flag.flag-vu, .flag.flag-ws, .flag.flag-ye, .flag.flag-za, .flag.flag-zm, .flag.flag-zw {

   	 		background: url( <?php echo get_template_directory_uri();?>/img/flags.png) no-repeat;
		}
		.side_bar .inside .basic-info p.gear-icon::before,.group-icon::before {
			background-image: url( <?php echo get_template_directory_uri();?>/img/icon_sprites_act.png);
		}
	</style>
	<script type="text/javascript">
		var bx_global = {
			'home_url' : '<?php echo home_url() ?>',
			'admin_url': '<?php echo admin_url() ?>',
			'ajax_url' : '<?php echo admin_url().'admin-ajax.php'; ?>',
			'selected_local' : '',
			'is_free_submit_job' : true,
			'user_ID':'<?php global $user_ID; echo $user_ID ?>',

		}
	</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	$html_logo = get_custom_logo();
	$default_logo = '<img class="logo style-svg" src="'.get_template_directory_uri().'/img/logo.png'.'" />';
?>
<?php do_action('before_header_menu' );?>
<div class="row-nav full-width header" id="full_header">
	<div class="container">
		<div class="row">
			<div class="col-md-2 col-logo col-xs-6">
				<?php if( ! empty( $html_logo ) ){ echo $html_logo; } else { ?>
				<a class="logo" href="<?php echo home_url();?>"> <?php echo $default_logo; ?>	</a>
				<?php }?>
			</div>
			<?php
			$main_menu_class = 'col-md-6';
			$user_menu_class="col-md-4";
			if( is_user_logged_in() ){
				$main_menu_class = 'col-md-7';
				$user_menu_class="col-md-3";
			}
			?>
			<div class="no-padding col-nav <?php echo $main_menu_class;?> ">
				<?php if ( has_nav_menu( 'top' ) ) { get_template_part( 'template/navigation', 'top' ); } ?>
			</div>
			<!-- seach form here !-->
			<div class="<?php echo $user_menu_class;?> col-xs-3 col-account-menu">
				<div class="f-right align-right no-padding-left header-action">
					<?php
						if ( is_user_logged_in() ) { box_account_dropdow_menu(); } else { ?>
						<ul class="main-login">
							<li class="login text-center desktop-only ">
								<a href="<?php echo box_get_static_link('login');?>" class="sign-text btn btn-login"><?php _e('Log In','boxtheme');?></a>
							</li>
							<li class=" sign-up desktop-only ">
								<a href="<?php echo box_get_static_link('signup');?>" class="btn btn-signup sign-text"> <?php _e('Sign Up','boxtheme');?></a>
							</li>
							<li class=" mobile-only">
								<button type="button" class="btn btn-login " data-toggle="modal" data-target="#loginModal">
			  						<i class="fa fa-user-circle-o login-icon" aria-hidden="true"></i>
								</button>
							</li>
						</ul>
					<?php } ?>
				</div>
			</div> <!-- .header-action !-->
		</div>
	</div>	<!-- .navigation-top -->
</div>

