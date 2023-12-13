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

// also requires a page called 'search' to exist
add_action( 'admin_notices', 'sherman_child_ext_pubs_admin_notice_search' );

function sherman_child_ext_pubs_admin_notice_search(){
    // check for a page with the slug 'search'
    $search_page = get_page_by_path( 'search' );
    if ( ! $search_page ) {
        ?>
        <div class="error">
            <p><?php _e( 'The Sherman Child Extension Publications child theme requires that a page with the slug "search" exists. Please create this page.', 'sherman-child-ext-pubs' ); ?></p>
        </div>
        <?php
    }
}

// enqueue scripts htmx cdn
add_action( 'wp_enqueue_scripts', 'sherman_child_ext_pubs_enqueue_scripts' );

function sherman_child_ext_pubs_enqueue_scripts() {
    wp_enqueue_script( 'htmx', 'https://unpkg.com/htmx.org/dist/htmx.min.js', array(), SHERMAN_CHILD_EXT_PUBS_VERSION, true );
}



function pub_pod_filter($query)
{
    if (!is_admin() && $query->is_main_query() && is_home()) {
        $query->set('post_type', array('publication'));
    }
}

add_action('pre_get_posts', 'pub_pod_filter');

function pubsearch_run_hooks()
{
    add_action('after_setup_theme', 'pubsearch_theme_support');
    add_action('wp_enqueue_scripts', 'pubsearch_enqueue_global_scripts');
}

pubsearch_run_hooks();

function isHTMX()
{
    return isset($_SERVER['HTTP_HX_REQUEST']);
}


add_action('pre_get_posts', 'custom_publication_archive_query');

function custom_publication_archive_query($query)
{
    if (is_post_type_archive('publication') && $query->is_main_query() && !is_admin()) {
        $tax_query = array(); // Initialize the tax_query array

        if (isset($_GET['year_of_publication'])) {
            // Modify the query to filter by year_of_publication
            $year = $_GET['year_of_publication'];
            // if it's all, don't filter
            if ($year != 'all') {
                $tax_query[] = array(
                    'taxonomy' => 'publication_year',
                    'field' => 'slug',
                    'terms' => $year
                );
            }
        }

        if (isset($_GET['category'])) {
            // Modify the query to filter by category
            $categories = $_GET['category'];

            if (!empty($categories)) {
                if (is_array($categories)) {
                    $tax_query[] = array(
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => $categories, // An array of selected category terms
                        'operator' => 'IN' // Use IN to include posts in any of the selected categories
                    );
                } else {
                    // Handle a single category selection
                    $tax_query[] = array(
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => $categories // Single category term
                    );
                }
            }
        }

        if (!empty($tax_query)) {
            // Set the combined tax_query to the main query
            $query->set('tax_query', $tax_query);
        }
    }
}