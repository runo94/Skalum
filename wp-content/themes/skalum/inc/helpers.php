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

/**
 * Іконка зовнішнього посилання (share / open-in-new).
 *
 * Колір успадковується від тексту посилання через currentColor.
 *
 * @param string $class Додатковий клас для svg.
 * @return string Готова до виводу SVG-розмітка.
 */
function skalum_external_link_icon(string $class = ''): string {
    $classes = trim('skalum-icon-external ' . $class);

    return '<svg class="' . esc_attr($classes) . '" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
        . '<path fill-rule="evenodd" clip-rule="evenodd" d="M13.9494 2.41148C13.9171 2.33343 13.8693 2.26027 13.8061 2.19668C13.8052 2.19573 13.8043 2.19479 13.8033 2.19385C13.6828 2.07404 13.5167 2 13.3333 2H9.33333C8.96513 2 8.66667 2.29848 8.66667 2.66667C8.66667 3.03485 8.96513 3.33333 9.33333 3.33333H11.7239L6.86193 8.19527C6.60158 8.4556 6.60158 8.87773 6.86193 9.13807C7.12227 9.3984 7.5444 9.3984 7.80473 9.13807L12.6667 4.27614V6.66667C12.6667 7.03487 12.9651 7.33333 13.3333 7.33333C13.7015 7.33333 14 7.03487 14 6.66667V2.66713C14 2.66631 14 2.66549 14 2.66467C13.9997 2.57849 13.9829 2.49236 13.9494 2.41148ZM2 5.33333C2 3.49239 3.49239 2 5.33333 2H6C6.36819 2 6.66667 2.29848 6.66667 2.66667C6.66667 3.03485 6.36819 3.33333 6 3.33333H5.33333C4.22877 3.33333 3.33333 4.22877 3.33333 5.33333V10.6667C3.33333 11.7713 4.22877 12.6667 5.33333 12.6667H10.6667C11.7713 12.6667 12.6667 11.7713 12.6667 10.6667V10C12.6667 9.6318 12.9651 9.33333 13.3333 9.33333C13.7015 9.33333 14 9.6318 14 10V10.6667C14 12.5076 12.5076 14 10.6667 14H5.33333C3.49239 14 2 12.5076 2 10.6667V5.33333Z" fill="currentColor"/>'
        . '</svg>';
}
