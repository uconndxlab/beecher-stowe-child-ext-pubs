<?php 

/** version */
define( 'SHERMAN_CHILD_EXT_PUBS_VERSION', '1.0.0' );

/** Requires that an ACF post type of "publication" exists. 
 * If it doesnt, display a message to the user. **/

add_action( 'admin_notices', 'sherman_child_ext_pubs_admin_notice' );

function sherman_child_ext_pubs_admin_notice() {
    $post_type_object = get_post_type_object( 'publication' );
    if ( ! $post_type_object ) {
        ?>
        <div class="error">
            <p><?php _e( 'The Sherman Child Extension Publications child theme requires that an ACF post type of "Publication" exists. Please create this post type.', 'sherman-child-ext-pubs' ); ?></p>
        </div>
        <?php
    }
}