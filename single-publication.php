<?php

/**
 * The template for displaying single publications.
 *
 *
 */

get_header(); ?>


<?php while (have_posts()) : the_post(); ?>
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
