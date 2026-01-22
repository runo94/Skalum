<?php get_header(); ?>


<section class="blog">
  <div class="container">

    <div class="blog__header">
      <span class="blog__badge">Blog</span>
      <h1 class="blog__title">Blog</h1>
    </div>

    <div class="blog__grid" data-page="1">
      <?php
      $q = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => get_option('posts_per_page'),
      ]);

      if ($q->have_posts()) :
        while ($q->have_posts()) : $q->the_post();
          get_template_part('template-parts/blog/card');
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </div>

    <button class="blog__load">Load More</button>

  </div>
</section>

<?php get_footer(); ?>
