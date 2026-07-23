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
    $cats = get_the_category();
    $cat = $cats[0] ?? null;

    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(wp_strip_all_tags($content));
    $reading_time = max(1, ceil($word_count / 200));

    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author();
    $theme_uri = get_template_directory_uri();

    /**
     * ACF user fields
     */
    $author_avatar_id = get_field('author_avatar', 'user_' . $author_id);
    $author_x = get_field('author_x', 'user_' . $author_id);
    $author_linkedin = get_field('author_linkedin', 'user_' . $author_id);

    /**
     * Avatar fallback
     */
    $author_avatar = $author_avatar_id
      ? wp_get_attachment_image_url($author_avatar_id, 'medium')
      : get_avatar_url($author_id, ['size' => 160]);

    /**
     * Social icons map
     */
    $links = [
      'instagram' => ['icon' => $theme_uri . '/assets/img/insta.svg', 'label' => 'Instagram'],
      'facebook'  => ['icon' => $theme_uri . '/assets/img/fb.svg', 'label' => 'Facebook'],
      'linkedin'  => ['icon' => $theme_uri . '/assets/img/mdi_linkedin.svg', 'label' => 'LinkedIn'],
      'whatsapp'  => ['icon' => $theme_uri . '/assets/img/whats.svg', 'label' => 'WhatsApp'],
      'x'         => ['icon' => $theme_uri . '/assets/img/x.svg', 'label' => 'X'],
    ];

    /**
     * Build author socials from existing ACF fields
     */
    $author_socials = [];

    if (!empty($author_x['url'])) {
      $author_socials[] = [
        'type'   => 'x',
        'url'    => $author_x['url'],
        'title'  => !empty($author_x['title']) ? $author_x['title'] : ($links['x']['label'] ?? 'X'),
        'target' => !empty($author_x['target']) ? $author_x['target'] : '_self',
        'icon'   => $links['x']['icon'] ?? '',
        'label'  => $links['x']['label'] ?? 'X',
      ];
    }

    if (!empty($author_linkedin['url'])) {
      $author_socials[] = [
        'type'   => 'linkedin',
        'url'    => $author_linkedin['url'],
        'title'  => !empty($author_linkedin['title']) ? $author_linkedin['title'] : $links['linkedin']['label'],
        'target' => !empty($author_linkedin['target']) ? $author_linkedin['target'] : '_self',
        'icon'   => $links['linkedin']['icon'],
        'label'  => $links['linkedin']['label'],
      ];
    }


    $current_lang = function_exists('pll_current_language')
      ? pll_current_language('slug')
      : 'en';

    $lang_map = [
      'en' => 'author_description_en',
      'de' => 'author_description_de',
      'nl' => 'author_description_nl',
      'fr' => 'author_description_fr',
    ];

    $current_field = $lang_map[$current_lang] ?? 'author_description_en';

    $author_description = get_field($current_field, 'user_' . $author_id);

    if (empty($author_description) && $current_field !== 'author_description_en') {
      $author_description = get_field('author_description_en', 'user_' . $author_id);
    }
    ?>

    <main id="primary" class="site-main single-post">
      <div class="container">

        <?php get_template_part('template-parts/blog/breadcrumbs', null, ['post_id' => $current_id]); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post__article'); ?>>

          <header class="single-post__header">
            <div class="single-post__hero">
              <div class="single-post__hero-content">
                <div class="single-post__meta">
                  <?php if ($cat): ?>
                    <span class="single-post__cat"><?php echo esc_html($cat->name); ?></span>
                  <?php endif; ?>

                  <span class="single-post__date"><?php echo esc_html(get_the_date('F j, Y')); ?></span>

                  <span class="single-post__read-time">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <path d="M12 7V12L15 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M7 3H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                      <path d="M7 21H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                      <path d="M18 3C18 7 14 8.5 14 12C14 15.5 18 17 18 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                      <path d="M6 3C6 7 10 8.5 10 12C10 15.5 6 17 6 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    <?php echo esc_html($reading_time); ?> min
                  </span>
                </div>

                <h1 class="single-post__title"><?php the_title(); ?></h1>

                <div class="single-post__author">
                  <?php if ($author_avatar): ?>
                    <span class="single-post__author-avatar">
                      <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>">
                    </span>
                  <?php endif; ?>

                  <?php if ($author_name): ?>
                    <span class="single-post__author-name"><?php echo esc_html($author_name); ?></span>
                  <?php endif; ?>
                </div>
              </div>

              <?php if (has_post_thumbnail()): ?>
                <div class="single-post__hero-media">
                  <?php the_post_thumbnail('full'); ?>
                </div>
              <?php endif; ?>
            </div>
          </header>

          <div class="single-post__content">
            <?php the_content(); ?>
          </div>

          <?php if ($author_avatar || $author_name || $author_description || !empty($author_socials)): ?>
            <section class="single-post__author-box author-box">
              <div class="author-box__inner">
                <?php if ($author_avatar): ?>
                  <div class="author-box__avatar">
                    <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>">
                  </div>
                <?php endif; ?>

                <div class="author-box__content">
                  <?php if ($author_name): ?>
                    <h3 class="author-box__name"><?php echo esc_html($author_name); ?></h3>
                  <?php endif; ?>

                  <?php if ($author_description): ?>
                    <div class="author-box__description">
                      <?php echo wp_kses_post($author_description); ?>
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($author_socials)): ?>
                    <div class="author-box__socials">
                      <?php foreach ($author_socials as $social): ?>
                        <a
                          class="author-box__social author-box__social--<?php echo esc_attr($social['type']); ?>"
                          href="<?php echo esc_url($social['url']); ?>"
                          target="<?php echo esc_attr($social['target']); ?>"
                          <?php echo $social['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                          aria-label="<?php echo esc_attr($social['label']); ?>"
                        >
                          <?php if (!empty($social['icon'])): ?>
                            <img
                              src="<?php echo esc_url($social['icon']); ?>"
                              alt="<?php echo esc_attr($social['label']); ?>"
                              width="20"
                              height="20"
                            >
                          <?php else: ?>
                            <span><?php echo esc_html($social['label']); ?></span>
                          <?php endif; ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </section>
          <?php endif; ?>

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
        $related_args['tax_query'] = [
          [
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => $cat_ids,
          ]
        ];
      } elseif (!empty($tag_ids)) {
        $related_args['tax_query'] = [
          [
            'taxonomy' => 'post_tag',
            'field' => 'term_id',
            'terms' => $tag_ids,
          ]
        ];
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