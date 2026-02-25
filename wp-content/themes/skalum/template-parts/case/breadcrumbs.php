<?php
defined('ABSPATH') || exit;

$post_id = $args['post_id'] ?? get_the_ID();

$home_label = __('Home', 'skalum');
$blog_label = __('Blog', 'skalum');

$home_url = home_url('/');

// Blog page URL (Settings → Reading → Posts page)
$blog_id  = (int) get_option('page_for_posts');
$blog_url = $blog_id ? get_permalink($blog_id) : home_url('/blog/');

echo '<nav class="breadcrumbs" aria-label="Breadcrumbs">';

//
// 1) SEO plugins (if available)
//
if (function_exists('yoast_breadcrumb')) {
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
// 2) Fallback: Home → Blog → (Category) → Current
//
echo '<ol class="breadcrumbs__list">';

// Home
echo '<li class="breadcrumbs__item"><a href="' . esc_url($home_url) . '">' . esc_html($home_label) . '</a></li>';

// Blog
if ($blog_url) {
  echo '<li class="breadcrumbs__item"><a href="' . esc_url($blog_url) . '">' . esc_html($blog_label) . '</a></li>';
}

// (Optional) insert primary category between Blog and post
$cats = get_the_category($post_id);
if (!empty($cats) && !is_wp_error($cats)) {
  $primary = $cats[0];
  $cat_link = get_category_link($primary->term_id);

  if (!is_wp_error($cat_link)) {
    echo '<li class="breadcrumbs__item"><a href="' . esc_url($cat_link) . '">' . esc_html($primary->name) . '</a></li>';
  }
}

// Current (title) — optional, якщо хочеш показувати останнім елементом
echo '<li class="breadcrumbs__item breadcrumbs__item--current" aria-current="page">' . esc_html(get_the_title($post_id)) . '</li>';

echo '</ol>';
echo '</nav>';
