<?php
defined('ABSPATH') || exit;

/**
 * Get ACF block attrs[data] from post_content.
 *
 * @param int    $post_id
 * @param string $block_name e.g. 'acf/skalum-header-case-block'
 * @return array|null
 */
if (!function_exists('skalum_get_acf_block_data')) {
  function skalum_get_acf_block_data(int $post_id, string $block_name): ?array
  {
    $post = get_post($post_id);
    if (!$post || empty($post->post_content))
      return null;

    $blocks = parse_blocks($post->post_content);
    if (empty($blocks))
      return null;

    $stack = $blocks;

    while (!empty($stack)) {
      $b = array_shift($stack);

      if (!empty($b['blockName']) && $b['blockName'] === $block_name) {
        $data = $b['attrs']['data'] ?? null;
        return is_array($data) ? $data : null;
      }

      if (!empty($b['innerBlocks']) && is_array($b['innerBlocks'])) {
        foreach ($b['innerBlocks'] as $inner) {
          $stack[] = $inner;
        }
      }
    }

    return null;
  }
}

/**
 * CPT + taxonomy registration
 */
add_action('init', function () {

  $labels = [
    'name' => __('Cases', 'skalum'),
    'singular_name' => __('Case', 'skalum'),
    'menu_name' => __('Cases', 'skalum'),
    'name_admin_bar' => __('Case', 'skalum'),
    'add_new' => __('Add New', 'skalum'),
    'add_new_item' => __('Add New Case', 'skalum'),
    'new_item' => __('New Case', 'skalum'),
    'edit_item' => __('Edit Case', 'skalum'),
    'view_item' => __('View Case', 'skalum'),
    'all_items' => __('All Cases', 'skalum'),
    'search_items' => __('Search Cases', 'skalum'),
    'not_found' => __('No cases found.', 'skalum'),
    'not_found_in_trash' => __('No cases found in Trash.', 'skalum'),
  ];

  register_post_type('case', [
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_in_rest' => true,

    // archive /cases
    'has_archive' => 'cases',
    'rewrite' => ['slug' => 'cases', 'with_front' => false],

    'menu_icon' => 'dashicons-portfolio',
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
    'hierarchical' => false,
  ]);

  register_taxonomy('case_category', ['case'], [
    'label' => __('Case Categories', 'skalum'),
    'public' => true,
    'hierarchical' => true,
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'case-category', 'with_front' => false],
  ]);

}, 0);

/**
 * Cache card data from the header block into post meta for fast archive rendering.
 */
add_action('save_post_case', function ($post_id, $post, $update) {

  if (wp_is_post_revision($post_id))
    return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  if (!current_user_can('edit_post', $post_id))
    return;

  // Safety: if helper not available for some reason, don't fatal.
  if (!function_exists('skalum_get_acf_block_data'))
    return;

  $data = skalum_get_acf_block_data((int) $post_id, 'acf/skalum-header-case-block');

  if (empty($data) || !is_array($data)) {
    delete_post_meta($post_id, '_case_card_data');
    return;
  }

  /**
   * NOTE:
   * Depending on ACF serialization, block data can be nested (client_info => [...])
   * or flattened (client_info_name, client_info_website, ...).
   * We support both.
   */
  $client = $data['client_info'] ?? null;

  $client_name = '';
  $website = '';
  $logo_id = 0;
  $services = null;

  if (is_array($client)) {
    $client_name = (string) ($client['name'] ?? '');
    $website = (string) ($client['website'] ?? '');
    $logo_id = (int) ($client['client_logo'] ?? 0);
    $services = $client['services'] ?? null;
  } else {
    $client_name = (string) ($data['client_info_name'] ?? '');
    $website = (string) ($data['client_info_website'] ?? '');
    $logo_id = (int) ($data['client_info_client_logo'] ?? 0);
    $services = $data['client_info_services'] ?? null;

  }
  $excerpt = wp_strip_all_tags($post->post_excerpt);
  $payload = [
    'client_name' => $client_name,
    'website' => $website,
    'logo_id' => $logo_id,
    'services' => $services,
    'excerpt' => $excerpt,
  ];

  update_post_meta($post_id, '_case_card_data', $payload);

}, 10, 3);
