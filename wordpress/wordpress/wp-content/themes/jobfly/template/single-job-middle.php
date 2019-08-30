<?php
global $job;
?>
<section class="page-job-detail__detail box-md box">
     <div class="row">
        <div class="col-md-4 col-sm-12 tab-sidebar"><?php get_template_part( 'template/short-company', 'info' );?></div>
        <div class="col-md-8  col-sm-12 tab-main-content">
            <?php
            $benefits = $job->benefit;

            if( is_array($benefits) && !empty($benefits) ){ ?>
                <div class="benefits">
                    <div class="what-we-offer mobile-box">
                        <h2>What We Can Offer</h2>
                        <?php
                        if( !empty($benefits[0])){ ?>
                             <div class="benefit row">
                                <div class="benefit-icon col-xs-1"><i class="fa fa-fw fa-lg fa-dollar"></i>
                                </div>
                                <div class="benefit-name col-xs-11"><?php echo $benefits[0];?></div>
                            </div>
                            <?php
                        }
                        if( !empty($benefits[1]) ){ ?>
                            <div class="benefit row">
                                 <div class="benefit-icon col-xs-1"><i class="fa fa-fw fa-lg fa-user-md"></i>
                                </div>
                                <div class="benefit-name col-xs-11"><?php echo $benefits[1];?></div>
                            </div><?php
                        }
                        if( ! empty ( $benefits[2]) ){ ?>
                            <div class="benefit row">
                                <div class="benefit-icon col-xs-1"><i class="fa fa-fw fa-lg fa-graduation-cap"></i>
                                </div>
                                <div class="benefit-name col-xs-11"><?php echo $benefits[2];?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="job-description mobile-box">
                <h2><?php _e('Job Description','boxtheme');?></h2>
                <div class="description" >
                   <?php the_content();?>
                </div>
             </div>

            <div class="job-requirements mobile-box">
                <h2><?php _e('Your Skills and Experience','boxtheme');?></h2>
                <div class="requirements""><?php the_requirement();?></div>
                <p><?php box_social_share();?> </p>
            </div>

        </div>
    </div>
</section>