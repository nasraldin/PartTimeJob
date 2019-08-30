<?php global $author_id;
wp_reset_query();
global $user_ID;
$args = array(
    'post_type' => 'project',
    'author'=> $user_ID,
    'posts_per_page' => -1,
    'post_status' => 'publish',
);
$query = new WP_Query($args);
            ?>
<div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-labelledby="directMessage" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" ><?php _e('Invite user bid on your job.','boxtheme');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="invite-bid-js">
           <?php if( $query->have_posts() ) {        ?>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label"><?php _e('Select Project:','boxtheme');?></label>
                        <select class="form-control required"   name="project_id">
                        <option><?php _e('Select a project','boxtheme');?></option>
                        <?php while( $query->have_posts() ){ $query->the_post(); ?>
                        <option value="<?php the_ID();?>"><?php the_title(); ?></option>
                        <?php } ?>
                        </select>
                        <input type="hidden" name="freelancer_id" value="<?php echo $author_id;?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><?php _e('Invite','boxtheme');?><i class="fa fa-spinner fa-spin"></i></button>
                    </div>
            <?php } else{
                _e('You don\'t have any open project','boxtheme');
            }
            wp_reset_query(); ?>
        </form>
      </div>

    </div>
    </div>
</div>