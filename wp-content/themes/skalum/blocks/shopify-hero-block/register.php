<?php

if (!defined('ABSPATH')) {
	exit;
}

add_action('acf/init', 'skalum_register_shopify_hero_block');

function skalum_register_shopify_hero_block()
{
	if (!function_exists('acf_register_block_type')) {
		return;
	}

	acf_register_block_type([
		'name' => 'shopify-hero-block',
		'title' => __('Shopify Hero Block', 'skalum'),
		'description' => __('Shopify hero section with CTA and trust blocks.', 'skalum'),
		'render_template' => get_template_directory() . '/blocks/shopify-hero-block/shopify-hero-block.php',
		'category' => 'formatting',
		'icon' => 'cover-image',
		'keywords' => ['shopify', 'hero', 'skalum'],
		'mode' => 'preview',
		'supports' => [
			'align' => false,
			'anchor' => true,
		],
		'enqueue_assets' => function () {
			$css_path = get_template_directory() . '/blocks/shopify-hero-block/assets/css/shopify-hero-block.css';
			$ver = wp_get_theme()->get('Version');
			$base = get_stylesheet_directory_uri() . '/blocks/shopify-hero-block/assets';

			wp_enqueue_style(
				'skalum-shopify-hero-block',
				get_template_directory_uri() . '/blocks/shopify-hero-block/assets/css/shopify-hero-block.css',
				[],
				file_exists($css_path) ? filemtime($css_path) : null
			);

			// Фронтовий JS не потрібен у редакторі — див. skalum_is_editor_render().
			// particles.js особливо: він крутить canvas-анімацію в прев'ю.
			if (skalum_is_editor_render()) {
				return;
			}

			$particles = "$base/js/particles.min.js";
			$script = "$base/js/shopify-hero-block.min.js";

			if (file_exists(get_stylesheet_directory() . '/blocks/shopify-hero-block/assets/js/particles.min.js')) {
				wp_enqueue_script('particles-js', $particles, [], $ver, true);
			}

			if (file_exists(get_stylesheet_directory() . '/blocks/shopify-hero-block/assets/js/shopify-hero-block.min.js')) {
				wp_enqueue_script('skalum-shopify-hero-block', $script, ['particles-js'], $ver, true);
			}
		},
	]);
}
