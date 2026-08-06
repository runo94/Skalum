<?php
/**
 * Block: Testimonials Carousel (Global)
 *
 * Розмітка та класи такі самі, як у testimonials-block, тому працює той самий
 * CSS і той самий ініціалізатор Slick. Різниця лише в джерелі даних:
 * Skalum Settings (get_field(..., 'option')) з розділенням по мовах.
 */

$id = $block['anchor'] ?? ('testimonials-global-block-' . $block['id']);
$class = 'testimonials-block testimonials-block--global'
    . (!empty($block['className']) ? ' ' . $block['className'] : '')
    . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/**
 * Поточна мова. Глобальні поля зберігаються парами *_en / *_de
 * (так само, як навігація та футер у footer.php).
 */
$current_lang = function_exists('pll_current_language')
    ? pll_current_language('slug')
    : 'en';

if (!in_array($current_lang, ['en', 'de'], true)) {
    $current_lang = 'en';
}

$settings = get_field('testimonials', 'option') ?: [];

$block_name   = $settings['block_name_' . $current_lang] ?? '';
$title        = $settings['title_' . $current_lang] ?? '';
$testimonials = $settings['testimonials_' . $current_lang] ?? [];

if (!is_array($testimonials)) {
    $testimonials = [];
}

// Фолбек на EN, якщо для поточної мови контент ще не заповнили.
if (!$testimonials && $current_lang !== 'en') {
    $fallback = $settings['testimonials_en'] ?? [];
    if (is_array($fallback) && $fallback) {
        $testimonials = $fallback;
        $block_name   = $block_name ?: ($settings['block_name_en'] ?? '');
        $title        = $title ?: ($settings['title_en'] ?? '');
    }
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
    <div class="container">
        <div class="testimonials-block__inner fade-in">

            <!-- Header -->
            <div class="testimonials-block__header">
                <?php if ($block_name): ?>
                    <div class="testimonials-block__name"><?php echo esc_html($block_name); ?></div>
                <?php endif; ?>

                <?php if ($title): ?>
                    <div class="testimonials-block__title"><?php echo wp_kses_post($title); ?></div>
                <?php endif; ?>
            </div>

            <!-- Slider -->
            <?php if (count($testimonials) > 0): ?>
                <div class="testimonials-block__slider">
                    <?php foreach ($testimonials as $item):
                        $desc = $item['description'] ?? '';
                        $name = $item['client_name'] ?? '';
                        $company = $item['company'] ?? '';
                    ?>
                        <div class="testimonial-card">
                            <div class="testimonial-card__stars">
                                ★★★★★
                            </div>
                            <div class="testimonial-card__text">
                                <?php echo wp_kses_post($desc); ?>
                            </div>
                            <div class="testimonial-card__author">
                                <strong><?php echo esc_html($name); ?></strong>
                                <?php if ($company): ?>
                                    <span><?php echo esc_html($company); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Dots -->
                <div class="testimonials-block__dots"></div>
            <?php else: ?>
                <p>
                    <?php esc_html_e('Testimonials is empty. Fill in Skalum Settings → Testimonials.', 'skalum'); ?>
                </p>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php
// --- Schema: Reviews for Testimonials block ---
if (count($testimonials) > 0) {

  $site_name = get_bloginfo('name');
  $site_url  = home_url('/');

  $schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [],
  ];

  foreach ($testimonials as $item) {
    $desc    = $item['description'] ?? '';
    $name    = $item['client_name'] ?? '';
    $company = $item['company'] ?? '';

    if (!$desc || !$name) {
      continue;
    }

    $review_body = wp_strip_all_tags($desc);
    if (!$review_body) {
      continue;
    }

    $author = ['@type' => 'Person', 'name' => wp_strip_all_tags($name)];

    if ($company) {
      $author['worksFor'] = [
        '@type' => 'Organization',
        'name'  => wp_strip_all_tags($company),
      ];
    }

    $schema['@graph'][] = [
      '@type' => 'Review',
      'reviewBody' => $review_body,
      'reviewRating' => [
        '@type'       => 'Rating',
        'ratingValue' => '5',
        'bestRating'  => '5',
        'worstRating' => '1',
      ],
      'author' => $author,
      'publisher' => [
        '@type' => 'Organization',
        'name'  => $site_name,
        'url'   => $site_url,
      ],
      'itemReviewed' => [
        '@type' => 'Organization',
        'name'  => $site_name,
        'url'   => $site_url,
      ],
    ];
  }

  if (!empty($schema['@graph'])) :
?>
<script type="application/ld+json">
<?= wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php
  endif;
}
?>
