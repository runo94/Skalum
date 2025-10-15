<?php
acf_register_block_type([
    'name'            => 'skalum-sky-block',
    'title'           => __('Sky Block', 'skalum'),
    'category'        => 'skalum',
    'icon'            => 'cloud',
    'render_template' => get_template_directory() . '/blocks/sky-block/sky-block.php',

    'enqueue_assets'  => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/sky-block/assets';

        wp_enqueue_style(
            'skalum-sky-block',
            "$base/css/sky-block.css?v=$ver",
            [],
            $ver
        );

        $particles = "$base/js/particles.min.js";
        $script = "$base/js/sky-block.min.js";

        if (file_exists(get_stylesheet_directory() . '/blocks/sky-block/assets/js/particles.min.js')) {
            wp_enqueue_script('particles-js', $particles, [], $ver, true);
        }

        if (file_exists(get_stylesheet_directory() . '/blocks/sky-block/assets/js/sky-block.min.js')) {
            wp_enqueue_script('skalum-sky-block', $script, ['particles-js'], $ver, true);
        }
    },

    'supports' => [
        'align'    => ['wide', 'full'],
        'anchor'   => true,
        'multiple' => true,
    ],
]);
