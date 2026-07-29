<?php
defined('ABSPATH') || exit;

/**
 * Blog "load more" — public, read-only pagination.
 *
 * No nonce: this serves the same published posts the archive already renders
 * to anonymous visitors, so a nonce would only add cache friction. Everything
 * that reaches a query is bounded and cast.
 */
add_action('wp_ajax_blog_more', 'blog_more');
add_action('wp_ajax_nopriv_blog_more', 'blog_more');

function blog_more() {
    $per_page = (int) get_option('posts_per_page') ?: 10;

    // Bound the page number: absint kills negatives/injection, the cap stops a
    // crawler from walking OFFSET into the millions and hammering the DB.
    $requested = isset($_GET['page']) ? absint(wp_unslash($_GET['page'])) : 1;
    $paged     = min($requested + 1, 500);

    $q = new WP_Query([
        'post_type'           => 'post',
        // Explicit: admin-ajax runs authenticated for logged-in editors, and
        // the WP_Query default would then expose drafts/private posts through
        // this otherwise-public endpoint.
        'post_status'         => 'publish',
        'posts_per_page'      => $per_page,
        'paged'               => $paged,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]);

    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            get_template_part('template-parts/blog/card');
        }
        wp_reset_postdata();
    }

    wp_die();
}
