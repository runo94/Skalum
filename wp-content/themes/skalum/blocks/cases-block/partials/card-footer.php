<?php
/**
 * Card footer: short description + CTA button.
 *
 * @var array $footer_case Row of the `cases` repeater.
 */

$footer_text = $footer_case['card_description'] ?? '';
$footer_cta = $footer_case['card_cta'] ?? null;

if (!$footer_text && empty($footer_cta['url'])) {
    return;
}
?>

<div class="case-detail__footer">
  <?php if ($footer_text): ?>
    <div class="case-detail__footer-text"><?= wp_kses_post($footer_text) ?></div>
  <?php endif; ?>

  <?php if (!empty($footer_cta['url'])): ?>
    <a class="case-detail__more" href="<?= esc_url($footer_cta['url']) ?>"
      <?= !empty($footer_cta['target']) ? 'target="' . esc_attr($footer_cta['target']) . '" rel="noopener noreferrer"' : '' ?>>
      <span><?= esc_html($footer_cta['title'] ?: 'Read More') ?></span>
      <svg class="case-detail__more-arrow" width="20" height="12" viewBox="0 0 23 12" fill="none" aria-hidden="true"
        focusable="false" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.18896 5.99979H21.8111M21.8111 5.99979L17.2654 1.4541M21.8111 5.99979L17.2654 10.5455"
          stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </a>
  <?php endif; ?>
</div>
