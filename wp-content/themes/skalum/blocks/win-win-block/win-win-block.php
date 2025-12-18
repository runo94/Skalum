<?php
/**
 * Template: Skalum – Win/Win Block
 * Block name: acf/skalum-win-win-block
 *
 * @var array $block WP block settings.
 */

if (!defined('ABSPATH')) {
  exit;
}

$id = $block['anchor'] ?? ('win-win-block-' . $block['id']);
$class = 'win-win-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';


/** ACF fields */
$title = get_field('title');          // wysiwyg
$description = get_field('description');    // wysiwyg
$cta = get_field('cta');            // link (array|false)
$photo_info = get_field('photo_info');     // group (array|false)
$photo = $photo_info['photo'] ?? null;      // image array|id|null
$person_name = $photo_info['name'] ?? '';
$position = $photo_info['position'] ?? '';

/** CTA parsed */
$cta_url = is_array($cta) && !empty($cta['url']) ? $cta['url'] : '';
$cta_title = is_array($cta) && !empty($cta['title']) ? $cta['title'] : '';
$cta_target = is_array($cta) && !empty($cta['target']) ? $cta['target'] : '_self';

/** Helpers */
$has_media = !empty($photo) || $person_name || $position;
$has_content = !empty($title) || !empty($description) || !empty($cta_url);
?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="win-win-block__inner fade-in <?php echo $has_media ? ' has-media' : ''; ?>">
      <?php if ($has_media): ?>
        <div class="win-win-block__media">
          <?php
          if (!empty($photo)) {
            // Підтримка як масиву, так і ID
            $image_id = is_array($photo) ? ($photo['ID'] ?? 0) : (int) $photo;
            if ($image_id) {
              echo wp_get_attachment_image(
                $image_id,
                'large',
                false,
                [
                  'class' => 'win-win-block__photo',
                  'loading' => 'lazy',
                  'decoding' => 'async',
                  'sizes' => '(min-width: 1024px) 480px, 100vw',
                ]
              );
            }
          }
          ?>

          <?php if ($person_name || $position): ?>
            <div class="win-win-block__caption">
              <?php if ($person_name): ?>
                <div class="win-win-block__name"><?php echo esc_html($person_name); ?></div>
              <?php endif; ?>
              <?php if ($position): ?>
                <div class="win-win-block__position"><?php echo esc_html($position); ?></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($has_content): ?>
        <div class="win-win-block__content">
          <?php if (!empty($title)): ?>
            <div class="win-win-block__title">
              <?php echo wp_kses_post($title); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($description)): ?>
            <div class="win-win-block__description">
              <?php echo wp_kses_post($description); ?>
            </div>
          <?php endif; ?>


        </div>
      <?php endif; ?>

      <?php if ($cta_url && $cta_title): ?>
        <div class="win-win-block__actions">
          <a class="btn win-win-block__cta" href="<?php echo esc_url($cta_url); ?>"
            target="<?php echo esc_attr($cta_target); ?>"
            rel="<?php echo esc_attr($cta_target === '_blank' ? 'noopener' : ''); ?>">
            <?php echo esc_html($cta_title); ?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>