<?php
/**
 * Single Case template
 */

defined('ABSPATH') || exit;

get_header();

if (have_posts()):
    while (have_posts()):
        the_post();

        $current_id = get_the_ID();
        ?>

        <main id="primary" class="site-main single-case">
            <div class="container">
                <?php get_template_part('template-parts/case/breadcrumbs', null, ['post_id' => get_the_ID()]); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('single-case__article'); ?>>

                    <div class="single-case__content">
                        <?php the_content(); ?>
                    </div>

                </article>
            </div>

            <?php
            /**
             * Related Projects (same taxonomy terms)
             */
            $term_ids = wp_get_post_terms($current_id, 'case_category', ['fields' => 'ids']);

            $related_args = [
                'post_type' => 'case',
                'posts_per_page' => 3,
                'post__not_in' => [$current_id],
                'ignore_sticky_posts' => true,
                'no_found_rows' => true,
            ];

            if (!empty($term_ids)) {
                $related_args['tax_query'] = [
                    [
                        'taxonomy' => 'case_category',
                        'field' => 'term_id',
                        'terms' => $term_ids,
                    ]
                ];
            }

            $related_q = new WP_Query($related_args);

            if ($related_q->have_posts()): ?>
                <section class="related-cases">
                    <div class="container">
                        <h2 class="related-cases__title"><?php esc_html_e('Related Projects', 'your-textdomain'); ?></h2>

                        <div class="related-cases__grid">
                            <?php while ($related_q->have_posts()):
                                $related_q->the_post(); ?>
                                <?php get_template_part('template-parts/case/card', null, ['post_id' => get_the_ID()]); ?>
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
