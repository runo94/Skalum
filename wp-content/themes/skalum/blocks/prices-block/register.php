<?php
acf_register_block_type([
    'name' => 'skalum-prices-block',
    'title' => __('Prices Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cart',
    'render_template' => get_template_directory() . '/blocks/prices-block/prices-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/prices-block/assets';

        wp_enqueue_style(
            'skalum-prices-block',
            "$base/css/prices-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/prices-block/assets';

        $script = "$base/js/prices-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/prices-block/assets/js/prices-block.min.js')) {
            wp_enqueue_script('skalum-prices-block', $script, [], $ver, true);

            wp_localize_script('skalum-prices-block', 'SkalumPricesBlock', [
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
