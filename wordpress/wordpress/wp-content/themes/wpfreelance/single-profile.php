<?php
the_post();
global $post;
$url_redirect = get_author_posts_url($post->post_author);

header("Location:$url_redirect");
exit;
?>