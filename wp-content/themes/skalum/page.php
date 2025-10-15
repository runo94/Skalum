<?php
/**
 * Template: Page
 *
 * @package Skalum
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()): ?>
            <?php while (have_posts()):
                the_post(); ?>

                <section id="page-<?php the_ID(); ?>" <?php post_class('page'); ?>>
                    <header class="page__header">
                        <?php if (!get_field('hide_title')): ?>
                            <h1 class="page__title" itemprop="headline"><?php the_title(); ?></h1>
                        <?php endif; ?>

                        <?php if (has_post_thumbnail()): ?>
                            <figure class="page__thumb">
                                <?php the_post_thumbnail('large', [
                                    'loading' => 'eager',
                                    'decoding' => 'async',
                                    'itemprop' => 'image'
                                ]); ?>
                            </figure>
                        <?php endif; ?>
                    </header>

                    <div class="page__content entry-content" itemprop="articleBody">
                        <?php
                        the_content();
                        ?>
                    </div>

                    <footer class="page__footer">
                        <?php edit_post_link(esc_html__('Edit', 'skalum'), '<span class="edit-link">', '</span>'); ?>
                    </footer>
                </section>

                <?php
                // Коментарі: якщо увімкнено або вже є
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>

            <?php endwhile; ?>
        <?php else: ?>

            <section class="no-results not-found">
                <h1 class="page__title"><?php esc_html_e('Nothing found', 'skalum'); ?></h1>
                <p><?php esc_html_e('Sorry, the page you are looking for could not be found.', 'skalum'); ?></p>
                <?php get_search_form(); ?>
            </section>

        <?php endif; ?>
    </div>
</main>

<?php get_footer();
