<?php
acf_register_block_type([
    'name' => 'skalum-casual-block',
    'title' => __('Casual Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'block-default',
    'render_template' => get_template_directory() . '/blocks/casual-block/casual-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/casual-block/assets';

        wp_enqueue_style(
            'skalum-casual-block',
            "$base/css/casual-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/casual-block/assets';

        $script = "$base/js/casual-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/casual-block/assets/js/casual-block.min.js')) {
            wp_enqueue_script('skalum-casual-block', $script, [], $ver, true);

            wp_localize_script('skalum-casual-block', 'SkalumTitleBlock', [
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
