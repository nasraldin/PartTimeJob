
<?php
$cats = get_terms( 'project_cat', array(
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 8,
) );
global $cat_title;
if(empty($cat_title)){
	$cat_title = __('Browse Freelancer Services','boxtheme');
}

if ( ! empty( $cats ) && ! is_wp_error( $cats ) ){ ?>
	<div class="row row-cats home-categories ">
		<div class="full">
			<div class="col-md-12 heading-cat">
				<h2 class="pypl-heading elementor-heading-title"><?php echo $cat_title;?></h2>
				<span class="break-line">&nbsp;</span>
				<div class="head-des">View over 30,0000 avaibale services by category</div>
			</div>
		</div>
			<ul class="list-cats">
			<?php
			    foreach ( $cats as $cat ) {
			    	fre_a_cat_item($cat);
			    }
	    	?>
	    	<ul>
	</div>

<?php } ?>


<style type="text/css">
	.row-cats{
		padding-top: 15px;
	}
	.list-cats{
		list-style: none;
		margin-top: 50px;
		padding: 0;
	}
	.list-cats li{
		text-align: center;
		margin-bottom: 30px;
	}
	.cat-item .cat-thumbnail{
		height: 79px;
		overflow: hidden;
	}
	.cat-item img{
	    width: 60px;
	    height: 60px;
	    vertical-align: middle;
	    border-radius: 50%;
	    background-color: #fff;
	}
	.cat-link{
		border-bottom: 1px solid green;
		display: inline-block;
		padding-bottom: 15px;
		height: 72px;
	}
	.cat-name{
		padding-top: 10px;
	}
	.cat-item h3{
		color: #0e0e0f;
	    font-size: 16px;
	    line-height: 120%;
	    font-weight: 400;
	    letter-spacing: 1px;
	    text-align: center;
	    display: inline-block;
	    margin: 0;
	    padding: 0;
	    overflow: hidden;
    	white-space: nowrap;
	}
	.cat-item .count-post{
		font-size: 16px;
		color: #5d5858;
		border-bottom: 4px solid #17a085;
		padding-bottom: 25px;
	}
.header-cat{
	padding: 20px 0;
	height: 159px;
	border-radius: 5px 5px 0 0;
	border-bottom: 3px solid #4499e6;
  	background-image: radial-gradient(#4dadf9, #4499e6, #008dff);;
}
.cat-item .wrap{
	background-color: #fff;
	border-radius: 5px;
}
.cat-item h3{
	color: #fff !important;
}
.cat-item .des-cat{
	padding: 35px 10px 15px 10px;
	height: 119px;
	border-radius: 0;
}
.home-categories h2{
	text-align: center;
	color: #666;
	font-size: 35px;
}
.head-des{
	text-align: center;
}
.heading-cat{
	color:#666;
	padding-bottom: 35px;
}
.heading-cat .break-line{
	width: 100px;
	margin: 15px auto;
	height: 3px;
	color: #17a085;
	background-color: #17a085;
	display: block;

}


</style>