<?php
/**
 * BLOCK: Range pricing
 * Fields:
 * - title (wysiwyg)
 * - range (repeater): hours, total, per_hour
 * - cta (link array)
 * - description (text)
 *
 * @var array $block
 */

if (!defined('ABSPATH')) exit;

$id = $block['anchor'] ?? ('skalum-range-block-' . $block['id']);
$class = 'skalum-range-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$title = get_field('title');
$range = get_field('range') ?: [];
$cta = get_field('cta');
$description = (string) (get_field('description') ?: '');

if (empty($title) && empty($range) && empty($cta) && $description === '') return;

// normalize items
$items = [];
foreach ($range as $row) {
  $hours = trim((string)($row['hours'] ?? ''));
  $total = trim((string)($row['total'] ?? ''));
  $per   = trim((string)($row['per_hour'] ?? ''));

  if ($hours === '' && $total === '' && $per === '') continue;

  $items[] = [
    'hours' => $hours,
    'total' => $total,
    'per'   => $per,
  ];
}

if (empty($items)) {
  return;
}

// default selected index: середина або 0
$selected = 0;
if (count($items) > 2) {
  $selected = (int) floor((count($items) - 1) / 2);
}
$selected = max(0, min($selected, count($items) - 1));

// CTA parts
$cta_url    = is_array($cta) && !empty($cta['url']) ? $cta['url'] : '';
$cta_title  = is_array($cta) && !empty($cta['title']) ? $cta['title'] : '';
$cta_target = is_array($cta) && !empty($cta['target']) ? $cta['target'] : '_self';

?>
<section
  id="<?php echo esc_attr($id); ?>"
  class="<?php echo esc_attr($class); ?>"
  data-range-block
>
  <div class="container">
    <div class="skalum-range-block__inner fade-in">

      <?php if (!empty($title)): ?>
        <div class="skalum-range-block__title">
          <?php echo wp_kses_post($title); ?>
        </div>
      <?php endif; ?>

      <?php
        $json = wp_json_encode($items, JSON_UNESCAPED_UNICODE);
      ?>
      <div
        class="skalum-range-block__slider"
        data-range-slider
        data-range-items="<?php echo esc_attr($json); ?>"
        data-range-selected="<?php echo esc_attr((string)$selected); ?>"
      >

        <div class="range-track" role="group" aria-label="<?php esc_attr_e('Engagement level', 'skalum'); ?>">
          <div class="range-track__bar" aria-hidden="true"></div>
          <div class="range-track__fill" aria-hidden="true" data-range-fill></div>

          <div class="range-track__dots" data-range-dots>
            <?php foreach ($items as $i => $it): ?>
              <button
                type="button"
                class="range-dot<?php echo $i === $selected ? ' is-active' : ''; ?>"
                data-range-dot
                data-index="<?php echo (int)$i; ?>"
                aria-label="<?php echo esc_attr($it['hours']); ?>"
                aria-pressed="<?php echo $i === $selected ? 'true' : 'false'; ?>"
              >
                <span class="range-dot__inner" aria-hidden="true"></span>
              </button>
            <?php endforeach; ?>
          </div>

          <div class="range-track__labels" aria-hidden="true">
            <?php foreach ($items as $i => $it): ?>
              <div class="range-label"><?php echo esc_html($it['hours']); ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="skalum-range-block__card" data-range-card>
        <div class="range-card__price">
          <span class="range-card__total" data-range-total><?php echo esc_html($items[$selected]['total']); ?></span>
          <span class="range-card__per">/ mo</span>
        </div>

        <div class="range-card__meta">
          <div class="range-card__meta-item">
            <span class="range-card__meta-value" data-range-hours><?php echo esc_html($items[$selected]['hours']); ?></span>
            <span class="range-card__meta-unit">/ mo</span>
          </div>

          <div class="range-card__meta-item">
            <span class="range-card__meta-value" data-range-per><?php echo esc_html($items[$selected]['per']); ?></span>
            <span class="range-card__meta-unit">/ hr</span>
          </div>
        </div>

        <?php if ($cta_url && $cta_title): ?>
          <div class="range-card__cta">
            <a
              class="range-card__btn"
              href="<?php echo esc_url($cta_url); ?>"
              target="<?php echo esc_attr($cta_target); ?>"
              rel="<?php echo $cta_target === '_blank' ? 'noopener noreferrer' : ''; ?>"
            >
              <?php echo esc_html($cta_title); ?>
            </a>
          </div>
        <?php endif; ?>

        <?php if ($description !== ''): ?>
          <div class="range-card__desc">
            <?php echo esc_html($description); ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
