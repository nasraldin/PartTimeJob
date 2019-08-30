<?php
the_post();
global $post, $job;

?>

<section class="page-job-detail__header">
    <div class="box box-md">
        <div class="absolute-right premium-popover-trigger"></div>
        <div class="row">

            <div class="col-md-12 col-content-wrapper">
                <div class="row">
                    <div class="col-lg-10 col-md-9 col-content">
                        <div class="job-header-info">
                            <h1 class="job-title"><?php the_title();?></h1>
                            <div class="row">

                                <?php
                                $terms = get_the_terms( get_the_ID(), 'skill' );

                                if ( $terms && ! is_wp_error( $terms ) ) :

                                    $draught_links = array();

                                    foreach ( $terms as $term ) {
                                        $draught_links[] = '<a class="big ilabel mkt-track" href="'.get_term_link($term).'">'.$term->name.'</a>';
                                    }

                                    $on_draught = join( " ", $draught_links );
                                    ?>

                                    <div class="tag-list col-sm-12">
                                        <?php echo  $on_draught  ; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if ( ! empty( $job->address) ){ ?>
                                        <span class="company-location"><i class="ion-ios-location-outline"></i> &nbsp; <span itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><?php echo $job->address;?></span></span>
                                    <?php }?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if( ! is_user_logged_in() ){ ?>
                                        <div class="salary not-signed-in">
                                            <span class="salary-icon-stack">
                                                <i class="ion-ios-circle-outline"></i>
                                                <i class="ion-social-usd"></i>
                                            </span>
                                            <a class="popup-link view-salary show-modal-login" data-popup-id="sign_in" data-link-status="sign_in_tab" href="javascript:void(0)"><?php _e('Sign in to view','boxtheme');?></a>
                                        </div>
                                     <?php } else { ?>
                                        <span class="salary">
                                            <span class="salary-icon-stack">
                                                <i class="ion-ios-circle-outline"></i>
                                                <i class="ion-social-usd"></i>
                                            </span>
                                          <strong class="text-primary salary-text"><?php echo $job->min_salary;?> - <?php echo $job->max_salary;?> USD</strong>
                                         </span>
                                    <?php } ?>
                                    <span class="view gray-light"> <?php printf(__('%d views','boxtheme'), (int) $job->{BOX_VIEWS} );?></span>
                                    <span class="gray-light m-l-xs m-r-xs">-</span>
                                    <span class="expiry gray-light">
                                    <?php
                                    $_expired_date = get_post_meta($job->ID,'_expired_date', true);
                                    if( !empty( $_expired_date)){?>
                                         <?php printf(__('Expires %s.',ET_DOMAIN), human_time_diff( time(), strtotime($_expired_date)) ); ?>
                                    <?php } else {
                                        printf(__('Posted %s ago','boxtheme'), human_time_diff( get_the_time('U'), time() ) );
                                    } ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Hide this in mobile-->
                    <!-- TODO: Use another class for case "not authenticated" -->
                    <div class="col-lg-2 col-md-3 col-btn  ">
                        <div class="row">
                            <?php
                            $apply_link = box_get_static_link('apply').'/'.$job->ID.'/';

                            ?>
                            <div class="col-xs-6 col-xs-push-6 col-md-12 col-md-push-0">
                            <a  class="btn btn-primary btn-block btn-apply track-event"href="<?php echo esc_url( $apply_link);?>">  <?php _e('Apply Now','boxtheme');?></a>
                            </div>
                            <div class="col-xs-6 col-xs-pull-6 col-md-12 col-md-pull-0 save-job-wrapper">
                                <?php

                                $class= is_user_logged_in() ? "btn-save-job" : "show-modal-login";
                                //update_user_meta( $user_ID, 'job_ids_saved', true);
                                $ids = get_user_meta($user_ID, 'job_ids_saved', true);
                                if( !is_array($ids) )
                                    $ids = array();

                                ?>

                                <?php if( ! in_array( $job->ID, $ids ) ){ ?>
                                        <button class="btn btn-primary btn-outline btn-block btn-save <?php echo $class;?> track-event" ><i class="fa fa-heart-o" aria-hidden="true"> &nbsp;</i><?php _e('Save Job','boxtheme');?></button>

                                <?php } else { ?>
                                    <button class="btn btn-primary btn-outline btn-block btn-save  track-event" ><i class="fa fa-heart" aria-hidden="true"></i> &nbsp; <?php _e('Saved','boxtheme');?></button>

                                <?php } ?>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>