<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('prices-block-' . $block['id']);
$class = 'prices-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

$title       = get_field('title');
$description = get_field('description');
$block_name  = get_field('block_name');
$plans       = get_field('plans') ?: [];
$cta         = get_field('cta'); // ['url','title','target']

$mk_slug = static function ($s) {
  $s = is_string($s) ? $s : '';
  $s = strtolower(trim($s));
  $s = preg_replace('~[^a-z0-9]+~', '-', $s);
  return trim($s, '-');
};

/** Build JSON for JS — pack_items is REPEATER with icon=ID */
$js_plans = [];
foreach ($plans as $i => $p) {
  $pid    = $mk_slug($p['title'] ?: ('plan-' . ($i + 1)));
  $badge1 = (string)($p['oprion_1'] ?? '');
  $badge2 = (string)($p['option_2'] ?? '');

  $features = [];
  if (!empty($p['pack_items']) && is_array($p['pack_items'])) {
    foreach ($p['pack_items'] as $row) {
      $icon_id  = isset($row['icon']) ? (int) $row['icon'] : 0;
      $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
      $features[] = [
        'label' => (string)($row['option'] ?? ''),
        'icon'  => $icon_url,
      ];
    }
  }

  $js_plans[] = [
    'id'       => $pid,
    'title'    => (string)($p['title'] ?? ''),
    'price'    => (string)($p['price'] ?? ''),
    'badges'   => array_values(array_filter([$badge1, $badge2], static fn($v) => $v !== '')),
    'features' => $features,
  ];
}
$plans_json = wp_json_encode($js_plans, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

/** CTA defaults */
$cta_url    = !empty($cta['url'])   ? $cta['url']   : '#contact';
$cta_title  = !empty($cta['title']) ? $cta['title'] : 'Get free consultation';
$cta_target = !empty($cta['target'])? $cta['target']: '_self';
$cta_rel    = ($cta_target === '_blank') ? 'noopener' : '';

/** SSR for right column (first/active plan) */
$first_plan     = $js_plans[0] ?? null;
$first_badges   = $first_plan['badges']   ?? [];
$first_features = $first_plan['features'] ?? [];
?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="prices-block__inner">

      <?php if ($block_name || $title): ?>
        <header class="prices-block__header">
          <?php if ($block_name): ?>
            <div class="prices-block__name"><?php echo esc_html($block_name); ?></div>
          <?php endif; ?>
          <?php if ($title): ?>
            <div class="prices-block__title"><?php echo wp_kses_post($title); ?></div>
          <?php endif; ?>
        </header>
      <?php endif; ?>

      <div class="prices-block__content">

        <div class="prices-block__grid"
             data-prices
             data-plans-json="<?php echo esc_attr($plans_json); ?>"
             data-target-input="#selected-plan-input"><!-- селектор hidden-поля форми -->

          <!-- LEFT SIDE -->
          <div class="prices-block__left" data-plans>
            <?php foreach ($plans as $i => $p):
              $pid = $mk_slug($p['title'] ?: ('plan-' . ($i + 1)));
              $is_active = ($i === 0);
            ?>
              <button
                type="button"
                class="plan-card<?php echo $is_active ? ' is-active' : ''; ?>"
                data-plan="<?php echo esc_attr($pid); ?>"
                data-index="<?php echo esc_attr($i); ?>"
                aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
              >
                <div class="plan-card__row">
                  <div class="plan-card__title">
                    <span class="plan-card__dot" aria-hidden="true"></span>
                    <?php echo esc_html($p['title'] ?? ''); ?>
                  </div>
                  <?php if (!empty($p['price'])): ?>
                    <div class="plan-card__price"><?php echo esc_html($p['price']); ?></div>
                  <?php endif; ?>
                </div>

                <?php if (!empty($p['oprion_1']) || !empty($p['option_2'])): ?>
                  <div class="plan-card__badges">
                    <?php if (!empty($p['oprion_1'])): ?>
                      <span class="badge"><?php echo esc_html($p['oprion_1']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($p['option_2'])): ?>
                      <span class="badge"><?php echo esc_html($p['option_2']); ?></span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </button>
            <?php endforeach; ?>
          </div>

          <!-- RIGHT SIDE -->
          <div class="prices-block__right">
            <ul class="features-list" data-features>
              <?php if (!empty($first_badges)): ?>
                <li class="features-list__badges">
                  <?php foreach ($first_badges as $b): ?>
                    <span class="badge"><?php echo esc_html($b); ?></span>
                  <?php endforeach; ?>
                </li>
              <?php endif; ?>

              <?php if (!empty($first_features)): ?>
                <?php foreach ($first_features as $f):
                  $url = !empty($f['icon']) ? esc_url($f['icon']) : '';
                  $label = isset($f['label']) ? (string)$f['label'] : '';
                ?>
                  <li class="features-list__item">
                    <span class="feat-icon">
                      <?php if ($url): ?>
                        <img src="<?php echo $url; ?>" alt="" loading="lazy" decoding="async">
                      <?php else: ?>
                        ✓
                      <?php endif; ?>
                    </span>
                    <span class="feat-text"><?php echo esc_html($label); ?></span>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>

            <div class="prices-block__cta">
              <a class="btn" data-choose-plan
                 href="<?php echo esc_url($cta_url); ?>"
                 target="<?php echo esc_attr($cta_target); ?>"
                 rel="<?php echo esc_attr($cta_rel); ?>">
                <?php echo esc_html($cta_title); ?>
              </a>
            </div>

            <noscript>
              <p><?php esc_html_e('JavaScript is disabled. You are viewing the default plan details.', 'your-textdomain'); ?></p>
            </noscript>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
