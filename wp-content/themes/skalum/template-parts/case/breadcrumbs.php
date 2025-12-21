<?php
defined('ABSPATH') || exit;

$post_id = $args['post_id'] ?? get_the_ID();

$home_label  = __('Home', 'your-textdomain');
$cases_label = __('Cases', 'your-textdomain');

$home_url  = home_url('/');
$cases_url = get_post_type_archive_link('case');

echo '<nav class="breadcrumbs" aria-label="Breadcrumbs">';

//
// 1) SEO plugins (if available)
//
if (function_exists('yoast_breadcrumb')) {
  // Yoast prints HTML by itself
  yoast_breadcrumb('<div class="breadcrumbs__inner">', '</div>');
  echo '</nav>';
  return;
}

if (function_exists('rank_math_the_breadcrumbs')) {
  echo '<div class="breadcrumbs__inner">';
  rank_math_the_breadcrumbs();
  echo '</div></nav>';
  return;
}

if (function_exists('seopress_display_breadcrumbs')) {
  echo '<div class="breadcrumbs__inner">';
  seopress_display_breadcrumbs();
  echo '</div></nav>';
  return;
}

//
// 2) Fallback: Home → Cases → Current
//
echo '<ol class="breadcrumbs__list">';
echo '<li class="breadcrumbs__item"><a href="' . esc_url($home_url) . '">' . esc_html($home_label) . '</a></li>';

if ($cases_url) {
  echo '<li class="breadcrumbs__item"><a href="' . esc_url($cases_url) . '">' . esc_html($cases_label) . '</a></li>';
}

// (Опційно) вставити першу категорію між Cases і кейсом
$terms = get_the_terms($post_id, 'case_category');
if (!empty($terms) && !is_wp_error($terms)) {
  $primary = $terms[0];
  $term_link = get_term_link($primary);
  if (!is_wp_error($term_link)) {
    echo '<li class="breadcrumbs__item"><a href="' . esc_url($term_link) . '">' . esc_html($primary->name) . '</a></li>';
  }
}

echo '</ol>';
echo '</nav>';
