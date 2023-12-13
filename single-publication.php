<?php
/**
 * The template for displaying single publications.
 *
 *
 */

get_header(); ?>


<?php
// Start the loop.
while (have_posts()) : the_post();
?>

<div id="primary" class="content-area">
  <div id="expub_content">

    <?php
        // do the content
        the_content();
    ?>

  </main>
</div>

<?php
// End the loop.
endwhile;
?>



<?php
get_footer();
