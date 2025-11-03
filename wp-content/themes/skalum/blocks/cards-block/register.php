<?php
acf_register_block_type([
    'name' => 'skalum-cards-block',
    'title' => __('Cards Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'screenoptions',
    'render_template' => get_template_directory() . '/blocks/cards-block/cards-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/cards-block/assets';

        wp_enqueue_style(
            'skalum-cards-block',
            "$base/css/cards-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/cards-block/assets';

        $script = "$base/js/cards-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/cards-block/assets/js/cards-block.min.js')) {
            wp_enqueue_script('skalum-cards-block', $script, [], $ver, true);

            wp_localize_script('skalum-cards-block', 'SkalumCardsBlock', [
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
