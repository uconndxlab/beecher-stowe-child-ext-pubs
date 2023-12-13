<?php 

// redirect to the /publications archive page
wp_redirect( get_post_type_archive_link( 'publication' ) );
