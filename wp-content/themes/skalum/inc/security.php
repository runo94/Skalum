<?php
/**
 * Skalum security & hardening.
 *
 * 1. Fully disables the WordPress comment system for every user.
 * 2. Adds baseline protection against spam, brute-force and injection vectors.
 */

defined('ABSPATH') || exit;

/* -------------------------------------------------------------------------
 * 1. DISABLE COMMENTS (all users, front-end + admin)
 * ---------------------------------------------------------------------- */

/** Comments and pings are always reported as closed. */
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

/** Hide any comments that might already exist in the DB. */
add_filter('comments_array', '__return_empty_array', 20, 2);

/** Remove comment support from every post type. */
add_action('init', function () {
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
        }
        if (post_type_supports($post_type, 'trackbacks')) {
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}, 100);

/** Block direct POSTs to wp-comments-post.php (bots hit it even with no form). */
add_action('pre_comment_on_post', function () {
    wp_die(
        esc_html__('Comments are disabled on this site.', 'skalum'),
        esc_html__('Comments closed', 'skalum'),
        ['response' => 403]
    );
});

/** Refuse comments programmatically as a final safety net. */
add_filter('pre_comment_approved', function () {
    return new WP_Error('comments_closed', __('Comments are disabled on this site.', 'skalum'));
}, 20);

/* --- Admin clean-up: remove all comment UI --- */

/** Redirect the edit-comments screen away. */
add_action('admin_init', function () {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
});

/** Remove the "Discussion"/comment meta boxes from every post type. */
add_action('add_meta_boxes', function () {
    foreach (get_post_types() as $post_type) {
        remove_meta_box('commentsdiv', $post_type, 'normal');
        remove_meta_box('commentstatusdiv', $post_type, 'normal');
        remove_meta_box('trackbacksdiv', $post_type, 'normal');
    }
}, 100);

/** Remove the admin menu item. */
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

/** Remove the "Comments" node from the admin toolbar. */
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('comments');
});

/** Remove the dashboard "Recent Comments" widget. */
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
});

/** Drop comment/comment-reply scripts from the front-end. */
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_script('comment-reply');
}, 100);

/** Remove comment feed links from <head>. */
add_filter('feed_links_show_comments_feed', '__return_false');

/* -------------------------------------------------------------------------
 * 2. SPAM / INJECTION / BRUTE-FORCE HARDENING
 * ---------------------------------------------------------------------- */

/** Kill XML-RPC entirely (pingback DDoS + password brute-force vector). */
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_xmlrpc_server_class', '__return_false');

/** Remove the X-Pingback header and RSD/WLW links. */
add_action('template_redirect', function () {
    header_remove('X-Pingback');
});
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

/** Hide the WordPress version (fingerprinting). */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

/** Strip the ?ver= WP version from enqueued assets so core version leaks less. */
add_filter('style_loader_src', 'skalum_strip_core_ver', 20);
add_filter('script_loader_src', 'skalum_strip_core_ver', 20);
function skalum_strip_core_ver($src) {
    if ($src && strpos($src, 'ver=' . get_bloginfo('version')) !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

/** Block author enumeration via ?author=N on the front-end. */
add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }
    if (isset($_GET['author']) && is_numeric($_GET['author'])) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

/** Disable the REST users endpoint for logged-out visitors (enumeration). */
add_filter('rest_endpoints', function ($endpoints) {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users']);
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});

/** Send baseline security response headers. */
add_action('send_headers', function () {
    if (is_admin()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-XSS-Protection: 1; mode=block');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
});
