<?php
acf_register_block_type([
    'name' => 'skalum-title-block',
    'title' => __('Title Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/title-block/title-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/title-block/assets';

        wp_enqueue_style(
            'skalum-title-block',
            "$base/css/title-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/title-block/assets';

        $script = "$base/js/title-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/title-block/assets/js/title-block.min.js')) {
            wp_enqueue_script('skalum-title-block', $script, [], $ver, true);

            wp_localize_script('skalum-title-block', 'SkalumTitleBlock', [
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
