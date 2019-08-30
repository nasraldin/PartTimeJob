  <?php global $author_id; ?>
  <div class="modal fade modal-msg" id="directMessage" tabindex="-1" role="dialog" aria-labelledby="directMessage" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" ><?php _e('Send A Message','boxtheme');?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="direct-message-js">
            <div class="full form-pre">
              <div class="form-group">
                <label for="message-text" class="col-form-label"><?php _e('Message:','boxtheme');?></label>
                <textarea class="form-control" id="message-text"  name="message"></textarea>
                <input type="hidden" name="user_id" value="<?php echo $author_id;?>">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Close','boxtheme');?></button>
                <button type="submit" class="btn btn-primary"><?php _e('Send message','boxtheme');?></button>
              </div>
            </div>
            <div class="msg-sent hidden">
              <div class="popup-main confirmation-stage night-time">
                <i class="fa fa-check" aria-hidden="true"></i>
                <figure><figcaption><?php _e('Message Sent!','boxtheme');?></figcaption></figure>
                <div><strong><?php _e('However, it may take some time to receive a response.','boxtheme');?></strong></div>
                <a class="view-msg" href="#"><?php _e('View your message','boxtheme');?></a></div>
            </div>
          </form>

        </div>

      </div>
    </div>
  </div>
  <script type="text/javascript">
  (function($){
  $('#directMessage').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-title').text('New message to ' + recipient)
  //modal.find('.modal-body input').val(recipient)
  });
  })(jQuery);
  </script>