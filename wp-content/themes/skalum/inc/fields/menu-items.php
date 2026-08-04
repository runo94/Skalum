<?php
/**
 * Поля для пунктів меню (Appearance → Menus).
 *
 * Мега-меню будується з ієрархії самого меню:
 *   Services (1 рівень)  →  SEO & Visibility (2 рівень = заголовок колонки)
 *                            →  AI Optimisation (3 рівень = пункт з іконкою)
 * Тому окремих полів під колонки не треба — лише іконка й підпис для пунктів
 * усередині панелі.
 */
if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group([
        'key' => 'group_skalum_menu_item',
        'title' => 'Menu item',
        'fields' => [
            [
                'key' => 'field_skalum_menu_icon',
                'label' => 'Icon',
                'name' => 'menu_item_icon',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'mime_types' => 'svg,png,webp',
                'instructions' => 'Іконка пункту в мега-меню. SVG вставляється інлайном і фарбується через CSS.',
            ],
            [
                'key' => 'field_skalum_menu_subtitle',
                'label' => 'Subtitle',
                'name' => 'menu_item_subtitle',
                'type' => 'text',
                'instructions' => 'Другий рядок під назвою пункту (необов’язково).',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'nav_menu_item',
                    'operator' => '==',
                    'value' => 'all',
                ],
            ],
        ],
        'style' => 'seamless',
        'label_placement' => 'top',
        'active' => true,
    ]);

}
