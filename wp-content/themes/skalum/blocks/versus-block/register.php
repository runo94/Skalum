<?php
acf_register_block_type([
    'name' => 'skalum-versus-block',
    'title' => __('Versus Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/versus-block/versus-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/versus-block/assets';

        wp_enqueue_style(
            'skalum-versus-block',
            "$base/css/versus-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/versus-block/assets';

        $script = "$base/js/versus-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/versus-block/assets/js/versus-block.min.js')) {
            wp_enqueue_script('skalum-versus-block', $script, [], $ver, true);

            wp_localize_script('skalum-versus-block', 'SkalumVersusBlock', [
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
