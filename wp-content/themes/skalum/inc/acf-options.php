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

// save_json/load_json навмисно не дублюються тут — вони зареєстровані один раз
// у functions.php. Другий однаковий шлях у load_json змушував ACF сканувати
// теку acf-json двічі на кожному запиті.

add_filter('acf/settings/show_admin', '__return_true');

