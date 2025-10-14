<?php
acf_register_block_type([
    'name' => 'skalum-sky-block',
    'title' => __('Sky Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/sky-block/sky-block.php',
    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');

        $css = get_template_directory() . '/blocks/sky-block/assets/css/sky-block.css';
        if (file_exists($css)) {
            wp_enqueue_style(
                'skalum-sky-block',
                get_template_directory_uri() . '/blocks/sky-block/assets/css/sky-block.css',
                [],
                $ver
            );
        }

        $jsMin = get_template_directory() . '/blocks/sky-block/assets/js/sky-block.min.js';
        if (file_exists($jsMin)) {
            wp_enqueue_script(
                'skalum-sky-block',
                get_template_directory_uri() . '/blocks/sky-block/assets/js/sky-block.min.js',
                [],
                $ver,
                true
            );
        }
    },
    'supports' => [
        'align' => ['wide', 'full'],
        'anchor' => true,
        'multiple' => true,
    ],
]);
