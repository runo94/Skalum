<?php
acf_register_block_type([
    'name' => 'skalum-animation-block',
    'title' => __('Animated Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/animation-block/animation-block.php',
    'supports' => [
        'align' => ['wide', 'full'],
        'anchor' => true,
        'multiple' => true,
        'mode' => true,
    ],

    'enqueue_assets' => function () {
        $theme_uri = get_stylesheet_directory_uri();
        $theme_dir = get_stylesheet_directory();
        $base_uri = $theme_uri . '/blocks/animation-block/assets';
        $base_dir = $theme_dir . '/blocks/animation-block/assets';

        $css_rel = '/css/animation-block.css';
        $css_uri = $base_uri . $css_rel;
        $css_path = $base_dir . $css_rel;
        $css_ver = file_exists($css_path) ? filemtime($css_path) : wp_get_theme()->get('Version');

        wp_enqueue_style(
            'skalum-animation-block',
            $css_uri,
            [],
            $css_ver
        );

        wp_enqueue_script(
            'gsap',
            'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
            [],
            '3.12.5',
            true
        );

        $js_rel_min = '/js/animation-block.min.js';
        $js_rel_src = '/js/animation-block.js';
        $js_path_min = $base_dir . $js_rel_min;
        $js_path_src = $base_dir . $js_rel_src;

        if (file_exists($js_path_min)) {
            $js_uri = $base_uri . $js_rel_min;
            $js_ver = filemtime($js_path_min);
        } elseif (file_exists($js_path_src)) {
            $js_uri = $base_uri . $js_rel_src;
            $js_ver = filemtime($js_path_src);
        } else {
            return;
        }

        wp_register_script(
            'skalum-animation-block',
            $js_uri,
            ['gsap'],
            $js_ver,
            true
        );

        wp_localize_script('skalum-animation-block', 'SkalumAnimatedBlock', [
            'imgBase' => $base_uri . '/images/',
        ]);

        wp_enqueue_script('skalum-animation-block');
    },
]);
