<?php
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Theme Settings',
        'menu_title' => 'Skalum Settings',
        'menu_slug'  => 'skalum-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
        'position'   => 58,
        'icon_url'   => 'dashicons-admin-generic',
    ]);
}

add_filter('acf/settings/save_json', fn() => get_template_directory() . '/acf-json');
add_filter('acf/settings/load_json', function($paths){
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});

add_filter('acf/settings/show_admin', '__return_true');

