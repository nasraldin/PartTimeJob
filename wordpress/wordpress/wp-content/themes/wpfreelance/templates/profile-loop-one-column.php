<?php
global $post;
global $user_ID;
$profile    = BX_Profile::get_instance()->convert($post);

?>
<div class="listing-row freelancer-list-item hoverable clearfix">
    <div class="freelancer-image-container row-image-container col-md-2">
            <a class="freelancer__image" data-hook="member-popover" href = "<?php echo get_author_posts_url($profile->post_author);?>"><?php echo get_avatar($profile->post_author, 150);?></a>
    </div>
    <div class="freelancer__info-container col-md-7">
        <div class="freelancer__info">
            <header class="freelancer__title">

                <h5 class="freelancer__name crop">
                    <a href = "<?php echo get_author_posts_url($profile->post_author);?>" class="link" online marketing href="#"><?php echo $profile->profile_name;?></a>
                </h5>
                <div class="freelancer__badges">
                    <span class="cert cert-level10-small " data-level="TOP" title="<?php echo $profile->professional_title;?>"></span>
                </div>
                <div class="freelancer__bio hidden-phone hidden-sm"><?php echo $profile->professional_title;?></div>
                <ul class="member-info horizontal crop">
                    <li class="crop"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $profile->country;?></li>
                </ul>
            </header>
            <div class="freelancer__skills hidden-sm">
                <ul class="skills__lists widget-tag-list">
                    <li class="skills__item  ">
                        <a class="tag-item  js-tag-item-update" data-update="online marketing">Online arketing </a>
                    </li>
                    <li class="skills__item  ">
                        <a class="tag-item  js-tag-item-update" data-update="online marketing">Web develop </a>
                    </li>
                    <li class="skills__item  hidden out">
                        <a class="tag-item  js-tag-item-update" data-update="social media marketing">
                            social media marketing                            </a>
                    </li>
                    <li class="skills__item  hidden out">
                        <a class="tag-item  js-tag-item-update" data-update="facebook marketing">
                            facebook marketing                            </a>
                    </li>
                    <li class="skills__item  hidden out">
                        <a class="tag-item  js-tag-item-update" data-update="search engine optimization (seo)">
                            search engine optimization (seo)                            </a>
                    </li>
                                                            <li class="more-skills">
                            <span class="js-more-skills__toggler">
                                <span class="more">
                                    + 3 more skills                                    </span>
                                <span class="less hide">
                                    show less                                    </span>
                            </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <aside class="freelancer__bio clearfix visible-phone ">SEO Consultant, 3000+ Reviews, #1in SEO &amp; Marketing on PPH **USA &amp; UK Link Building**</aside>
    <div class="freelancer__rating-price-wrapper col-md-3">
                <div class="freelancer__review">
            <div class="freelancer__review--wrapper">
                <span class="js-tooltip" title="User Rating and Reviews">
                <div class="widget-jsModuleRating clearfix star-rating-system">
    <ul class="rating clearfix none-style inline" data-disabled="1" data-hook="star-rating">
                <li class="left" data-index="1"><i class="fa fa-star" aria-hidden="true"></i></li>
                <li class="left" data-index="2"><i class="fa fa-star" aria-hidden="true"></i></li>
                <li class="left" data-index="3"><i class="fa fa-star" aria-hidden="true"></i></li>
                <li class="left" data-index="4"><i class="fa fa-star" aria-hidden="true"></i></li>
                <li class="left" data-index="5"><i class="fa fa-star" aria-hidden="true"></i></li>
            </ul>
            <input data-hook="rating" type="hidden" name="rating" value="5">
</div>
<span class="freelancer__review-number">4.9</span><span class="freelancer__total-reviews">(3469)</span>                </span>
            </div>
        </div>
                <div class="freelancer__price hidden-phone">
            <div class="medium price-tag quiet">
                <span title="">$45<small>/hr</small></span>            </div>
        </div>
    </div>
    <div class="freelancer-actions col-md-3 pull-right ">
                    <div class="freelancer__contact--full">
                <a class="btn freelancer__contact" rel="nofollow" href="#">Contact</a>            </div>
                    <a href="#" class="freelancer__view-profile hidden-phone hidden-sm btn">View profile</a>
    </div>
</div>