<?php
acf_register_block_type([
    'name' => 'skalum-range-block',
    'title' => __('Range Price Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'image-flip-horizontal',
    'render_template' => get_template_directory() . '/blocks/range-block/range-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/range-block/assets';
        wp_enqueue_style(
            'skalum-range-block',
            "$base/css/range-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/range-block/assets';

        $script = "$base/js/range-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/range-block/assets/js/range-block.min.js')) {
            wp_enqueue_script('skalum-range-block', $script, [], $ver, true);
            wp_localize_script('skalum-range-block', 'SkalumRangeBlock', [
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
