<?php
add_action('wp_enqueue_scripts', function () {
    $ver = wp_get_theme()->get('Version');
    wp_enqueue_style('skalum-main', get_template_directory_uri() . '/assets/css/main.css', [], $ver);
    wp_enqueue_script('skalum-main', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], $ver, true);
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('skalum-admin', get_template_directory_uri() . '/assets/css/admin.css', [], '1.0');
    wp_enqueue_script('skalum-admin', get_template_directory_uri() . '/assets/js/admin.js', ['jquery'], '1.0', true);
});
