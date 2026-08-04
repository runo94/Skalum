<?php
acf_register_block_type([
    'name' => 'skalum-header-case-block',
    'title' => __('Header case Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'cloud',
    'render_template' => get_template_directory() . '/blocks/header-case-block/header-case-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/header-case-block/assets';

        wp_enqueue_style(
            'skalum-header-case-block',
            "$base/css/header-case-block.css?v=$ver",
            [],
            $ver
        );
        // Фронтовий JS не потрібен у редакторі — див. skalum_is_editor_render().
        if (skalum_is_editor_render()) {
            return;
        }

        $script = "$base/js/header-case-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/header-case-block/assets/js/header-case-block.min.js')) {
            wp_enqueue_script('skalum-header-case-block', $script, [], $ver, true);

            wp_localize_script('skalum-header-case-block', 'SkalumTitleBlock', [
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
