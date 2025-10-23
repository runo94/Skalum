<?php
acf_register_block_type([
    'name' => 'skalum-team-block',
    'title' => __('Team Block', 'skalum'),
    'category' => 'skalum',
    'icon' => 'groups',
    'render_template' => get_template_directory() . '/blocks/team-block/team-block.php',

    'enqueue_assets' => function () {
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/team-block/assets';

        wp_enqueue_style(
            'skalum-team-block',
            "$base/css/team-block.css?v=$ver",
            [],
            $ver
        );
        $ver = wp_get_theme()->get('Version');
        $base = get_stylesheet_directory_uri() . '/blocks/team-block/assets';

        $script = "$base/js/team-block.min.js";
        if (file_exists(get_stylesheet_directory() . '/blocks/team-block/assets/js/team-block.min.js')) {
            wp_enqueue_script('skalum-team-block', $script, [], $ver, true);

            wp_localize_script('skalum-team-block', 'SkalumTitleBlock', [
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
