<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'blog-content-block',
        'title'           => __('Blog Content Block', 'skalum'),
        'description'     => __('Blog content with sticky table of contents generated from H2 headings.', 'skalum'),
        'render_template' => get_template_directory() . '/blocks/blog-content-block/render.php',
        'category'        => 'formatting',
        'icon'            => 'list-view',
        'keywords'        => ['blog', 'content', 'toc', 'contents'],
        'mode'            => 'preview',
        'supports'        => [
            'align'  => ['wide', 'full'],
            'anchor' => true,
            'jsx'    => true,
        ],
        'enqueue_assets'  => function () {
            $block_url  = get_template_directory_uri() . '/blocks/blog-content-block/assets';
            $block_path = get_template_directory() . '/blocks/blog-content-block/assets';

            wp_enqueue_style(
                'blog-content-block-style',
                $block_url . '/css/style.css',
                [],
                file_exists($block_path . '/css/style.css') ? filemtime($block_path . '/css/style.css') : null
            );

            wp_enqueue_script(
                'blog-content-block-script',
                $block_url . '/js/script.js',
                [],
                file_exists($block_path . '/js/script.js') ? filemtime($block_path . '/js/script.js') : null,
                true
            );
        },
    ]);
});