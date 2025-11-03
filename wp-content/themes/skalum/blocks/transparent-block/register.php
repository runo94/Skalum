<?php
acf_register_block_type([
    'name' => 'skalum-transparent-block',
    'title' => __('Transparent Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'visibility',
    'render_template' => get_template_directory() . '/blocks/transparent-block/transparent-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/transparent-block/assets';

        wp_enqueue_style(
            'skalum-transparent-block',
            "$base/css/transparent-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/transparent-block/assets';

        $particles = "$base/js/particles.min.js";
        $script = "$base/js/transparent-block.min.js";

        if (file_exists(get_stylesheet_directory() . '/blocks/transparent-block/assets/js/particles.min.js')) {
            wp_enqueue_script('particles-js', $particles, [], $ver, true);
        }

        if (file_exists(get_stylesheet_directory() . '/blocks/transparent-block/assets/js/transparent-block.min.js')) {
            wp_enqueue_script('skalum-transparent-block', $script, ['particles-js'], $ver, true);
        }
    },

    'supports' => [
        'align' => ['wide', 'full'],
        'anchor' => true,
        'multiple' => true,
    ],
]);
