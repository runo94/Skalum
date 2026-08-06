<?php
/**
 * Card label — a link to the case page when `case_link` is filled.
 *
 * @var array $detail_case Row of the `cases` repeater.
 */

$label_text = $detail_case['case_label'] ?? '';
$label_attrs = skalum_link_attrs($detail_case['case_link'] ?? null);

if (!$label_text) {
    return;
}
?>

<?php if ($label_attrs): ?>
  <a class="case-detail__label case-detail__label--link"<?= $label_attrs ?>>
    <span class="dot"></span>
    <?= esc_html($label_text) ?>
  </a>
<?php else: ?>
  <div class="case-detail__label">
    <span class="dot"></span>
    <?= esc_html($label_text) ?>
  </div>
<?php endif; ?>
