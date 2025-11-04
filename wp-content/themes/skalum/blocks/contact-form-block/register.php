<?php
acf_register_block_type([
    'name' => 'skalum-contact-form-block',
    'title' => __('Contact Form Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'editor-help',
    'render_template' => get_template_directory() . '/blocks/contact-form-block/contact-form-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/contact-form-block/assets';

        wp_enqueue_style(
            'skalum-contact-form-block',
            "$base/css/contact-form-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/contact-form-block/assets';

        $script = "$base/js/contact-form-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/contact-form-block/assets/js/contact-form-block.min.js')) {
            wp_enqueue_script('skalum-contact-form-block', $script, [], $ver, true);

            wp_localize_script('skalum-contact-form-block', 'SkalumContactFormBlock', [
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
