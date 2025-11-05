<?php
/**
 * Block: FAQ
 */
$id = $block['anchor'] ?? 'faq-' . $block['id'];
$class = 'faq-block' . (!empty($block['className']) ? ' ' . $block['className'] : '');

$block_name = get_field('block_name') ?: 'FAQ';
$title = get_field('title');
$faq_list = get_field('faq_list');
?>

<section id="<?= esc_attr($id) ?>" class="<?= esc_attr($class) ?>">
  <div class="container">
    <div class="faq-block__inner">

      <div class="faq-block__header">
        <?php if ($block_name): ?>
          <span class="faq-block__name"><?= esc_html($block_name) ?></span>
        <?php endif; ?>

        <?php if ($title): ?>
          <div class="faq-block__title"><?= wp_kses_post($title) ?></div>
        <?php endif; ?>
      </div>

      <div class="faq-block__list">
        <?php if ($faq_list): ?>
          <?php foreach ($faq_list as $index => $item): ?>
            <div class="faq-item">
              <button class="faq-item__question" aria-expanded="false">
                <span><?= esc_html($item['question']) ?></span>
                <span class="faq-item__icon">
                  <svg viewBox="0 0 24 24"><path d="M12 8v8m-4-4h8"/></svg>
                </span>
              </button>
              <div class="faq-item__answer">
                <div class="faq-item__answer-inner">
                  <?= wp_kses_post($item['answer']) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>