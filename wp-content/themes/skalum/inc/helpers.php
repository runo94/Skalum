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

/**
 * Чи рендериться блок для екрана редагування, а не для фронта.
 *
 * ACF рендерить блок двічі: під час завантаження екрана редагування (preload,
 * звичайний admin-запит) і далі через REST /wp/v2/block-renderer/ на кожну
 * зміну поля. В обох випадках фронтовий JS блоку не потрібен — слайдери,
 * particles і GSAP тільки гальмують прев'ю і переініціалізуються на кожен
 * ре-рендер. wp_doing_ajax() виключає власні AJAX-ендпоінти теми
 * (inc/ajax.php), які віддають фронтову розмітку через admin-ajax.
 */
function skalum_is_editor_render(): bool {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return true;
    }
    return is_admin() && !wp_doing_ajax();
}
