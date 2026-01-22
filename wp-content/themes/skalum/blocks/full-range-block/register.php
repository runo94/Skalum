<?php
acf_register_block_type([
    'name' => 'skalum-full-range-block',
    'title' => __('Full Range Price Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'image-flip-horizontal',
    'render_template' => get_template_directory() . '/blocks/full-range-block/full-range-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/full-range-block/assets';
        wp_enqueue_style(
            'skalum-full-range-block',
            "$base/css/full-range-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/full-range-block/assets';

        $script = "$base/js/full-range-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/full-range-block/assets/js/full-range-block.min.js')) {
            wp_enqueue_script('skalum-full-range-block', $script, [], $ver, true);
            wp_localize_script('skalum-full-range-block', 'SkalumFullRangeBlock', [
                'imgBase' => $base . '/images/',
            ]);
        }
    },

    'supports' => [
        'align' => ['wide', 'full'],
        'anchor' => true,
        'multiple' => true,
    ],
]);
