<?php
/**
 * Archive Case template
 */

defined('ABSPATH') || exit;

get_header();

?>

<main id="primary" class="site-main archive-cases">
    <div class="container">

        <header class="archive-cases__header">
            <h1 class="archive-cases__title">
                <?php post_type_archive_title(); ?>
            </h1>

            <?php if (term_description()): ?>
                <div class="archive-cases__description">
                    <?php echo term_description(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if (have_posts()): ?>
            <div class="archive-cases__grid">
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php get_template_part('template-parts/case/card', null, ['post_id' => get_the_ID()]); ?>
                <?php endwhile; ?>
            </div>

            <div class="archive-cases__pagination">
                <?php the_posts_pagination([
                    'mid_size' => 1,
                    'prev_text' => '←',
                    'next_text' => '→',
                ]); ?>
            </div>

        <?php else: ?>
            <div class="archive-cases__empty">
                <p><?php esc_html_e('No cases found.', 'your-textdomain'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
wp_reset_postdata();
?>