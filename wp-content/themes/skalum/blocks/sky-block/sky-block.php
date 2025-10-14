<?php
/**
 * @var array $block WP block settings.
 */
$id    = $block['anchor'] ?? ('sky-block-' . $block['id']);
$class = 'sky-block';
$class .= !empty($block['className']) ? ' ' . $block['className'] : '';
$class .= !empty($block['align']) ? ' align' . $block['align'] : '';

$title    = get_field('sky-block_title');
$text     = get_field('sky-block_text');
$image    = get_field('sky-block_image');
$cta     = get_field('sky-block_cta'); 

?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?> layout-<?php echo esc_attr($layout); ?>">
  <div class="container">
    <div class="sky-block__inner">
      <div class="sky-block__content">
        <?php if ($title): ?>
          <h1 class="sky-block__title"><?php echo esc_html($title); ?></h1>
        <?php endif; ?>

        <?php if ($text): ?>
          <div class="sky-block__text"><?php echo wp_kses_post($text); ?></div>
        <?php endif; ?>

        <?php if (!empty($cta)): 
              $label  = $btn['label'] ?? '';
              $url    = $btn['url'] ?? '';
              $target = !empty($btn['is_external']) ? '_blank' : '_self';?>
          <div class="sky-block__actions">
              <a class="btn" href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>">
                <?php echo esc_html($label); ?>
              </a>
          </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($image)): ?>
        <figure class="sky-block__media">
          <?php echo wp_get_attachment_image($image, 'full', false, ['loading' => 'lazy']); ?>
        </figure>
      <?php endif; ?>
    </div>
  </div>
</section>
