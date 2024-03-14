<?php

/**
 * The template for displaying single publication.
 */

get_header(); ?>


<?php while (have_posts()) : the_post(); ?>

<?php
// if it's a pdf (is_pdf is true), just 301 redirect to the pdf file
$isPDF = get_field('is_pdf');
if ($isPDF) {
  $pdf_upload = get_field('pdf_upload');
  $item_url = $pdf_upload;
  wp_redirect($item_url, 301);
}
?>
  <div id="primary" class="content-area">
    <div id="expub_content">
      <?php
      // do the content
      the_content();
      ?>

      </main>
    </div>
  </div>

  <?php endwhile; ?>

  <?php
  get_footer();
