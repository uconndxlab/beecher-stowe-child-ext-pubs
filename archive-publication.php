<?php

/**
 * Template Name: Search Page
 */
get_header();
?>



<div id="searchApp">
    <div class="row">
        <div class="col-lg-3">
            <div class="filter-pub">
                <form hx-get="
                        <?php
                        // the publictaion archive page
                        echo get_post_type_archive_link('publication');
                        ?>
                    " hx-target="#publication-wrap" hx-push-url="true" hx-swap="outerHTML" hx-select="#publication-wrap" hx-trigger="change" method="get">
                    <section class="filters">
                        <div>
                            <label for="search"><strong>Search:</strong></label>
                            <br>
                            <input type="text" id="search" name="s" value="<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                        <div>
                            <p><strong>Category:</strong></p>

                            <?php
                            $categories = get_categories();
                            // get only the categories with published posts
                            $categories = array_filter($categories, function ($category) {
                                $args = array(
                                    'post_type' => 'publication',
                                    'post_status' => 'publish',
                                    'category_name' => $category->slug,
                                    'posts_per_page' => 1
                                );
                                $query = new WP_Query($args);
                                return $query->have_posts();
                            });

                            // but there could be multiple categories selected
                            $currently_selected_categories = isset($_GET['category']) ? $_GET['category'] : null;


                            foreach ($categories as $category) {
                                echo '<label for="' . $category->slug . '" class="checkbox-contain">' . $category->name . '
                                        <input name = "category[]"';

                                if (
                                    $currently_selected_categories && in_array($category->slug, $currently_selected_categories)
                                ) {
                                    echo 'checked';
                                }

                                echo '
                                         type="checkbox" id="' . $category->slug . '" value="' . $category->slug . '" class="category">
                                        <span class="checkmark"></span>
                                    </label>';
                            }
                            ?>


                        </div>
                        <hr>
                        <div>
                            <label for="year_of_publication"><strong>Year:</strong></label>
                            <br>
                            <select id="year_of_publication" class="" name="year_of_publication" style="margin-top:10px">
                                <option value="all">All</option>
                                <?php
                                // year_of_publication is a pods taxonomy
                                $years = get_terms('publication_year');

                                if (isset($years)) {
                                    foreach ($years as $year) {
                                        echo '<option value="' . $year->name . '"';

                                        if (isset($_GET['year_of_publication']) && $_GET['year_of_publication'] == $year->name) {
                                            echo 'selected';
                                        }

                                        echo '>' . $year->name . '</option>';
                                    }
                                }
                                ?>

                            </select>

                        </div>

            </div>

            </form>

        </div>

        <div class="col-lg-9" id="publication-wrap">

            <ul id="publication-list">
                <!-- <li class="publication-block"><a href="./interior.html"><img src="./img/farm.jpg">
                            <p class="publication-label"><span>Health</span></p>
                            <h2>Another Example</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam faucibus luctus ligula ac condimentum. Aenean pulvinar leo ipsum, in consequat mi elementum at.</p>
                        </a>
                    </li> -->

                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    // these are not pods, they're ACF custom post types
                    // if the keep_private field is set to "private", don't show it
                    // $post_status = get_post_status();
                    // if ($post_status == 'private') {
                    //     continue;
                    // }





                    $post = get_post();
                    $first_category = get_the_category()[0]->name;
                    $item_link = get_the_permalink();


                    // check if the post has is_pdf set to true
                    $isPDF = get_field('is_pdf');


                    if ($isPDF) {
                        $pdf_upload = get_field('pdf_upload');

                        $item_link = $pdf_upload['guid'];
                    }


                    ?>
                    <li class="publication-block">
                        <a <?php if ($isPDF) : ?> target="_blank" <?php endif; ?> href="<?php echo $item_link

                                                                                        ?>">
                            <?php the_post_thumbnail(); ?>
                            <?php if ($isPDF) : ?>
                                <!-- if it's a pdf, show the pdf icon, material design icon -->
                                <i class="pdf-icon material-icons">picture_as_pdf</i>
                            <?php endif; ?>
                            <p class="publication-label"><span><?php echo $first_category ?></span></p>
                            <h2>

                                <?php
                                the_title(); ?>


                            </h2>
                            <p><?php the_excerpt(); ?></p>
                        </a>
                    </li>
                <?php endwhile; ?>


            </ul>

            <?php if (!have_posts()) : ?>
                <div class="alert alert-warning">
                    <?php _e('Sorry, no results were found.', 'sage'); ?>
                </div>
            <?php
            endif;

            the_posts_pagination(
                array(
                    'prev_text'          => "Prev",
                    'next_text'          => "Next",
                    'before_page_number' => '<span class="meta-nav screen-reader-text">Page</span>',
                )
            );
            ?>
        </div>
    </div>
</div>


<?php
get_footer();
