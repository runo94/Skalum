<?php
acf_register_block_type([
    'name'            => 'skalum-testimonials-block',
    'title'           => __('Testimonials Carousel Block', 'skalum'),
    'category'        => 'skalum',
    'icon'            => 'cloud',
    'render_template' => get_template_directory() . '/blocks/testimonials-block/testimonials-block.php',

    'enqueue_assets'  => function () {
        $ver  = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/testimonials-block/assets';

        // 1. Slick CSS
        wp_enqueue_style(
            'slick-css',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
            [],
            '1.8.1'
        );

        // 2. Твій CSS
        wp_enqueue_style(
            'skalum-testimonials-block-css',
            "$base/css/testimonials-block.css?v=$ver",
            [],
            $ver
        );

        // Фронтовий JS не потрібен у редакторі — див. skalum_is_editor_render().
        // Slick інакше переініціалізовує карусель на кожен ре-рендер прев'ю.
        if (skalum_is_editor_render()) {
            return;
        }

        // 3. jQuery — з WordPress, в футері
        wp_enqueue_script('jquery');

        // 4. Slick JS — залежить від jQuery
        wp_enqueue_script(
            'slick-js',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
            ['jquery'],
            '1.8.1',
            true
        );

        // 5. Твій JS — з перевіркою jQuery
        $js_path = get_stylesheet_directory() . '/blocks/testimonials-block/assets/js/testimonials-block.min.js';
        if (file_exists($js_path)) {
            wp_enqueue_script(
                'skalum-testimonials-block',
                "$base/js/testimonials-block.min.js",
                ['jquery', 'slick-js'],
                $ver,
                true
            );

            wp_localize_script('skalum-testimonials-block', 'SkalumTestimonials', [
                'imgBase' => $base . '/images/',
            ]);
        }
    },

    'supports' => [
        'align'    => ['wide', 'full'],
        'anchor'   => true,
        'multiple' => true,
    ],
]);