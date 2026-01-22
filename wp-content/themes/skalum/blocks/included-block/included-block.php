<?php
/**
 * BLOCK: Included block
 * Fields:
 * - border_top (true_false)
 * - title (text)
 * - list (repeater): icon (image array), text (text)
 *
 * @var array $block WP block settings.
 */
if (!defined('ABSPATH')) exit;

$id = $block['anchor'] ?? ('included-block-' . $block['id']);
$class = 'included-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$border_top = (bool) get_field('border_top');
$title = (string) (get_field('title') ?: '');
$list = get_field('list') ?: [];

if ($border_top) {
  $class .= ' included-block--border-top';
}

$items = [];
foreach ($list as $row) {
  $text = trim((string)($row['text'] ?? ''));
  $icon = $row['icon'] ?? null;
  if ($text === '') continue;

  $items[] = [
    'text' => $text,
    'icon' => is_array($icon) ? $icon : null,
  ];
}

if ($title === '' && empty($items)) return;
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="included-block__inner fade-in">
      <?php if ($title !== ''): ?>
        <div class="included-block__title">
          <?php echo esc_html($title); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($items)): ?>
        <ul class="included-block__list" role="list">
          <?php foreach ($items as $it): ?>
            <li class="included-block__item">
              <span class="included-block__icon" aria-hidden="true">
                <?php if (!empty($it['icon']['url'])): ?>
                  <img
                    src="<?php echo esc_url($it['icon']['url']); ?>"
                    alt=""
                    loading="lazy"
                    decoding="async"
                  />
                <?php else: ?>
                  <!-- fallback check (SVG) -->
                  <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                    <path d="M9.2 16.6 4.9 12.3l1.4-1.4 2.9 2.9 8.5-8.5 1.4 1.4-9.9 9.9z" fill="currentColor"/>
                  </svg>
                <?php endif; ?>
              </span>

              <span class="included-block__text">
                <?php echo esc_html($it['text']); ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</section>
