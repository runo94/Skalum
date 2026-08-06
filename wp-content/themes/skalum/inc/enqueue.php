<?php
/**
 * Версія — час зміни файлу, а не версія теми: інакше після правок CSS/JS
 * браузери й кеш-плагін і далі віддають старий файл.
 */
function skalum_asset_version(string $rel): string
{
    $path = get_template_directory() . $rel;
    return file_exists($path) ? (string) filemtime($path) : (string) wp_get_theme()->get('Version');
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'skalum-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        skalum_asset_version('/assets/css/main.css')
    );
    wp_enqueue_script(
        'skalum-main',
        get_template_directory_uri() . '/assets/js/main.js',
        ['jquery'],
        skalum_asset_version('/assets/js/main.js'),
        true
    );

    // Подія form_submit для GTM — потрібна лише там, де є Ninja Forms.
    if (class_exists('Ninja_Forms')) {
        wp_enqueue_script(
            'skalum-form-tracking',
            get_template_directory_uri() . '/assets/js/form-tracking.js',
            ['jquery'],
            skalum_asset_version('/assets/js/form-tracking.js'),
            true
        );
    }
});

