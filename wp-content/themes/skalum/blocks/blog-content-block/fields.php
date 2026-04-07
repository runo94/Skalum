<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_blog_content_block',
        'title' => 'Blog Content Block',
        'fields' => [
            [
                'key' => 'field_blog_content_toc_title',
                'label' => 'TOC Title',
                'name' => 'toc_title',
                'type' => 'text',
                'default_value' => 'Contents',
            ],
            [
                'key' => 'field_blog_content_show_toc',
                'label' => 'Show TOC',
                'name' => 'show_toc',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_blog_content_background_color',
                'label' => 'Background Color',
                'name' => 'background_color',
                'type' => 'select',
                'choices' => [
                    '' => 'Default',
                    'dark' => 'Dark',
                    'blue' => 'Blue',
                    'light_blue' => 'Light Blue',
                ],
                'default_value' => 'dark',
                'ui' => 1,
                'return_format' => 'value',
            ],
            [
                'key' => 'field_blog_content_rounded',
                'label' => 'Rounded',
                'name' => 'rounded',
                'type' => 'checkbox',
                'choices' => [
                    'top' => 'Top',
                    'bottom' => 'Bottom',
                ],
                'layout' => 'horizontal',
                'return_format' => 'value',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/blog-content-block',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
});