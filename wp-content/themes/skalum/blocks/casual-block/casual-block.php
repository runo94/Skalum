<?php
/**
 * Template: Skalum – Casual Block
 * Block name: acf/skalum-casual-block
 *
 * @var array $block WP block settings.
 */

if (!defined('ABSPATH')) {
  exit;
}

$id = $block['anchor'] ?? ('casual-block-' . $block['id']);
$class = 'casual-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

/** ACF fields (top-level) */
$title = get_field('title');       // wysiwyg
$description = get_field('description'); // wysiwyg

/** ACF group: card */
$card = get_field('card') ?: [];
$card_icon = $card['icon'] ?? null;                 // image (array|id)
$card_title = $card['title'] ?? '';                  // text
$card_desc = $card['card-description'] ?? '';       // wysiwyg (name з дефісом)
$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/** Helpers */
$has_header = !empty($title) || !empty($description);
$has_card = !empty($card_icon) || !empty($card_title) || !empty($card_desc);
?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="casual-block__inner fade-in">
      <?php if ($has_header): ?>
        <div class="casual-block__content">
          <div class="casual-block__header">
            <?php if (!empty($title)): ?>
              <div class="casual-block__title">
                <?php echo wp_kses_post($title); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($description)): ?>
              <div class="casual-block__description">
                <?php echo wp_kses_post($description); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($has_card): ?>
        <div class="casual-block__card">
          <?php if (!empty($card_icon)): ?>
            <div class="casual-block__icon">
              <?php
              $img_id = is_array($card_icon) ? ($card_icon['ID'] ?? 0) : (int) $card_icon;
              if ($img_id) {
                echo wp_get_attachment_image(
                  $img_id,
                  'full',
                  false,
                  [
                    'class' => 'casual-block__icon-img',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                  ]
                );
              }
              ?>
            </div>
          <?php endif; ?>

          <div class="casual-block__card-body">
            <?php if (!empty($card_title)): ?>
              <h3 class="casual-block__card-title"><?php echo esc_html($card_title); ?></h3>
            <?php endif; ?>

            <?php if (!empty($card_desc)): ?>
              <div class="casual-block__card-desc">
                <?php
                echo wp_kses_post($card_desc);
                ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>