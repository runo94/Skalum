<?php

if (!defined('ABSPATH')) {
	exit;
}

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'shopify-hero-block-' . $block['id'];

$class_name = 'shopify-hero-block';

if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

$background = get_field('background');
$background_url = '';

if (is_array($background) && !empty($background['url'])) {
	$background_url = $background['url'];
}

$title = get_field('title');
$subtitle = get_field('subtitle');
$cta = get_field('cta');

$cta_url = !empty($cta['url']) ? $cta['url'] : '#';
$cta_title = !empty($cta['title']) ? $cta['title'] : 'FREE STRATEGY CALL';
$cta_target = !empty($cta['target']) ? $cta['target'] : '_self';

$bages = get_field('bages');
$trusted_block_1 = get_field('trusted_block_1');
$trusted_block_2 = get_field('trusted_block_2');
$trusted_block_3 = get_field('trusted_block_3');
$trusted_block_4 = get_field('trusted_block_4');

$title_html = esc_html($title);


$section_style = $background_url ? ' style="--shopify-hero-bg: url(' . esc_url($background_url) . ');"' : '';

$render_trust_cards = static function ($trusted_block_1, $trusted_block_2, $trusted_block_3, $trusted_block_4) {
	?>
	<?php if (!empty($trusted_block_1)) : ?>
		<article class="shopify-hero-card shopify-hero-card--upwork">
			<div class="shopify-hero-card__top">
				<?php if (!empty($trusted_block_1['name'])) : ?>
					<div class="shopify-hero-card__label"><?php echo esc_html($trusted_block_1['name']); ?></div>
				<?php endif; ?>

				<div class="shopify-hero-card__stars" aria-label="5 stars">★★★★★</div>
			</div>

			<?php if (!empty($trusted_block_1['logo'])) : ?>
				<div class="shopify-hero-card__logo">
					<?php echo wp_get_attachment_image($trusted_block_1['logo'], 'medium', false, ['loading' => 'lazy']); ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($trusted_block_1['title'])) : ?>
				<div class="shopify-hero-card__score"><?php echo esc_html($trusted_block_1['title']); ?></div>
			<?php endif; ?>

			<?php if (!empty($trusted_block_1['labels'])) : ?>
				<div class="shopify-hero-card__text">
					<?php echo esc_html(implode(' · ', array_filter(array_column($trusted_block_1['labels'], 'label')))); ?>
				</div>
			<?php endif; ?>
		</article>
	<?php endif; ?>

	<?php if (!empty($trusted_block_2)) : ?>
		<article class="shopify-hero-card shopify-hero-card--shopify">
			<div class="shopify-hero-card__top">
				<div class="shopify-hero-card__brand">
					<?php if (!empty($trusted_block_2['logo'])) : ?>
						<?php echo wp_get_attachment_image($trusted_block_2['logo'], 'thumbnail', false, ['loading' => 'lazy']); ?>
					<?php endif; ?>

					<?php if (!empty($trusted_block_2['name'])) : ?>
						<div class="shopify-hero-card__label"><?php echo esc_html($trusted_block_2['name']); ?></div>
					<?php endif; ?>
				</div>

				<?php if (!empty($trusted_block_2['rating']) || !empty($trusted_block_2['reviews_count'])) : ?>
					<div class="shopify-hero-card__rating">
						<?php echo esc_html(trim(($trusted_block_2['rating'] ?? '') . ' · ' . ($trusted_block_2['reviews_count'] ?? ''), ' ·')); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if (!empty($trusted_block_2['description'])) : ?>
				<div class="shopify-hero-card__description">
					<?php echo esc_html($trusted_block_2['description']); ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($trusted_block_2['markets'])) : ?>
				<div class="shopify-hero-card__markets">
					<?php echo esc_html(implode(' · ', array_filter(array_column($trusted_block_2['markets'], 'name')))); ?>
				</div>
			<?php endif; ?>
		</article>
	<?php endif; ?>

	<?php foreach ([$trusted_block_3, $trusted_block_4] as $trusted_block) :
		if (empty($trusted_block)) {
			continue;
		}
	?>
		<article class="shopify-hero-card shopify-hero-card--stat">
			<?php if (!empty($trusted_block['label'])) : ?>
				<div class="shopify-hero-card__label"><?php echo esc_html($trusted_block['label']); ?></div>
			<?php endif; ?>

			<?php if (!empty($trusted_block['count'])) : ?>
				<div class="shopify-hero-card__count"><?php echo esc_html($trusted_block['count']); ?></div>
			<?php endif; ?>

			<?php if (!empty($trusted_block['text'])) : ?>
				<div class="shopify-hero-card__text"><?php echo esc_html($trusted_block['text']); ?></div>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
	<?php
};
?>

<section
	id="<?php echo esc_attr($block_id); ?>"
	class="<?php echo esc_attr($class_name); ?>"
	<?php echo $section_style; ?>
>
	<div class="shopify-hero-block__container">
		<div class="shopify-hero-block__content">
			<?php if ($title) : ?>
				<div class="shopify-hero-block__title">
					<?php echo wp_kses_post($title); ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($bages)) : ?>
				<div class="shopify-hero-block__bages">
					<?php foreach ($bages as $bage) :
						$label = $bage['label'] ?? '';
						$color = $bage['bage_color'] ?? '#ff8428';

						if (!$label) {
							continue;
						}
					?>
						<div class="shopify-hero-block__bage">
							<span class="shopify-hero-block__bage-dot" style="--bage-color: <?php echo esc_attr($color); ?>"></span>
							<span><?php echo esc_html($label); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="shopify-hero-block__trust shopify-hero-block__trust--mobile">
				<?php $render_trust_cards($trusted_block_1, $trusted_block_2, $trusted_block_3, $trusted_block_4); ?>
			</div>

			<?php if ($cta_title && $cta_url) : ?>
				<div class="shopify-hero-block__cta-wrap">
					<a
						href="<?php echo esc_url($cta_url); ?>"
						target="<?php echo esc_attr($cta_target); ?>"
						class="shopify-hero-block__cta"
					>
						<span><?php echo esc_html($cta_title); ?></span>
						<span class="shopify-hero-block__cta-icon" aria-hidden="true"></span>
					</a>

					<div class="shopify-hero-block__cta-meta">
						<span>No commitment</span>
						<span>30 min call</span>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($subtitle) : ?>
				<div class="shopify-hero-block__subtitle">
					<?php echo wp_kses_post($subtitle); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="shopify-hero-block__trust shopify-hero-block__trust--desktop">
			<?php $render_trust_cards($trusted_block_1, $trusted_block_2, $trusted_block_3, $trusted_block_4); ?>
		</div>
	</div>
  <div id="particles-js-shopify-hero"></div>
</section>
