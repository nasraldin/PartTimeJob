 <?php
/**
 * Template Name: Membership Plans
*/
?>
<?php get_header(); ?>

<div class="container site-container">
  <div class="row site-content" id="content" >
    <div class="col-md-12 detail-project text-justify">
      <?php the_post(); ?>
      <h1 class="col-md-12"><?php the_title();?></h1>


      <?php

      $args = array(
        'post_type' => '_package',
        'meta_key' => 'pack_type', // buy credit or premium_post
        'meta_value' => 'membership',

	    );
	    $list_subscriptions = array();
	    $the_query = new WP_Query($args);
	    global $symbol;
	    if($the_query->have_posts()){


	    	?>
	    	<section data-id="24ff98f" class="elementor-element elementor-element-24ff98f elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section" data-element_type="section">
						<div class="container">
				<div class="elementor-row">
					<?php
	    	while($the_query->have_posts()){


	    		$the_query->the_post();

	    		$price = get_post_meta(get_the_ID(),'price', true);
	    		if($price ==0 || empty($price))
	    			continue;
    			$link = box_get_static_link('signup');
				$new_link = add_query_arg('plan',get_the_ID(), $link);

	    		?>
	    		<div data-id="eed332f" class="elementor-element elementor-element-eed332f elementor-column elementor-col-33 elementor-top-column" data-element_type="column">
					<div class="elementor-column-wrap  elementor-element-populated">
						<div class="elementor-widget-wrap">
							<div data-id="a8b4c81" class="elementor-element elementor-element-a8b4c81 elementor-widget elementor-widget-box-price-table" data-element_type="box-price-table.default">
								<div class="elementor-widget-container">

									<div class="elementor-price-table">

										<div class="elementor-price-table__header">
											<h3 class="elementor-price-table__heading"><?php the_title();?></h3>
										</div>

										<div class="elementor-price-table__price">
											<span class="elementor-price-table__currency"><?php echo $symbol;?></span>
											<span class="elementor-price-table__integer-part"><?php echo $price;?></span>
											<span class="elementor-price-table__period elementor-typo-excluded">Per month</span>
										</div>

										<?php the_content(); ?>

										<div class="elementor-price-table__footer 111">
											<a class="elementor-price-table__button disable unable-click btn-membership elementor-button elementor-size-md" href="<?php echo $new_link;?>">Choose Plan</a>
										</div>
									</div>

								</div>
							</div>
						</div>
						</div>
					</div>
	    		<?php
	    		}
	    	?>
	    </div>
	</div>
</section>
	    	<?php
	    }

    ?>
    </div>
  </div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo plugins_url();?>/elementor-pro/assets/css/frontend.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url();?>/elementor/assets/css/frontend.min.css">


<?php get_footer();?>