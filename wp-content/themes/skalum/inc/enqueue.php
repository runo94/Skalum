<?php
add_action('wp_enqueue_scripts', function () {
    $ver = wp_get_theme()->get('Version');
    wp_enqueue_style('skalum-main', get_template_directory_uri() . '/assets/css/main.css', [], $ver);
    wp_enqueue_script('skalum-main', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], $ver, true);
});

