<?php get_header(); ?>

<section class="blog">
  <div class="sky-bg">
    <div id="particles-js" class="blog__particles" aria-hidden="true"></div>

    <div class="container">
      <div class="blog__header">
        <span class="blog__badge">Blog</span>
        <h1 class="blog__title">Blog</h1>
      </div>

      <?php
      $q = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => 1,
      ]);
      ?>

      <div
        class="blog__grid"
        data-page="1"
        data-max-pages="<?php echo esc_attr((int) $q->max_num_pages); ?>"
      >
        <?php
        if ($q->have_posts()):
          while ($q->have_posts()):
            $q->the_post();
            get_template_part('template-parts/blog/card');
          endwhile;
          wp_reset_postdata();
        endif;
        ?>
      </div>

      <?php if ((int) $q->max_num_pages > 1): ?>
        <button class="blog__load" type="button">Load More</button>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>