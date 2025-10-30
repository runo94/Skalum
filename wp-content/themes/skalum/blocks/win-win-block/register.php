<?php
acf_register_block_type([
    'name' => 'skalum-win-win-block',
    'title' => __('Win-win Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/win-win-block/win-win-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/win-win-block/assets';

        wp_enqueue_style(
            'skalum-win-win-block',
            "$base/css/win-win-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/win-win-block/assets';

        $script = "$base/js/win-win-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/win-win-block/assets/js/win-win-block.min.js')) {
            wp_enqueue_script('skalum-win-win-block', $script, [], $ver, true);

            wp_localize_script('skalum-win-win-block', 'SkalumTitleBlock', [
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
