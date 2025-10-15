<?php
$inc = [
    '/inc/setup.php',
    '/inc/enqueue.php',
    '/inc/acf-options.php',
    '/inc/helpers.php', 
    '/inc/ajax.php',
    '/inc/blocks.php',
    '/inc/fields/page-settings.php'
];

foreach ($inc as $rel) {
    $path = get_template_directory() . $rel;
    if ( file_exists($path) ) {
        require_once $path;
    } else {
        error_log('[Skalum] Missing include: ' . $path);
    }
}

add_filter('acf/settings/save_json', fn() => get_template_directory() . '/acf-json');
add_filter('acf/settings/load_json', function($paths){
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});

