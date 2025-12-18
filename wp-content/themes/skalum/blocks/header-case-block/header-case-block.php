<?php
/**
 * Block: Cases head block (acf/skalum-header-case-block)
 *
 * Expected ACF fields:
 * - title (wysiwyg)
 * - description (wysiwyg)
 * - first_column (group: num_1, num_2, description)
 * - second_column (group: num_1, num_2, description)
 * - third_column (group: num_1, num_2, description)
 * - client_info (group: client_logo (id), name, type, services (term id), website)
 */

defined('ABSPATH') || exit;

$block_id = !empty($block['anchor']) ? $block['anchor'] : ('case-head-' . $block['id']);

$class_name = 'case-head';
if (!empty($block['className'])) $class_name .= ' ' . $block['className'];
if (!empty($block['align'])) $class_name .= ' align' . $block['align'];

// Fields
$title       = get_field('title');
$desc        = get_field('description');

$col1        = (array) get_field('first_column');
$col2        = (array) get_field('second_column');
$col3        = (array) get_field('third_column');

$client      = (array) get_field('client_info');

// Client fields
$client_logo_id = $client['client_logo'] ?? null;
$client_name    = $client['name'] ?? '';
$client_type    = $client['type'] ?? '';
$client_site    = $client['website'] ?? '';

/**
 * services field is taxonomy (return_format = id), multiple=0 in your JSON,
 * but we handle both id/int and array just in case.
 */
$services_value = $client['services'] ?? null;
$service_terms  = [];

if (!empty($services_value)) {
  if (is_array($services_value)) {
    foreach ($services_value as $tid) {
      $t = get_term((int)$tid, 'case_category');
      if ($t && !is_wp_error($t)) $service_terms[] = $t;
    }
  } else {
    $t = get_term((int)$services_value, 'case_category');
    if ($t && !is_wp_error($t)) $service_terms[] = $t;
  }
}

// Helpers
$render_stat = function(array $col) {
  $n1 = trim((string)($col['num_1'] ?? ''));
  $n2 = trim((string)($col['num_2'] ?? ''));
  $d  = trim((string)($col['description'] ?? ''));

  if ($n1 === '' && $n2 === '' && $d === '') return;

  ?>
  <div class="case-head__stat">
    <div class="case-head__stat-top">
      <?php if ($n1 !== '' || $n2 !== ''): ?>
        <div class="case-head__stat-nums">
          <?php if ($n1 !== ''): ?>
            <span class="case-head__stat-num case-head__stat-num--muted"><?= esc_html($n1); ?></span>
          <?php endif; ?>
          <?php if ($n2 !== ''): ?>
            <span class="case-head__stat-arrow">→</span>
            <span class="case-head__stat-num case-head__stat-num--accent"><?= esc_html($n2); ?></span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($d !== ''): ?>
      <div class="case-head__stat-desc"><?= esc_html($d); ?></div>
    <?php endif; ?>
  </div>
  <?php
};

?>

<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
  <div class="container case-head__container fade-in">

    <div class="case-head__grid">

      <!-- LEFT -->
      <div class="case-head__main">

        <?php if (!empty($title)): ?>
          <div class="case-head__title wysiwyg">
            <?php echo wp_kses_post($title); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($desc)): ?>
          <div class="case-head__text wysiwyg">
            <?php echo wp_kses_post($desc); ?>
          </div>
        <?php endif; ?>

        <div class="case-head__stats">
          <?php $render_stat($col1); ?>
          <?php $render_stat($col2); ?>
          <?php $render_stat($col3); ?>
        </div>

      </div>

      <!-- RIGHT -->
      <aside class="case-head__aside">
        <div class="case-head__aside-inner">

          <?php if (!empty($client_logo_id)): ?>
            <div class="case-head__logo">
              <?php echo wp_get_attachment_image((int)$client_logo_id, 'medium', false, ['loading' => 'lazy']); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($client_name)): ?>
            <div class="case-head__meta">
              <div class="case-head__meta-label"><?php esc_html_e('Client:', 'skalum'); ?></div>
              <div class="case-head__meta-value"><?php echo esc_html($client_name); ?></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($client_type)): ?>
            <div class="case-head__meta">
              <div class="case-head__meta-label"><?php esc_html_e('Nische:', 'skalum'); ?></div>
              <div class="case-head__meta-value"><?php echo esc_html($client_type); ?></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($service_terms)): ?>
            <div class="case-head__meta">
              <div class="case-head__meta-label"><?php esc_html_e('Our services:', 'skalum'); ?></div>
              <div class="case-head__meta-value">
                <?php
                $names = array_map(fn($t) => $t->name, $service_terms);
                echo esc_html(implode(', ', $names));
                ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($client_site)): ?>
            <div class="case-head__meta">
              <div class="case-head__meta-label"><?php esc_html_e('Website:', 'skalum'); ?></div>
              <div class="case-head__meta-value">
                <a class="case-head__link" href="<?php echo esc_url($client_site); ?>" target="_blank" rel="noopener">
                  <?php echo esc_html(preg_replace('#^https?://#', '', $client_site)); ?>
                  <span aria-hidden="true">↗</span>
                </a>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </aside>

    </div>

  </div>
</section>
