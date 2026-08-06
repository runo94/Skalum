<?php
/**
 * Card chart — a link to the case page when `case_link` is filled.
 *
 * @var array $detail_case Row of the `cases` repeater.
 */

$chart_img = wp_get_attachment_image_url($detail_case['image'] ?? 0, 'large');
$chart_attrs = skalum_link_attrs($detail_case['case_link'] ?? null);
$chart_alt = $detail_case['title'] ? sprintf('%s — chart', $detail_case['title']) : 'Chart';

if (!$chart_img) {
    return;
}
?>

<div class="case-detail__chart">
  <?php if ($chart_attrs): ?>
    <a class="case-detail__chart-link"<?= $chart_attrs ?>
      aria-label="<?= esc_attr(sprintf('View case: %s', $detail_case['title'])) ?>">
      <img src="<?= esc_url($chart_img) ?>" alt="<?= esc_attr($chart_alt) ?>">
    </a>
  <?php else: ?>
    <img src="<?= esc_url($chart_img) ?>" alt="<?= esc_attr($chart_alt) ?>">
  <?php endif; ?>
</div>
