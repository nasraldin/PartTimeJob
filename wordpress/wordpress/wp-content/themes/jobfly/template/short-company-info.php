<div class="side_bar">
<?php
global $post, $job;

$company_user = $post->post_author;
$company =   get_userdata($company_user );
$types = get_company_types();
$ranges = get_company_ranges();



$umeta = get_user_meta( $job->post_author,  'umeta', true);
$countries = get_countries();
$overview = $description = $company_type = $country = $range = '';
if( isset( $umeta['overview']) )
    $overview = $umeta['overview'];


if( isset( $umeta['description']) )
    $description = $umeta['description'];

if( isset( $umeta['company_type']) ){
    $types = get_company_types();
    if( isset( $types[ $umeta['company_type'] ]) )
        $company_type = $types[$umeta['company_type']];
}

$range_key = 0;
if( isset( $umeta['range']) ){
    $range_key = $umeta['range'];
    if( isset( $ranges[ $umeta['range'] ]) ){
        $range = $ranges[$umeta['range']];
    }
}


$country_key = 'us';
if( isset( $umeta['country']) ){
    $country_key = strtolower($umeta['country']);
    if( isset( $countries[ $umeta['country'] ]) ){

        $country = $countries[$umeta['country']];
    }
}
?>
    <div class="inside">
    <!-- Last updated: "2017-12-18 18:56:39 +0700"-->
        <div class="logo">
            <a href="#">
                <?php echo box_get_avatar($company_user);?>
            </a>
        </div>
        <div class="employer-info">
            <h3 class="name"><a href="#" id="<?php echo $company->user_login;?>"><?php echo $company->display_name;?></a></h3>
            <div class="basic-info">
                <?php if( !empty($description)) { ?>
                <div class="short"><?php echo trim($description);?></div>
                <?php } ?>
                <?php if( !empty($company_type)) { ?>
                    <p class="gear-icon"><?php echo trim($company_type);?></p>
                <?php }?>
                <?php if( !empty($range) ){ ?>
                <p class="group-icon"><?php echo trim($range);?></p>
                <?php } ?>
                <?php if( !empty($country) ){?>
                <div class="country-icon"><i class="flag flag-<?php echo $country_key;?>"></i><span class="country-name"><?php echo $country;?></span></div>
                <?php } ?>
                <div class="working-date"> <i class="fa fa-calendar"></i><span> Monday - Friday</span></div>
                <div class="overtime"><i class="fa fa-clock-o"></i><span>&nbsp; No OT</span></div>
            </div>
        </div>
        <div class="employer-jobs-info">
            <div class="more_jobs">
                <div class="current-jobs links"><a href="#"><?php echo count_user_posts($job->post_author,'job');?> Jobs</a></div>
                <div class="employer-profile links hide"> <a href="#">View our company page</a>           </div>
            </div>
        </div>
    </div>

    <div class="outside-jr hide">
        <div class="saved-wrapper">
            <div class="saved-body">
                <a data-remote="true" rel="nofollow" data-method="post" href="/saved_jobs?job_id=30087">
                    <div class="big saved saved-default"></div>
                <div class="saved-text saved-text-default">
                <span class="save_job">Save Job</span>
                </div>
                </a>
            </div>
            <div class="hide saved-body">
                <a data-remote="true" rel="nofollow" data-method="delete" href="/saved_jobs/30087">
                    <div class="big saved saved-red saved-red-hover"></div>
                    <span class="save-job">
                 <div class="saved-text saved-text-success transparent">           Saved             </div>             </span>
                </a>
            </div>
        </div>

    </div>
</div>