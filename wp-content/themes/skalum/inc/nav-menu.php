<?php
/**
 * Навігація хедера: дерево пунктів меню + рендер десктопного мега-меню
 * і мобільного меню з акордеоном.
 *
 * Структура в Appearance → Menus:
 *   Services                        1 рівень — тригер панелі
 *     ├ SEO & Visibility            2 рівень — заголовок колонки
 *     │   ├ AI Optimisation         3 рівень — пункт з іконкою
 *     │   └ SEO Audit
 *     └ Conversion
 *         └ ...
 * Пункти 2-го рівня без дітей збираються в одну безіменну колонку — так
 * звичайний однорівневий дропдаун теж працює тією ж розміткою.
 */

/** ACF-поле пункту меню (нічого не ламається, якщо ACF вимкнено). */
function skalum_menu_field(string $name, int $item_id)
{
    return function_exists('get_field') ? get_field($name, $item_id) : null;
}

/**
 * Дерево пунктів меню для локації. Кешується в межах запиту:
 * хедер і мобільне меню використовують один і той самий результат.
 */
function skalum_nav_tree(string $location): array
{
    static $cache = [];
    if (isset($cache[$location])) {
        return $cache[$location];
    }

    $locations = get_nav_menu_locations();
    $menu = !empty($locations[$location]) ? wp_get_nav_menu_object($locations[$location]) : null;
    if (!$menu) {
        return $cache[$location] = [];
    }

    $items = wp_get_nav_menu_items($menu->term_id, ['update_post_term_cache' => false]);
    if (empty($items)) {
        return $cache[$location] = [];
    }

    // Додає current-menu-item / current-menu-ancestor — wp_nav_menu робить це сам,
    // а ми рендеримо вручну, тому викликаємо явно.
    if (function_exists('_wp_menu_item_classes_by_context')) {
        _wp_menu_item_classes_by_context($items);
    }

    $by_parent = [];
    foreach ($items as $item) {
        $by_parent[(int) $item->menu_item_parent][] = $item;
    }

    return $cache[$location] = skalum_nav_branch($by_parent, 0);
}

/** Рекурсивно збирає гілку дерева. */
function skalum_nav_branch(array $by_parent, int $parent): array
{
    $branch = [];

    foreach ($by_parent[$parent] ?? [] as $item) {
        $url = (string) $item->url;

        $branch[] = [
            'id' => (int) $item->ID,
            'title' => $item->title,
            'url' => $url,
            'is_link' => $url !== '' && $url !== '#',
            'target' => $item->target,
            'rel' => $item->xfn,
            'description' => $item->description,
            'classes' => array_filter((array) $item->classes),
            'icon' => skalum_menu_field('menu_item_icon', (int) $item->ID),
            'subtitle' => (string) (skalum_menu_field('menu_item_subtitle', (int) $item->ID) ?? ''),
            'children' => skalum_nav_branch($by_parent, (int) $item->ID),
        ];
    }

    return $branch;
}

/**
 * Розкладає дітей пункту 1-го рівня по колонках панелі.
 * Пункт з дітьми = колонка із заголовком, пункти без дітей злипаються
 * у спільну безіменну колонку.
 */
function skalum_nav_columns(array $children): array
{
    $columns = [];
    $loose = null;

    foreach ($children as $child) {
        if (!empty($child['children'])) {
            $columns[] = [
                'heading' => $child['title'],
                'url' => $child['is_link'] ? $child['url'] : '',
                'items' => $child['children'],
            ];
            $loose = null;
            continue;
        }

        if ($loose === null) {
            $columns[] = ['heading' => '', 'url' => '', 'items' => []];
            $loose = count($columns) - 1;
        }
        $columns[$loose]['items'][] = $child;
    }

    return $columns;
}

/**
 * Чи має пункт відкривати панель. Будь-яка вкладеність = панель, щоб пункти
 * 2-го рівня не зникали з десктопного меню.
 */
function skalum_nav_has_panel(array $node): bool
{
    return !empty($node['children']);
}

/** Іконка «за замовчуванням» — та сама зірочка, що в макеті. */
function skalum_menu_icon_default(): string
{
    return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">'
        . '<path d="M12 3.2l1.35 3.63a5 5 0 0 0 2.96 2.96L19.94 11l-3.63 1.35a5 5 0 0 0-2.96 2.96L12 18.94l-1.35-3.63a5 5 0 0 0-2.96-2.96L4.06 11l3.63-1.35a5 5 0 0 0 2.96-2.96L12 3.2z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>'
        . '<path d="M18.6 16.4l.5 1.35 1.35.5-1.35.5-.5 1.35-.5-1.35-1.35-.5 1.35-.5.5-1.35z" fill="currentColor" opacity=".75"/>'
        . '</svg>';
}

/** Вміст SVG-файлу з медіатеки, щоб іконка фарбувалась через currentColor. */
function skalum_inline_svg(int $attachment_id): string
{
    static $cache = [];
    if (isset($cache[$attachment_id])) {
        return $cache[$attachment_id];
    }

    $path = get_attached_file($attachment_id);
    $svg = ($path && is_readable($path)) ? (string) file_get_contents($path) : '';

    // Лишаємо тільки корінь <svg>…</svg>, без xml-пролога, doctype і скриптів.
    if ($svg && preg_match('#<svg\b.*</svg>#is', $svg, $m)) {
        $svg = preg_replace('#<script\b.*?</script>#is', '', $m[0]);
        $svg = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\')#i', '', $svg);
    } else {
        $svg = '';
    }

    return $cache[$attachment_id] = (string) $svg;
}

/** Розмітка іконки пункту меню. */
function skalum_menu_icon_markup($icon): string
{
    $id = 0;
    $url = '';
    $alt = '';

    if (is_array($icon)) {
        $id = (int) ($icon['ID'] ?? 0);
        $url = (string) ($icon['url'] ?? '');
        $alt = (string) ($icon['alt'] ?? '');
    } elseif (is_numeric($icon)) {
        $id = (int) $icon;
        $url = (string) wp_get_attachment_url($id);
    } elseif (is_string($icon) && $icon !== '') {
        $url = $icon;
    }

    if ($id && get_post_mime_type($id) === 'image/svg+xml') {
        $inline = skalum_inline_svg($id);
        if ($inline !== '') {
            return $inline;
        }
    }

    if ($url === '') {
        return skalum_menu_icon_default();
    }

    return sprintf(
        '<img src="%s" alt="%s" width="20" height="20" loading="lazy" decoding="async" />',
        esc_url($url),
        esc_attr($alt)
    );
}

/** Атрибути target/rel для посилання пункту. */
function skalum_menu_link_attrs(array $node): string
{
    $attrs = '';

    if (!empty($node['target'])) {
        $attrs .= ' target="' . esc_attr($node['target']) . '"';
        if (empty($node['rel'])) {
            $attrs .= ' rel="noopener"';
        }
    }
    if (!empty($node['rel'])) {
        $attrs .= ' rel="' . esc_attr($node['rel']) . '"';
    }

    return $attrs;
}

/** Один пункт усередині панелі: іконка + назва (+ підпис). */
function skalum_render_panel_item(array $node): void
{
    $tag = $node['is_link'] ? 'a' : 'span';
    $href = $node['is_link'] ? ' href="' . esc_url($node['url']) . '"' : '';
    $current = array_intersect($node['classes'], ['current-menu-item', 'current_page_item']) ? ' is-current' : '';
    ?>
    <li class="mega-menu__item">
        <<?php echo $tag; ?> class="mega-menu__link<?php echo $current; ?>"<?php echo $href . skalum_menu_link_attrs($node); ?>>
            <span class="mega-menu__icon" aria-hidden="true"><?php echo skalum_menu_icon_markup($node['icon']); ?></span>
            <span class="mega-menu__text">
                <span class="mega-menu__title"><?php echo esc_html($node['title']); ?></span>
                <?php if ($node['subtitle'] !== ''): ?>
                    <span class="mega-menu__desc"><?php echo esc_html($node['subtitle']); ?></span>
                <?php endif; ?>
            </span>
        </<?php echo $tag; ?>>
    </li>
    <?php
}

/** Панель мега-меню (спільна для десктопу). */
function skalum_render_mega_panel(array $node, string $panel_id): void
{
    $columns = skalum_nav_columns($node['children']);
    if (!$columns) {
        return;
    }

    // Одна колонка = звичайний дропдаун під пунктом, а не панель на всю ширину.
    $classes = 'mega-menu' . (count($columns) === 1 ? ' mega-menu--single' : '');
    ?>
    <div class="<?php echo esc_attr($classes); ?>" id="<?php echo esc_attr($panel_id); ?>" data-mega-panel>
        <div class="mega-menu__inner">
            <?php foreach ($columns as $column): ?>
                <div class="mega-menu__col">
                    <?php if ($column['heading'] !== ''): ?>
                        <?php if ($column['url'] !== ''): ?>
                            <a class="mega-menu__heading" href="<?php echo esc_url($column['url']); ?>">
                                <?php echo esc_html($column['heading']); ?>
                            </a>
                        <?php else: ?>
                            <p class="mega-menu__heading"><?php echo esc_html($column['heading']); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <ul class="mega-menu__list">
                        <?php foreach ($column['items'] as $item) {
                            skalum_render_panel_item($item);
                        } ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/** Іконка-шеврон. */
function skalum_nav_caret(): string
{
    return '<svg class="nav-caret" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">'
        . '<path d="M6 9.5l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
        . '</svg>';
}

/** Десктопне меню в пілюлі хедера. */
function skalum_render_desktop_nav(string $location = 'header'): void
{
    $tree = skalum_nav_tree($location);
    if (!$tree) {
        return;
    }
    ?>
    <ul class="nav-pill__list" data-mega-nav>
        <?php foreach ($tree as $i => $node): ?>
            <?php
            $has_panel = skalum_nav_has_panel($node);
            $panel_id = 'skl-mega-' . ($i + 1);
            $classes = ['nav-pill__item'];
            $classes = array_merge($classes, $node['classes']);
            if ($has_panel) {
                $classes[] = 'nav-pill__item--has-panel';
            }
            ?>
            <li class="<?php echo esc_attr(implode(' ', array_unique($classes))); ?>" <?php echo $has_panel ? 'data-mega-item' : ''; ?>>
                <?php if ($has_panel && !$node['is_link']): ?>
                    <button type="button" class="nav-pill__link nav-pill__link--trigger" aria-expanded="false"
                        aria-controls="<?php echo esc_attr($panel_id); ?>" data-mega-toggle>
                        <span><?php echo esc_html($node['title']); ?></span>
                        <?php echo skalum_nav_caret(); ?>
                    </button>
                <?php elseif ($has_panel): ?>
                    <span class="nav-pill__group">
                        <a class="nav-pill__link"
                            href="<?php echo esc_url($node['url']); ?>"<?php echo skalum_menu_link_attrs($node); ?>>
                            <span><?php echo esc_html($node['title']); ?></span>
                        </a>
                        <button type="button" class="nav-pill__caret" aria-expanded="false"
                            aria-controls="<?php echo esc_attr($panel_id); ?>" data-mega-toggle
                            aria-label="<?php echo esc_attr(sprintf(__('Show submenu of %s', 'skalum'), $node['title'])); ?>">
                            <?php echo skalum_nav_caret(); ?>
                        </button>
                    </span>
                <?php else: ?>
                    <a class="nav-pill__link"
                        href="<?php echo esc_url($node['url']); ?>"<?php echo skalum_menu_link_attrs($node); ?>>
                        <span><?php echo esc_html($node['title']); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($has_panel) {
                    skalum_render_mega_panel($node, $panel_id);
                } ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}

/** Мобільне меню: ті самі дані, але акордеоном. */
function skalum_render_mobile_nav(string $location = 'header'): void
{
    $tree = skalum_nav_tree($location);
    if (!$tree) {
        return;
    }
    ?>
    <ul class="mobile-nav__list">
        <?php foreach ($tree as $i => $node): ?>
            <?php
            $has_children = !empty($node['children']);
            $sub_id = 'skl-mobile-sub-' . ($i + 1);
            $classes = array_merge(['mobile-nav__item'], $node['classes']);
            if ($has_children) {
                $classes[] = 'mobile-nav__item--has-children';
            }
            ?>
            <li class="<?php echo esc_attr(implode(' ', array_unique($classes))); ?>">
                <div class="mobile-nav__row">
                    <?php if ($has_children && !$node['is_link']): ?>
                        <?php // Пункт без власного URL — уся стрічка розкриває підменю ?>
                        <button type="button" class="mobile-nav__toggle mobile-nav__toggle--row" aria-expanded="false"
                            aria-controls="<?php echo esc_attr($sub_id); ?>" data-submenu-toggle>
                            <span class="mobile-nav__link"><?php echo esc_html($node['title']); ?></span>
                            <?php echo skalum_nav_caret(); ?>
                        </button>
                    <?php else: ?>
                        <?php if ($node['is_link']): ?>
                            <a class="mobile-nav__link"
                                href="<?php echo esc_url($node['url']); ?>"<?php echo skalum_menu_link_attrs($node); ?>>
                                <?php echo esc_html($node['title']); ?>
                            </a>
                        <?php else: ?>
                            <span class="mobile-nav__link"><?php echo esc_html($node['title']); ?></span>
                        <?php endif; ?>

                        <?php if ($has_children): ?>
                            <button type="button" class="mobile-nav__toggle" aria-expanded="false"
                                aria-controls="<?php echo esc_attr($sub_id); ?>"
                                aria-label="<?php echo esc_attr(sprintf(__('Show submenu of %s', 'skalum'), $node['title'])); ?>"
                                data-submenu-toggle>
                                <?php echo skalum_nav_caret(); ?>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if ($has_children): ?>
                    <div class="mobile-nav__sub" id="<?php echo esc_attr($sub_id); ?>" data-submenu>
                        <div class="mobile-nav__sub-inner">
                            <?php foreach (skalum_nav_columns($node['children']) as $column): ?>
                                <div class="mobile-nav__group">
                                    <?php if ($column['heading'] !== ''): ?>
                                        <p class="mobile-nav__heading"><?php echo esc_html($column['heading']); ?></p>
                                    <?php endif; ?>
                                    <ul class="mobile-nav__sublist">
                                        <?php foreach ($column['items'] as $item): ?>
                                            <li>
                                                <?php $tag = $item['is_link'] ? 'a' : 'span'; ?>
                                                <<?php echo $tag; ?> class="mobile-nav__sublink"<?php echo $item['is_link'] ? ' href="' . esc_url($item['url']) . '"' : ''; ?><?php echo skalum_menu_link_attrs($item); ?>>
                                                    <span class="mobile-nav__icon" aria-hidden="true"><?php echo skalum_menu_icon_markup($item['icon']); ?></span>
                                                    <span class="mobile-nav__text">
                                                        <span class="mobile-nav__subtitle"><?php echo esc_html($item['title']); ?></span>
                                                        <?php if ($item['subtitle'] !== ''): ?>
                                                            <span class="mobile-nav__desc"><?php echo esc_html($item['subtitle']); ?></span>
                                                        <?php endif; ?>
                                                    </span>
                                                </<?php echo $tag; ?>>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}
