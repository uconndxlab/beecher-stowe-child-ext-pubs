<?php
require_once('inc/acf.php');

function link_parent_theme_style()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'link_parent_theme_style');


function exclude_private_posts_from_loop($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // Exclude private posts
    $query->set('post_status', 'publish');

    // You can add other conditions or modifications here if needed

}
add_action('pre_get_posts', 'exclude_private_posts_from_loop');


/** version */
define('stowe_CHILD_EXT_PUBS_VERSION', '1.0.0');

// register a sidebar called publication_lede

add_action('widgets_init', 'stowe_child_ext_pubs_widgets_init');

function stowe_child_ext_pubs_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Publication Archive Top', 'stowe-child-ext-pubs'),
        'id'            => 'publication_lede',
        'description'   => __('Widgets in this area will be shown on the publication archive.', 'stowe-child-ext-pubs'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}

/** Requires that an ACF post type of "publication" exists. 
 * If it doesnt, display a message to the user. **/

add_action('admin_notices', 'stowe_child_ext_pubs_admin_notice');

function stowe_child_ext_pubs_admin_notice()
{
    $post_type_object = get_post_type_object('publication');
    if (!$post_type_object) {
?>
        <div class="error">
            <p><?php _e('The Stowe Child Extension Publications child theme requires that an ACF post type of "Publication" exists. Please create this post type.', 'stowe-child-ext-pubs'); ?></p>
        </div>
    <?php
    }
}

// also requires a page called 'search' to exist
add_action('admin_notices', 'stowe_child_ext_pubs_admin_notice_search');

function stowe_child_ext_pubs_admin_notice_search()
{
    // check for a page with the slug 'search'
    $search_page = get_page_by_path('search');
    if (!$search_page) {
    ?>
        <div class="error">
            <p><?php _e('The Stowe Child Extension Publications child theme requires that a page with the slug "search" exists. Please create this page.', 'stowe-child-ext-pubs'); ?></p>
        </div>
<?php
    }
}

// enqueue scripts htmx cdn
add_action('wp_enqueue_scripts', 'stowe_child_ext_pubs_enqueue_scripts');

function stowe_child_ext_pubs_enqueue_scripts()
{
    wp_enqueue_script('htmx', 'https://unpkg.com/htmx.org/dist/htmx.min.js', array(), stowe_CHILD_EXT_PUBS_VERSION, true);
}


add_action('wp_enqueue_scripts', 'stowe_child_ext_pubs_enqueue_styles');
function stowe_child_ext_pubs_enqueue_styles()
{
    wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), stowe_CHILD_EXT_PUBS_VERSION);
}


function pub_pod_filter($query)
{
   // when on the home query, or when looking at a category or tag archive, or when searching, or when looking at a date archive
    if ($query->is_main_query() && (is_home() || is_category() || is_tag() || is_search() || is_date())) {
        $query->set('post_type', array('post', 'publication'));
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

function custom_publication_search_template($template) {
    if (is_search() && get_query_var('post_type') == 'publication') {
        // Use the custom archive-publication.php template
        return locate_template('archive-publication.php');
    }
    return $template;
}
add_filter('template_include', 'custom_publication_search_template');

