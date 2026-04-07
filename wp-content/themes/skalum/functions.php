<?php
$inc = [
    '/inc/setup.php',
    '/inc/enqueue.php',
    '/inc/acf-options.php',
    '/inc/helpers.php', 
    '/inc/ajax.php',
    '/inc/blocks.php',
    '/inc/fields/page-settings.php',
    '/inc/post-types/cases.php',
    '/inc/fields/full-range-pricing-prefill.php',
];

foreach ($inc as $rel) {
    $path = get_template_directory() . $rel;
    if ( file_exists($path) ) {
        require_once $path;
    } else {
        error_log('[Skalum] Missing include: ' . $path);
    }
}

add_filter('acf/settings/save_json', fn() => get_template_directory() . '/acf-json');
add_filter('acf/settings/load_json', function($paths){
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});

add_action('pre_get_posts', function ($q) {
  if (is_admin() || !$q->is_main_query()) return;

  if ($q->is_home()) {
    error_log('[BLOG] pre_get_posts hit: ' . print_r([
      'post_type' => $q->get('post_type'),
      'post_status' => $q->get('post_status'),
      'tax_query' => $q->get('tax_query'),
      'meta_query' => $q->get('meta_query'),
    ], true));
  }
});

add_action('wp_enqueue_scripts', function () {
    if ((!is_home() && !is_post_type_archive('post') && is_page_template('page-blog.php')) || (!is_home() && !is_post_type_archive('post') && is_page_template('page-blogs.php'))) {
        return;
    }

    wp_enqueue_script(
        'particles-js-lib',
        get_template_directory_uri() . '/assets/js/vendor/particles.min.js',
        [],
        '2.0.0',
        true
    );

    wp_enqueue_script(
        'blog-particles-init',
        get_template_directory_uri() . '/assets/js/blog-particles.js',
        ['particles-js-lib'],
        filemtime(get_template_directory() . '/assets/js/blog-particles.js'),
        true
    );

    wp_localize_script('blog-particles-init', 'skalumBlog', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ]);
});

