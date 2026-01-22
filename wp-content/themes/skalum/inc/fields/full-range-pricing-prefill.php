<?php
if (!defined('ABSPATH')) exit;

/**
 * ===== Debug toggles =====
 * 1) easiest: set define('SKALUM_ACFDBG', true); in wp-config.php (dev only)
 * 2) or add ?acfdbg=1 to admin URL (may NOT propagate to admin-ajax).
 */
function skalum_acfdbg_enabled(): bool {
  if (defined('SKALUM_ACFDBG') && SKALUM_ACFDBG) return true;
  return is_admin() && isset($_GET['acfdbg']) && $_GET['acfdbg'] == '1';
}

function skalum_acfdbg_log(string $label, array $data = []): void {
  if (!skalum_acfdbg_enabled()) return;

  $payload = [
    't'     => date('H:i:s'),
    'label' => $label,
    'ajax'  => (defined('DOING_AJAX') && DOING_AJAX) ? 1 : 0,
    'uri'   => $_SERVER['REQUEST_URI'] ?? '',
  ] + $data;

  error_log('[SKALUM ACFDBG] ' . wp_json_encode($payload));
}

function skalum_acfdbg_header(string $name, string $value): void {
  if (!skalum_acfdbg_enabled()) return;
  if (!is_admin()) return;
  if (headers_sent()) return;
  header($name . ': ' . $value);
}

/**
 * Mark every admin-ajax response when debug is enabled (visible in DevTools).
 */
add_action('admin_init', function () {
  if (!(defined('DOING_AJAX') && DOING_AJAX)) return;
  if (!skalum_acfdbg_enabled()) return;

  skalum_acfdbg_header('X-Skalum-ACFDBG', 'active');
}, 1);

/**
 * Range -> range (repeater) KEY: field_6960d9fc5ad5d
 * Sub fields (KEYs):
 *  - Hours:    field_6960da035ad5e
 *  - Total:    field_6960da0f5ad5f
 *  - Per Hour: field_6960da205ad60
 */
add_filter('acf/load_value', function ($value, $post_id, $field) {

  // працюємо ТІЛЬКИ з нашим блоком
  $supported_keys = [
    'field_6960d9fc5ad5d', // Range -> range (repeater)
    'field_6960e6bd5ad6a', // Included -> list (repeater)
    'field_6960da2e5ad61', // Range -> CTA (link)
  ];

  if (!in_array($field['key'] ?? '', $supported_keys, true)) {
    return $value;
  }

  // ACF Blocks: empty === NULL або FALSE
  if ($value !== null && $value !== false) {
    return $value;
  }

  switch ($field['key']) {

    /**
     * ======================
     * Range -> range
     * ======================
     */
    case 'field_6960d9fc5ad5d':
      return [
        ['field_6960da035ad5e' => '20h', 'field_6960da0f5ad5f' => '$1800', 'field_6960da205ad60' => '$90'],
        ['field_6960da035ad5e' => '30h', 'field_6960da0f5ad5f' => '$2700', 'field_6960da205ad60' => '$90'],
        ['field_6960da035ad5e' => '40h', 'field_6960da0f5ad5f' => '$3600', 'field_6960da205ad60' => '$90'],
        ['field_6960da035ad5e' => '50h', 'field_6960da0f5ad5f' => '$4500', 'field_6960da205ad60' => '$90'],
        ['field_6960da035ad5e' => '60h', 'field_6960da0f5ad5f' => '$5400', 'field_6960da205ad60' => '$90'],
        ['field_6960da035ad5e' => '70h', 'field_6960da0f5ad5f' => '$6300', 'field_6960da205ad60' => '$90'],
        ['field_6960da035ad5e' => '80h', 'field_6960da0f5ad5f' => '$7200', 'field_6960da205ad60' => '$90'],
      ];

    /**
     * ======================
     * Included -> list
     * ======================
     */
    case 'field_6960e6bd5ad6a':
      return [
        ['field_6960e6cd5ad6b' => 0, 'field_6960e6dc5ad6c' => 'Key Words Optimized'],
        ['field_6960e6cd5ad6b' => 0, 'field_6960e6dc5ad6c' => 'Website Audit & Analysis'],
        ['field_6960e6cd5ad6b' => 0, 'field_6960e6dc5ad6c' => 'Technical SEO Basics'],
        ['field_6960e6cd5ad6b' => 0, 'field_6960e6dc5ad6c' => 'Local SEO Optimization'],
      ];

    /**
     * ======================
     * CTA
     * ======================
     */
    case 'field_6960da2e5ad61':
      return [
        'title'  => 'Get free consultation',
        'url'    => '#',
        'target' => '',
      ];
  }

  return $value;
}, 20, 3);


/**
 * Included -> list (repeater) KEY: field_6960e6bd5ad6a
 * Sub fields (KEYs):
 *  - Icon: field_6960e6cd5ad6b
 *  - Text: field_6960e6dc5ad6c
 */
add_filter('acf/load_value/key=field_6960e6bd5ad6a', function ($value, $post_id, $field) {

  skalum_acfdbg_log('included_load_value_hit', [
    'post_id' => $post_id,
    'value_type' => gettype($value),
    'value_is_null' => $value === null ? 1 : 0,
    'value_count' => is_array($value) ? count($value) : -1,
  ]);

  if (defined('DOING_AJAX') && DOING_AJAX) {
    skalum_acfdbg_header('X-Skalum-ACFDBG-Included', $value === null ? 'NULL' : ('NONNULL:' . (is_array($value) ? count($value) : gettype($value))));
  }

  if ($value !== NULL) {
    skalum_acfdbg_log('included_return_existing', [
      'reason' => 'value_not_null',
    ]);
    return $value;
  }

  $defaults = [
    [
      'field_6960e6cd5ad6b' => 0,
      'field_6960e6dc5ad6c' => 'Key Words Optimized',
    ],
    [
      'field_6960e6cd5ad6b' => 0,
      'field_6960e6dc5ad6c' => 'Website Audit & Analysis',
    ],
    [
      'field_6960e6cd5ad6b' => 0,
      'field_6960e6dc5ad6c' => 'Technical SEO Basics',
    ],
    [
      'field_6960e6cd5ad6b' => 0,
      'field_6960e6dc5ad6c' => 'Local SEO Optimization',
    ],
  ];

  skalum_acfdbg_log('included_apply_defaults', [
    'rows' => count($defaults),
  ]);

  if (defined('DOING_AJAX') && DOING_AJAX) {
    skalum_acfdbg_header('X-Skalum-ACFDBG-Included-Apply', 'rows:' . count($defaults));
  }

  return $defaults;
}, 20, 3);
