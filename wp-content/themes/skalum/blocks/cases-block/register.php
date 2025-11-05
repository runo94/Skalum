<?php
acf_register_block_type([
    'name' => 'skalum-cases-block',
    'title' => __('Cases Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'exerpt-view',
    'render_template' => get_template_directory() . '/blocks/cases-block/cases-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/cases-block/assets';

        wp_enqueue_style(
            'skalum-cases-block',
            "$base/css/cases-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/cases-block/assets';

        $script = "$base/js/cases-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/cases-block/assets/js/cases-block.min.js')) {
            wp_enqueue_script('skalum-cases-block', $script, [], $ver, true);

            wp_enqueue_style('swiper', 'https://unpkg.com/swiper@10/swiper-bundle.min.css', [], '10.0.0');
            wp_enqueue_script('swiper', 'https://unpkg.com/swiper@10/swiper-bundle.min.js', [], '10.0.0', true);

            // твій файл зі слайдером
            wp_localize_script('skalum-cases-block', 'SkalumTitleBlock', [
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
