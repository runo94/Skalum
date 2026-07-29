<?php
/**
 * Block: Hero Expert
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('hero-expert-block-' . $block['id']);
$class = 'hero-expert-block animation-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/**
 * Assets
 * (підстрой шлях якщо у тебе інша структура)
 */
$assets_base = trailingslashit(get_stylesheet_directory_uri()) . 'blocks/hero-expert-block/assets/';
$portrait_fallback_url = $assets_base . 'images/portrait-2.png';
$logos_dir_abs = trailingslashit(get_stylesheet_directory()) . 'blocks/hero-expert-block/assets/images/';

// Light effect PNG
$assets_base_abs = trailingslashit(get_stylesheet_directory()) . 'blocks/hero-expert-block/assets/';

$light_effect_url = $assets_base . 'images/light-effect.png';
$light_effect_abs = $assets_base_abs . 'images/light-effect.png';

/** Content (ACF) */
$block_name  = get_field('block_name');
$title       = get_field('title');        // wysiwyg
$description = get_field('description');  // wysiwyg
$cta         = get_field('cta');          // link array: url/title/target
$image_id    = (int) get_field('image');  // attachment ID

/**
 * Defaults (як на скріні)
 */
if (!$title) {
  $title = 'Hire a <span class="is-accent">Shopify SEO Expert</span><br>for eCommerce Stores';
}

if (!$description) {
  $description = 'Founder-led SEO for Shopify stores in the US, UK &amp; DACH markets';
}

if (!$cta || !is_array($cta) || empty($cta['title']) || empty($cta['url'])) {
  $cta = [
    'title'  => 'Free Strategy Call',
    'url'    => '#',
    'target' => '_self',
  ];
}

/**
 * Image (ACF ID -> url) + fallback to theme asset
 */
$image_url = '';
$image_alt = '';

if ($image_id) {
  $image_url = wp_get_attachment_image_url($image_id, 'large');
  $image_alt = (string) get_post_meta($image_id, '_wp_attachment_image_alt', true);
}

if (!$image_url) {
  $image_url = $portrait_fallback_url;
  $image_alt = 'Portrait';
}

/**
 * Default benefits (як на скріні) — якщо repeater пустий
 */
$default_benefits = [
    [
        'title' => '8+',
        'text'  => 'years in eCommerce & Shopify SEO',
    ],
    [
        'title' => '99%',
        'text'  => 'job success on Upwork',
    ],
    [
        'title' => '120+',
        'text'  => 'clients served worldwide',
    ],
];

/**
 * Default logos from theme folder (якщо repeater logos пустий)
 * Підтягуємо всі файли з /assets/images/logos/*.(svg|png|webp|jpg|jpeg)
 */

$default_logo_items = [
  [
    'file' => 'top-rate.png',
    'text' => 'Top Rated</br> Plus Experts',
  ],
  [
    'file' => 'reviewed.png',
    'text' => '100+ Reviewed Company',
  ],
  [
    'file' => 'shopify.png',
    'text' => 'Shopify Partners Agency',
  ],
  [
    'file' => 'upwork.png',
    'text' => '99% Job Success on Upwork',
  ],
];

// ✅ Build final array: only pairs (image exists AND text not empty)
$logos_final = [];

// 1) ACF logos (highest priority)
if (have_rows('logos')) {
  while (have_rows('logos')) { the_row();

    $logo_id   = (int) get_sub_field('logo');
    $logo_text = trim((string) get_sub_field('text'));

    if (!$logo_id || $logo_text === '') continue;

    $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
    if (!$logo_url) continue;

    $logo_alt = (string) get_post_meta($logo_id, '_wp_attachment_image_alt', true);

    $logos_final[] = [
      'url'  => $logo_url,
      'alt'  => $logo_alt,
      'text' => $logo_text,
    ];
  }
}

// 2) Fallback to defaults only if ACF produced nothing
if (empty($logos_final)) {
  foreach ($default_logo_items as $item) {
    $file = trim((string)($item['file'] ?? ''));
    $text = trim((string)($item['text'] ?? ''));

    if ($file === '' || $text === '') continue;

    $abs = $logos_dir_abs . $file; // $logos_dir_abs already has trailing slash
    if (!file_exists($abs)) continue;

    $url = trailingslashit(get_stylesheet_directory_uri()) . 'blocks/hero-expert-block/assets/images/' . $file;

    $logos_final[] = [
      'url'  => $url,
      'alt'  => '',
      'text' => $text,
    ];
  }
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="hero-expert-block__inner fade-in">

        <?php if (file_exists($light_effect_abs)) : ?>
            <img
                    class="hero-expert-block__light"
                    src="<?php echo esc_url($light_effect_url); ?>"
                    alt=""
                    aria-hidden="true"
                    decoding="async"
            >
        <?php endif; ?>

      <div class="hero-expert-block__grid">

        <div class="hero-expert-block__content">

          <?php if ($block_name): ?>
            <div class="hero-expert-block__name"><?php echo esc_html($block_name); ?></div>
          <?php endif; ?>

          <?php if ($title): ?>
            <div class="hero-expert-block__title">
              <?php echo wp_kses_post($title); ?>
            </div>
          <?php endif; ?>

          <?php if ($description): ?>
            <div class="hero-expert-block__text">
              <?php echo wp_kses_post($description); ?>
            </div>
          <?php endif; ?>

          <?php if (have_rows('benefits')): ?>
            <ul class="hero-expert-block__list" role="list">
              <?php while (have_rows('benefits')): the_row();
                  $title = get_sub_field('title');
                  $text  = get_sub_field('text');
                if (!$text) continue;
              ?>
                <li class="hero-expert-block__item">
                    <span class="hero-expert-block__item-title">
                        <?php echo esc_html($title); ?>
                    </span>
                    <span class="hero-expert-block__item-text">
                         <?php echo esc_html($text); ?>
                    </span>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php else: ?>
            <ul class="hero-expert-block__list" role="list">
                <?php foreach ($default_benefits as $benefit): ?>
                  <li class="hero-expert-block__item">

                        <?php if (!empty($benefit['title'])): ?>
                            <span class="hero-expert-block__item-title">
                                <?php echo esc_html($benefit['title']); ?>
                            </span>
                        <?php endif; ?>
                      <?php if (!empty($benefit['text'])): ?>
                            <span class="hero-expert-block__item-text">
                                <?php echo esc_html($benefit['text']); ?>
                            </span>
                        <?php endif; ?>

                  </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <?php
          $cta_title  = trim((string) ($cta['title'] ?? ''));
          $cta_url    = trim((string) ($cta['url'] ?? ''));
          $cta_target = !empty($cta['target']) ? (string) $cta['target'] : '_self';
          $icon_url = get_stylesheet_directory_uri() . '/blocks/hero-expert-block/assets/images/icon-calendar.png';

          if ($cta_title && $cta_url): ?>
            <div class="hero-expert-block__actions">
              <a class="hero-expert-block__btn"
                 href="<?php echo esc_url($cta_url); ?>"
                 target="<?php echo esc_attr($cta_target); ?>"
                 rel="<?php echo $cta_target === '_blank' ? 'noopener noreferrer' : ''; ?>">
                    <span><?php echo esc_html($cta_title); ?></span>
                  <img class="hero-expert-block__btn-icon"
                       src="<?php echo esc_url($icon_url); ?>"
                       alt=""
                       aria-hidden="true"
                       decoding="async">
              </a>
            </div>
          <?php endif; ?>

        </div><!-- /content -->

        <div class="hero-expert-block__media">
          <?php if ($image_url): ?>
            <img class="hero-expert-block__image"
                 src="<?php echo esc_url($image_url); ?>"
                 alt="<?php echo esc_attr($image_alt); ?>"
                 loading="eager"
                 decoding="async">
          <?php endif; ?>

            <?php if (!empty($logos_final)): ?>
              <div class="hero-expert-block__logos" aria-label="<?php echo esc_attr__('Client logos', 'skalum'); ?>">
                <?php foreach ($logos_final as $item): ?>
                  <div class="hero-expert-block__logo-item">
                    <img
                      src="<?php echo esc_url($item['url']); ?>"
                      alt="<?php echo esc_attr($item['alt'] ?? ''); ?>"
                      loading="lazy"
                      decoding="async"
                    >
                    <span class="hero-expert-block__logo-text">
                      <?php echo wp_kses_post($item['text']); ?>
                    </span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

        </div>

      </div><!-- /grid -->

    </div>
  </div>
</section>
