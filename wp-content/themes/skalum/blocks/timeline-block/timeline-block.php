<?php
/**
 * Template: Skalum – Timeline Block
 * Block name: acf/skalum-timeline-block
 */

if (!defined('ABSPATH'))
  exit;

/** @var array $block */
$id = $block['anchor'] ?? ('timeline-block-' . $block['id']);
$class = 'timeline-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

/** ACF fields (JSON уже налаштований під цей ключ блоку) */
$block_name = get_field('block_name');
$title = get_field('title');
$description = get_field('description');
$cards = get_field('cards'); // repeater
?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>"
  data-block="<?php echo esc_attr($block_name ?: 'Timeline'); ?>">
  <div class="container">
    <div class="timeline-block__inner">
      <div class="timeline-block__header">

        <?php if ($block_name): ?>
          <div class="title-block__name"><?php echo esc_html($block_name); ?></div>
        <?php endif; ?>

        <?php if ($title): ?>
          <div class="timeline-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>
        
        <?php if ($description): ?>
          <div class="timeline-block__description"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
      </div>

      <div class="timeline-block__line_shadow">
      <div class="timeline-block__body">
        <!-- вертикальна «шина» таймлайну -->
        <div class="tl-spine" aria-hidden="true">
          <div class="tl-line tl-line--bg"></div>
          <div class="tl-line tl-line--progress" style="height:0"></div>
        </div>

        <?php if ($cards && is_array($cards)): ?>
          <ul class="tl-list" role="list">
            <?php foreach ($cards as $i => $card): ?>
              <?php
              $item_title = $card['title'] ?? '';
              $item_desc = $card['description'] ?? '';
              $icon = $card['icon'] ?? null;

              // Альтернат: ліво/право
              $side = ($i % 2 === 0) ? 'right' : 'left';
              ?>
              <li class="tl-item tl-item--<?php echo $side; ?><?php echo $i === 0 ? ' tl-item--first' : ''; ?>">
                <?php if ($i !== 0): ?>
                  <span class="tl-gap" aria-hidden="true"></span>
                <?php endif; ?>

                <?php if ($i !== 0): ?>
                  <div class="tl-node" aria-hidden="true">

                  </div>
                <?php else: ?>
                  <div class="tl-node tl-node--hidden" aria-hidden="true"></div>
                <?php endif; ?>
                <div class="tl-node tl-node--hidden" aria-hidden="true"></div>

                <article class="tl-card">
                  <?php if ($icon && !empty($icon['ID'])): ?>
                    <div class="tl-card__icon">
                      <?php echo wp_get_attachment_image($icon['ID'], 'thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($item_title): ?>
                    <div class="tl-card__title">
                      <?php echo wp_kses_post($item_title); ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($item_desc): ?>
                    <div class="tl-card__desc">
                      <?php echo wp_kses_post($item_desc); ?>
                    </div>
                  <?php endif; ?>
                </article>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
  </div>
</section>