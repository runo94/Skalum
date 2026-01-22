<?php
/**
 * BLOCK: Full Range Pricing
 * A single block that merges:
 * - Title block (block_name + title + description + position)
 * - Range pricing (range group)
 * - Included list (included group)
 *
 * ACF fields (as provided):
 * - position (select: left|center|right)
 * - block_name (text)
 * - title (wysiwyg)
 * - description (wysiwyg)
 * - range (group):
 *   - title (wysiwyg)
 *   - range (repeater): hours, total, per_hour
 *   - cta (link)
 *   - description (text)
 * - included (group):
 *   - border_top (true_false)
 *   - title (text)
 *   - list (repeater): icon (image), text (text)
 *
 * @var array $block WP block settings.
 */

if (!defined('ABSPATH')) exit;

$id = $block['anchor'] ?? ('skalum-full-range-pricing-' . $block['id']);
$class = 'full-range-pricing-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

// Optional common flag (safe if absent)
$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/** Top content */
$block_name  = (string) (get_field('block_name') ?: '');
$title       = get_field('title');
$description = get_field('description');

$position = (string) (get_field('position') ?: 'center');
$allowed_positions = ['left', 'center', 'right'];
if (!in_array($position, $allowed_positions, true)) {
  $position = 'center';
}
$class .= ' full-range-pricing-block--' . $position;

/** Range group */
$range_group = get_field('range');
$range_group = is_array($range_group) ? $range_group : [];

$range_title = $range_group['title'] ?? null;
$range_rows  = $range_group['range'] ?? [];
$range_rows  = is_array($range_rows) ? $range_rows : [];

$range_cta = $range_group['cta'] ?? null;
$range_desc = trim((string) ($range_group['description'] ?? ''));

// Normalize range items
$range_items = [];
foreach ($range_rows as $row) {
  $hours = trim((string) ($row['hours'] ?? ''));
  $total = trim((string) ($row['total'] ?? ''));
  $per   = trim((string) ($row['per_hour'] ?? ''));

  if ($hours === '' && $total === '' && $per === '') continue;

  $range_items[] = [
    'hours' => $hours,
    'total' => $total,
    'per'   => $per,
  ];
}

// default selected index: middle or 0
$selected = 0;
if (count($range_items) > 2) {
  $selected = (int) floor((count($range_items) - 1) / 2);
}
$selected = max(0, min($selected, max(0, count($range_items) - 1)));

// CTA parts
$cta_url    = is_array($range_cta) && !empty($range_cta['url']) ? (string) $range_cta['url'] : '';
$cta_title  = is_array($range_cta) && !empty($range_cta['title']) ? (string) $range_cta['title'] : '';
$cta_target = is_array($range_cta) && !empty($range_cta['target']) ? (string) $range_cta['target'] : '_self';

/** Included group */
$included = get_field('included');
$included = is_array($included) ? $included : [];

$included_border_top = !empty($included['border_top']);
$included_title = trim((string) ($included['title'] ?? ''));
$included_list  = $included['list'] ?? [];
$included_list  = is_array($included_list) ? $included_list : [];

$included_items = [];
foreach ($included_list as $row) {
  $text = trim((string) ($row['text'] ?? ''));
  if ($text === '') continue;

  $icon = $row['icon'] ?? null;
  $included_items[] = [
    'text' => $text,
    'icon' => is_array($icon) ? $icon : null,
  ];
}

// If everything is empty - render nothing
$has_top = ($block_name !== '' || !empty($title) || !empty($description));
$has_range = (!empty($range_title) || !empty($range_items) || ($cta_url && $cta_title) || $range_desc !== '');
$has_included = ($included_title !== '' || !empty($included_items));

if (!$has_top && !$has_range && !$has_included) {
  return;
}

?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="full-range-pricing-block__inner fade-in">

      <?php if ($has_top): ?>
        <div class="frp-title">
          <?php if ($block_name !== ''): ?>
            <div class="frp-title__name"><?php echo esc_html($block_name); ?></div>
          <?php endif; ?>

          <?php if (!empty($title)): ?>
            <div class="frp-title__title"><?php echo wp_kses_post($title); ?></div>
          <?php endif; ?>

          <?php if (!empty($description)): ?>
            <div class="frp-title__text"><?php echo wp_kses_post($description); ?></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($has_range && !empty($range_items)): ?>
        <?php $json = wp_json_encode($range_items, JSON_UNESCAPED_UNICODE); ?>

        <div class="frp-range" data-range-block>
          <?php if (!empty($range_title)): ?>
            <div class="frp-range__title">
              <?php echo wp_kses_post($range_title); ?>
            </div>
          <?php endif; ?>

          <div
            class="frp-range__slider"
            data-range-slider
            data-range-items="<?php echo esc_attr($json); ?>"
            data-range-selected="<?php echo esc_attr((string) $selected); ?>"
          >
            <div class="range-track" role="group" aria-label="<?php esc_attr_e('Engagement level', 'skalum'); ?>">
              <div class="range-track__bar" aria-hidden="true"></div>
              <div class="range-track__fill" aria-hidden="true" data-range-fill></div>

              <div class="range-track__dots" data-range-dots>
                <?php foreach ($range_items as $i => $it): ?>
                  <button
                    type="button"
                    class="range-dot<?php echo $i === $selected ? ' is-active' : ''; ?>"
                    data-range-dot
                    data-index="<?php echo (int) $i; ?>"
                    aria-label="<?php echo esc_attr($it['hours']); ?>"
                    aria-pressed="<?php echo $i === $selected ? 'true' : 'false'; ?>"
                  >
                    <span class="range-dot__inner" aria-hidden="true"></span>
                  </button>
                <?php endforeach; ?>
              </div>

              <div class="range-track__labels" aria-hidden="true">
                <?php foreach ($range_items as $it): ?>
                  <div class="range-label"><?php echo esc_html($it['hours']); ?></div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div class="frp-range__card" data-range-card>
            <div class="range-card__price">
              <span class="range-card__total" data-range-total><?php echo esc_html($range_items[$selected]['total']); ?></span>
              <span class="range-card__per">/ mo</span>
            </div>

            <div class="range-card__meta">
              <div class="range-card__meta-item">
                <span class="range-card__meta-value" data-range-hours><?php echo esc_html($range_items[$selected]['hours']); ?></span>
                <span class="range-card__meta-unit">/ mo</span>
              </div>

              <div class="range-card__meta-item">
                <span class="range-card__meta-value" data-range-per><?php echo esc_html($range_items[$selected]['per']); ?></span>
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

            <?php if ($range_desc !== ''): ?>
              <div class="range-card__desc"><?php echo esc_html($range_desc); ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($has_included): ?>
        <div class="frp-included<?php echo $included_border_top ? ' frp-included--border-top' : ''; ?>">
          <?php if ($included_title !== ''): ?>
            <div class="frp-included__title"><?php echo esc_html($included_title); ?></div>
          <?php endif; ?>

          <?php if (!empty($included_items)): ?>
            <ul class="frp-included__list" role="list">
              <?php foreach ($included_items as $it): ?>
                <li class="frp-included__item">
                  <span class="frp-included__icon" aria-hidden="true">
                    <?php if (!empty($it['icon']['url'])): ?>
                      <img
                        src="<?php echo esc_url($it['icon']['url']); ?>"
                        alt=""
                        loading="lazy"
                        decoding="async"
                      />
                    <?php else: ?>
                      <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                        <path d="M9.2 16.6 4.9 12.3l1.4-1.4 2.9 2.9 8.5-8.5 1.4 1.4-9.9 9.9z" fill="currentColor"/>
                      </svg>
                    <?php endif; ?>
                  </span>
                  <span class="frp-included__text"><?php echo esc_html($it['text']); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
