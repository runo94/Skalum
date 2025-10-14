<?php
add_filter('block_categories_all', function($cats) {
  $cats[] = ['slug' => 'skalum', 'title' => __('Skalum Blocks', 'skalum')];
  return $cats;
}, 10, 1);

add_action('acf/init', function () {
  if (!function_exists('acf_register_block_type')) return;

  foreach (glob(get_template_directory() . '/blocks/*/register.php') as $reg) {
    require_once $reg;
  }

  foreach (glob(get_template_directory() . '/inc/fields/*.php') as $fg) {
    require_once $fg;
  }
}, 5);
