<?php
/**
 * Block: Hero Expert
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('hero-expert-block-' . $block['id']);
$class = 'hero-expert-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/**
 * Assets
 * (підстрой шлях якщо у тебе інша структура)
 */
$assets_base = trailingslashit(get_stylesheet_directory_uri()) . 'blocks/hero-expert-block/assets/';
$portrait_fallback_url = $assets_base . 'images/portrait.png';
$logos_dir_abs = trailingslashit(get_stylesheet_directory()) . 'blocks/hero-expert-block/assets/images/logos/';

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
    'title'  => 'Jetzt Eintragen',
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
  '8+ years in eCommerce & Shopify SEO',
  'Leads a dedicated SEO & CRO team',
  'Working with eCommerce brands worldwide (US, UK, Germany, Austria, Switzerland & more)',
];

/**
 * Default logos from theme folder (якщо repeater logos пустий)
 * Підтягуємо всі файли з /assets/images/logos/*.(svg|png|webp|jpg|jpeg)
 */
$default_logo_urls = [];
if (is_dir($logos_dir_abs)) {
  $files = glob($logos_dir_abs . '*.{svg,png,webp,jpg,jpeg}', GLOB_BRACE) ?: [];
  // Щоб порядок був стабільний
  sort($files, SORT_NATURAL | SORT_FLAG_CASE);

  foreach ($files as $abs_path) {
    $rel = str_replace(trailingslashit(get_stylesheet_directory()), '', $abs_path);
    $default_logo_urls[] = trailingslashit(get_stylesheet_directory_uri()) . $rel;
  }
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="hero-expert-block__inner fade-in">

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
                $text = get_sub_field('text');
                if (!$text) continue;
              ?>
                <li class="hero-expert-block__item">
                  <span class="hero-expert-block__check" aria-hidden="true"></span>
                  <span class="hero-expert-block__item-text"><?php echo esc_html($text); ?></span>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php else: ?>
            <ul class="hero-expert-block__list" role="list">
              <?php foreach ($default_benefits as $text): ?>
                <li class="hero-expert-block__item">
                  <span class="hero-expert-block__check" aria-hidden="true"></span>
                  <span class="hero-expert-block__item-text"><?php echo esc_html($text); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <?php
          $cta_title  = trim((string) ($cta['title'] ?? ''));
          $cta_url    = trim((string) ($cta['url'] ?? ''));
          $cta_target = !empty($cta['target']) ? (string) $cta['target'] : '_self';

          if ($cta_title && $cta_url): ?>
            <div class="hero-expert-block__actions">
              <a class="hero-expert-block__btn"
                 href="<?php echo esc_url($cta_url); ?>"
                 target="<?php echo esc_attr($cta_target); ?>"
                 rel="<?php echo $cta_target === '_blank' ? 'noopener noreferrer' : ''; ?>">
                <?php echo esc_html($cta_title); ?>
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
        </div>

      </div><!-- /grid -->

      <?php if (have_rows('logos')): ?>
        <div class="hero-expert-block__logos" aria-label="<?php echo esc_attr__('Client logos', 'skalum'); ?>">
          <?php while (have_rows('logos')): the_row();
            $logo_id = (int) get_sub_field('logo');
            if (!$logo_id) continue;

            $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
            if (!$logo_url) continue;

            $logo_alt = (string) get_post_meta($logo_id, '_wp_attachment_image_alt', true);
          ?>
            <div class="hero-expert-block__logo">
              <img src="<?php echo esc_url($logo_url); ?>"
                   alt="<?php echo esc_attr($logo_alt); ?>"
                   loading="lazy"
                   decoding="async">
            </div>
          <?php endwhile; ?>
        </div>

      <?php elseif (!empty($default_logo_urls)): ?>
        <div class="hero-expert-block__logos" aria-label="<?php echo esc_attr__('Client logos', 'skalum'); ?>">
          <?php foreach ($default_logo_urls as $url): ?>
            <div class="hero-expert-block__logo">
              <img src="<?php echo esc_url($url); ?>" alt="" loading="lazy" decoding="async">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
