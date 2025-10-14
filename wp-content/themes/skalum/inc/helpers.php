<?php
function skalum_get_field($name, $post_id = false, $default = null) {
    if (function_exists('get_field')) {
        $v = get_field($name, $post_id);
        return $v !== null ? $v : $default;
    }
    return $default;
}

function skalum_e($str) {
    echo esc_html((string) $str);
}
function skalum_theme_version() {
    return wp_get_theme()->get('Version') ?: '1.0.0';
}
