<?php
acf_register_block_type([
    'name' => 'skalum-included-block',
    'title' => __('Included Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/included-block/included-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/included-block/assets';

        wp_enqueue_style(
            'skalum-included-block',
            "$base/css/included-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/included-block/assets';

        $script = "$base/js/included-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/included-block/assets/js/included-block.min.js')) {
            wp_enqueue_script('skalum-included-block', $script, [], $ver, true);
            wp_localize_script('skalum-included-block', 'SkalumIncludedBlock', [
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
