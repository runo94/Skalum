<?php
acf_register_block_type([
    'name' => 'skalum-phone-block',
    'title' => __('Phone Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/phone-block/phone-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/phone-block/assets';

        wp_enqueue_style(
            'skalum-phone-block',
            "$base/css/phone-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/phone-block/assets';

        $script = "$base/js/phone-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/phone-block/assets/js/phone-block.min.js')) {
            wp_enqueue_script('skalum-phone-block', $script, [], $ver, true);

            wp_localize_script('skalum-phone-block', 'SkalumPhoneBlock', [
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
