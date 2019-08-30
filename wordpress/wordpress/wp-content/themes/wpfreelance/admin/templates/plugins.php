<?php
//update_option('box_plugins','');
$group_option = 'box_plugins';
$section = 'acf_pro';

$option = BX_Option::get_instance();
$plugin_setting = (object)$option->get_plugins_settings();
$acf = (object)$plugin_setting->acf_pro;
$project_group_id = isset($acf->project_group_id) ? $acf->project_group_id : 0;

?>
<div id="<?php echo $group_option;?>" class="main-group">
<div class="sub-section" id="<?php echo $section;?>">
        <div class="sub-item" id="project_group_id">
        <div class="form-group row">
            <div class="col-md-3"><h3> Custom Fields Advanced </h3></div>
            <div class="col-md-9 form-group">

                <span class="full"> Select group fields for post project form </span>
                <?php
                $args = array(
                    'post_type' => 'acf-field-group',
                    'post_status' => 'publish',
                );
                $fields = new WP_Query($args);
                if($fields->have_posts()){ ?>
                    <select class="form-control auto-save" level ="1" name="project_group_id" >
                        <option value="0">Select Group</option>
                        <?php
                        while($fields->have_posts()){
                            $fields->the_post();
                            global $post; ?>
                            <option value="<?php echo $post->ID;?>" <?php selected( $project_group_id, $post->ID);?>><?php the_title();?></option>
                            <?php

                        }
                        ?>
                    </select><?php

                } else {
                    $link = admin_url('edit.php?post_type=acf-field-group');
                    ?>
                    There is no any fields yet.<br />
                    Go to  <a target="_blank" href="<?php echo $link ;?>"> this section </a> to add new group field <?php

                }
                ?>
                </select>
                <br />
                <br />
                <br />
                <div class="hide">
                    <span class="full"> Select group fields for profile edit page </span>
                    <?php
                    if($fields->have_posts()){ ?>
                        <select class="form-control  auto-save">
                            <option value="0">Select Group</option>
                            <?php
                            while($fields->have_posts()){
                                $fields->the_post();
                                global $post; ?>
                                <option value="<?php echo $post->ID;?>"><?php the_title();?></option>
                                <?php

                            }
                            ?>
                        </select><?php

                    }
                    ?>
                </div>

            </div>
        </div>

    </div>