<?php
acf_register_block_type([
    'name' => 'skalum-result-card',
    'title' => __('Result Card Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'screenoptions',
    'render_template' => get_template_directory() . '/blocks/result-card/result-card.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/result-card/assets';

        wp_enqueue_style(
            'skalum-result-card',
            "$base/css/result-card.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/result-card/assets';

        $script = "$base/js/result-card.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/result-card/assets/js/result-card.min.js')) {
            wp_enqueue_script('skalum-result-card', $script, [], $ver, true);

            wp_localize_script('skalum-result-card', 'SkalumCardsBlock', [
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
