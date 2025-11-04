<?php
acf_register_block_type([
    'name' => 'skalum-faq-block',
    'title' => __('FAQ Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'list-view',
    'render_template' => get_template_directory() . '/blocks/faq-block/faq-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/faq-block/assets';

        wp_enqueue_style(
            'skalum-faq-block',
            "$base/css/faq-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/faq-block/assets';

        $script = "$base/js/faq-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/faq-block/assets/js/faq-block.min.js')) {
            wp_enqueue_script('skalum-faq-block', $script, [], $ver, true);

            wp_localize_script('skalum-faq-block', 'SkalumTitleBlock', [
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
