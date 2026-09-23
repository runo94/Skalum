<?php
/**
 * Product + Review JSON-LD — тільки для головної сторінки англійської версії.
 *
 * Google віддає rich-сніпет із зірками лише тоді, коли відгуки прикріплені
 * до одного itemReviewed разом з aggregateRating. Окремі Review-ноди, які
 * виводять блоки тестимоніалів, для цього не годяться, тому на /en/ вони
 * замінюються цією схемою (див. testimonials-block.php та
 * testimonials-global-block.php).
 *
 * Відгуки збираються з фактично відрендерених блоків, тому схема завжди
 * відповідає тому, що бачить користувач на сторінці.
 */

/**
 * Статична частина оффера. Правити тут або через фільтр
 * `skalum_en_home_product_schema` (отримує готовий масив схеми).
 *
 * 'image' — шлях відносно теки uploads.
 */
const SKALUM_EN_HOME_PRODUCT = [
    'name'        => 'Shopify SEO & Growth Agency Package',
    'description' => 'Shopify SEO, CRO, development and ads growth package for e-commerce brands. Technical audits, conversion optimization, and paid growth managed by a dedicated Shopify agency team.',
    'sku'         => 'SKALUM-GROWTH-PACKAGE',
    'brand'       => 'Skalum',
    'price'       => '1350',
    'currency'    => 'EUR',
    'image'       => '2025/12/logo-linkedin.png',
];

/**
 * Чи це фронтовий запит головної сторінки EN.
 *
 * Polylang робить переклад front page повноцінною головною, тому
 * is_front_page() тут true і для /, і для /en/ — мову перевіряємо окремо.
 * Без Polylang сайт вважаємо англійським.
 */
function skalum_is_en_home_reviews_schema(): bool
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return $cache = false;
    }

    if (!is_front_page()) {
        return $cache = false;
    }

    $lang = function_exists('pll_current_language')
        ? pll_current_language('slug')
        : 'en';

    return $cache = ($lang === 'en');
}

/**
 * Складає відгуки відрендереного блоку в спільний буфер для схеми.
 *
 * @param array|null $items Репітер тестимоніалів (description/client_name/company).
 */
function skalum_collect_testimonials($items): void
{
    if (!is_array($items)) {
        return;
    }

    if (!isset($GLOBALS['skalum_collected_testimonials'])) {
        $GLOBALS['skalum_collected_testimonials'] = [];
    }

    foreach ($items as $item) {
        $body = skalum_schema_text($item['description'] ?? '');
        $name = skalum_schema_text($item['client_name'] ?? '');

        if (!$body || !$name) {
            continue;
        }

        $GLOBALS['skalum_collected_testimonials'][$name . '|' . $body] = [
            'body'    => $body,
            'name'    => $name,
            'company' => skalum_schema_text($item['company'] ?? ''),
        ];
    }
}

/**
 * WYSIWYG/текстове поле → плоский рядок для JSON-LD: без тегів,
 * без HTML-ентіті й без переносів рядків.
 *
 * Копірайтери обертають відгуки в лапки («…», “…”) — для reviewBody це
 * зайве оформлення, тому крайні лапки прибираємо.
 */
function skalum_schema_text($value): string
{
    $text = wp_strip_all_tags((string) $value);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', (string) $text);
    $text = preg_replace('/^[\p{Pi}\p{Pf}"\x27]+|[\p{Pi}\p{Pf}"\x27]+$/u', '', (string) $text);

    return trim((string) $text);
}

/**
 * Абсолютний URL зображення продукту з фолбеком на кастомний лого.
 */
function skalum_en_home_product_image(): string
{
    $rel     = SKALUM_EN_HOME_PRODUCT['image'];
    $uploads = wp_get_upload_dir();

    if ($rel && file_exists(trailingslashit($uploads['basedir']) . $rel)) {
        return trailingslashit($uploads['baseurl']) . $rel;
    }

    $logo_id = get_theme_mod('custom_logo');
    $logo    = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';

    return $logo ?: trailingslashit($uploads['baseurl']) . $rel;
}

/**
 * Вивід схеми. wp_footer, бо блоки рендеряться в the_content — раніше
 * буфер відгуків ще порожній.
 */
add_action('wp_footer', function () {
    if (!skalum_is_en_home_reviews_schema()) {
        return;
    }

    $reviews = $GLOBALS['skalum_collected_testimonials'] ?? [];

    if (!$reviews) {
        return;
    }

    $home_en = function_exists('pll_home_url')
        ? pll_home_url('en')
        : home_url('/');

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => SKALUM_EN_HOME_PRODUCT['name'],
        'image'       => [skalum_en_home_product_image()],
        'description' => SKALUM_EN_HOME_PRODUCT['description'],
        'sku'         => SKALUM_EN_HOME_PRODUCT['sku'],
        'brand'       => [
            '@type' => 'Brand',
            'name'  => SKALUM_EN_HOME_PRODUCT['brand'],
        ],
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '5.0',
            'reviewCount' => (string) count($reviews),
        ],
        'offers' => [
            '@type'         => 'Offer',
            'url'           => $home_en,
            'priceCurrency' => SKALUM_EN_HOME_PRODUCT['currency'],
            'price'         => SKALUM_EN_HOME_PRODUCT['price'],
            // Дата рухається сама, щоб оффер не «протермінувався».
            'priceValidUntil' => gmdate('Y-12-31', strtotime('+6 months')),
            'itemCondition' => 'https://schema.org/NewCondition',
            'availability'  => 'https://schema.org/InStock',
        ],
        'review' => [],
    ];

    foreach ($reviews as $review) {
        $author = [
            '@type' => 'Person',
            'name'  => $review['name'],
        ];

        if ($review['company']) {
            $author['worksFor'] = [
                '@type' => 'Organization',
                'name'  => $review['company'],
            ];
        }

        $schema['review'][] = [
            '@type'      => 'Review',
            'reviewBody' => $review['body'],
            'reviewRating' => [
                '@type'       => 'Rating',
                'ratingValue' => '5',
                'bestRating'  => '5',
                'worstRating' => '1',
            ],
            'author' => $author,
        ];
    }

    $schema = apply_filters('skalum_en_home_product_schema', $schema);

    if (empty($schema['review'])) {
        return;
    }
    ?>
<script type="application/ld+json">
<?= wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php
}, 20);
