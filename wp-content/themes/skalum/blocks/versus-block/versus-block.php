<?php
/**
 * Versus block template
 *
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('versus-block-' . $block['id']);
$class = 'versus-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

// Якщо це поле у тебе є глобально/в іншій групі — лишай.
// Якщо ні — прибери, щоб не було зайвих викликів.
$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

$title   = get_field('title');
$columns = get_field('columns');

if (empty($columns) || !is_array($columns)) {
  return;
}

// Порахуємо максимальну кількість items серед усіх колонок,
// щоб рендерити рівні рядки (як таблиця).
$max_rows = 0;
foreach ($columns as $col) {
  $items = isset($col['items']) && is_array($col['items']) ? $col['items'] : [];
  $max_rows = max($max_rows, count($items));
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">

    <?php if (!empty($title)): ?>
      <div class="versus-block__title fade-in">
        <?php echo wp_kses_post($title); ?>
      </div>
    <?php endif; ?>

    <div class="versus-block__table-wrap fade-in">
      <div
        class="versus-block__table"
        style="--versus-cols: <?php echo (int) count($columns); ?>;"
        role="table"
        aria-label="<?php echo esc_attr(wp_strip_all_tags($title ?: 'Versus')); ?>"
      >
        <!-- Header -->
        <div class="versus-block__head" role="row">
          <?php foreach ($columns as $col): ?>
            <div class="versus-block__head-cell" role="columnheader">
              <?php echo esc_html($col['column_title'] ?? ''); ?>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Body -->
        <div class="versus-block__body" role="rowgroup">
          <?php for ($i = 0; $i < $max_rows; $i++): ?>
            <div class="versus-block__row" role="row">
              <?php foreach ($columns as $col): ?>
                <?php
                  $items = isset($col['items']) && is_array($col['items']) ? $col['items'] : [];
                  $item  = $items[$i] ?? null;

                  $icon = $item['icon'] ?? null; // image array (ACF return_format=array)
                  $desc = $item['description'] ?? '';
                ?>
                <div class="versus-block__cell" role="cell">
                  <?php if (!empty($item)): ?>
                    <div class="versus-block__item">
                      <?php if (!empty($icon['ID'])): ?>
                        <div class="versus-block__icon">
                          <?php
                            echo wp_get_attachment_image(
                              (int) $icon['ID'],
                              'thumbnail',
                              false,
                              [
                                'class' => 'versus-block__icon-img',
                                'loading' => 'lazy',
                                'decoding' => 'async',
                                'alt' => esc_attr($icon['alt'] ?? ''),
                              ]
                            );
                          ?>
                        </div>
                      <?php endif; ?>

                      <?php if ($desc !== ''): ?>
                        <div class="versus-block__text">
                          <?php echo esc_html($desc); ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endfor; ?>
        </div>

      </div>
    </div>

  </div>
</section>
