<?php
// in functions.php
add_action('acf/init', function () {
  if (!function_exists('acf_register_block_type')) return;

  acf_register_block_type([
    'name'            => 'skalum-timeline-block',
    'title'           => __('Timeline Block', 'skalum'),
    'description'     => __('Scrollable timeline with progress line', 'skalum'),
    'category'        => 'skalum',
    'icon'            => 'schedule', // або своя іконка
    'keywords'        => ['timeline', 'progress', 'steps'],
    'mode'            => 'preview',
    'supports'        => [
      'anchor'   => true,
      'align'    => ['wide', 'full'],
      'jsx'      => true,
    ],
    'render_template' => get_stylesheet_directory() . '/blocks/timeline-block/timeline-block.php',

    'enqueue_assets'  => function () {
      $ver  = wp_get_theme()->get('Version');
      $base = get_stylesheet_directory_uri() . '/blocks/timeline-block/assets';

      wp_enqueue_style(
        'skalum-timeline-block',
        "$base/css/timeline-block.css?v=$ver",
        [],
        $ver
      );

      wp_enqueue_script(
        'skalum-timeline-block',
        "$base/js/timeline-block.js?v=$ver",
        [],
        $ver,
        true
      );
    },
  ]);
});
