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

