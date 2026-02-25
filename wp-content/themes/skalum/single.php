<?php
/**
 * Single Post template
 */

defined('ABSPATH') || exit;

get_header();

if (have_posts()):
  while (have_posts()):
    the_post();

    $current_id = get_the_ID();
    ?>

    <main id="primary" class="site-main single-post">
      <div class="container">

        <?php get_template_part('template-parts/blog/breadcrumbs', null, ['post_id' => $current_id]); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post__article'); ?>>

          <header class="single-post__header">
            <h1 class="single-post__title"><?php the_title(); ?></h1>

            <div class="single-post__meta">
              <?php
              $cats = get_the_category();
              $cat = $cats[0] ?? null;
              ?>
              <?php if ($cat): ?>
                <span class="single-post__cat"><?php echo esc_html($cat->name); ?></span>
              <?php endif; ?>

              <span class="single-post__date"><?php echo esc_html(get_the_date('F j, Y')); ?></span>
            </div>

            <?php if (has_post_thumbnail()): ?>
              <div class="single-post__thumb">
                <?php the_post_thumbnail('large'); ?>
              </div>
            <?php endif; ?>
          </header>

          <div class="single-post__content">
            <?php the_content(); ?>
          </div>

        </article>
      </div>

      <?php
      /**
       * Related posts: by categories first, fallback to tags, fallback to latest
       */
      $cat_ids = wp_get_post_terms($current_id, 'category', ['fields' => 'ids']);
      $tag_ids = wp_get_post_terms($current_id, 'post_tag', ['fields' => 'ids']);

      $related_args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => [$current_id],
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
      ];

      if (!empty($cat_ids)) {
        $related_args['tax_query'] = [[
          'taxonomy' => 'category',
          'field' => 'term_id',
          'terms' => $cat_ids,
        ]];
      } elseif (!empty($tag_ids)) {
        $related_args['tax_query'] = [[
          'taxonomy' => 'post_tag',
          'field' => 'term_id',
          'terms' => $tag_ids,
        ]];
      }

      $related_q = new WP_Query($related_args);

      if ($related_q->have_posts()): ?>
        <section class="related-posts">
          <div class="container">
            <h2 class="related-posts__title"><?php esc_html_e('Related Posts', 'your-textdomain'); ?></h2>

            <div class="related-posts__grid">
              <?php while ($related_q->have_posts()):
                $related_q->the_post(); ?>
                <?php get_template_part('template-parts/blog/card', null, ['post_id' => get_the_ID()]); ?>
              <?php endwhile; ?>
            </div>
          </div>
        </section>
        <?php
      endif;
      wp_reset_postdata();
      ?>

    </main>

    <?php
  endwhile;
endif;

get_footer();
