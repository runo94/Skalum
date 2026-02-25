<?php
acf_register_block_type([
    'name' => 'skalum-hero-expert-block',
    'title' => __('Hero Expert Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/hero-expert-block/hero-expert-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/hero-expert-block/assets';

        wp_enqueue_style(
            'skalum-hero-expert-block',
            "$base/css/hero-expert-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/hero-expert-block/assets';

        $script = "$base/js/hero-expert-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/hero-expert-block/assets/js/hero-expert-block.min.js')) {
            wp_enqueue_script('skalum-hero-expert-block', $script, [], $ver, true);

            wp_localize_script('skalum-hero-expert-block', 'SkalumHeroExpertBlock', [
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
