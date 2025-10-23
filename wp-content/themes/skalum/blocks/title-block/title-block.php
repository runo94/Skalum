<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('title-block-' . $block['id']);
$class = 'title-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

/** Content */
$title = get_field('title');
$description = get_field('description');
// $cta = get_field('cta');
$block_name = get_field('block_name');
// $list = get_field('list');


?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="title-block__inner">


      <div class="title-block__content">

        <?php if ($block_name): ?>
          <div class="title-block__name"><?php echo esc_html($block_name); ?></div>
        <?php endif; ?>

        <?php if ($title): ?>
          <div class="title-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if ($description): ?>
          <div class="title-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>