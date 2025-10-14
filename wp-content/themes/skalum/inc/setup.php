<?php
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    load_theme_textdomain('skalum', get_template_directory() . '/languages');

    register_nav_menus([
        'header' => __('Header Menu', 'skalum'),
        'footer' => __('Footer Menu', 'skalum'),
    ]);
});
