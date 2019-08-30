<?php
/**
 *Template Name: Blog
 */
?>
<?php get_header(); ?>
<?php the_post(); ?>

<div class="row heading-block" id="content" >
	<div class="container ">
		<div class="wrap-heading text-center">
			<h1><i class="fa fa-link"></i> <?php the_title();?></h1>
			<span>Keep up to date with the latest news</span>
		</div>
	</div>
</div>
<div class="row">
	<div class="container list-posts">
		<?php
		$args = array(
			'post_type' => 'post',
			'post_status' =>'publish',
		);
		$the_query = new WP_Query($args);

		if( $the_query->have_posts() ){
			echo '<ul>';
			while($the_query->have_posts() ){
				$the_query->the_post();
				?>
				<li class="col-md-4 block-item">
					<div class="full">
						<div class="post-thumbnail">
							<a href="<?php the_permalink();?>">
								<?php if( has_post_thumbnail())  the_post_thumbnail();  else {?>
									<img src="<?php echo get_stylesheet_directory_uri();?>/img/nothumb.jpg" />
								<?php } ?>
							</a>
						</div>
						<div class="post-title">
							<a href="<?php the_permalink();?>"><?php the_title(); ?></a>
						</div>
					</div>
				</li>
				<?php
			}
			echo '</ul>';
		}
		?>
	</div>
</div>
<style type="text/css">
	body{
		background-color: #f2f2f2 !important;
	}
	.heading-block{
		background-color: #202123;
		min-height: 250px;
		padding: 100px 0 100px 0;
		text-align: center;
		color: #fff;
	}
	.page-template-box-blog  h1{
		font-size: 33px;
	}
	.list-posts ul{
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.list-posts{
		padding: 50px 0;
	}
	.post-thumbnail img{
		max-width: 100%;
		overflow: hidden;
	}

	.post-title a{
		color: #636363;
		font-size: 16px;
		display: block;
		padding: 5px 0;
	}
	.block-item{
		margin-bottom: 10px;
	}
	.block-item .full{
		background-color: #fff;
		padding-bottom: 20px;
	}
	.post-title{
		padding: 5px;
	}
</style>

<?php get_footer();?>