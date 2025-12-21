<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('title-block-' . $block['id']);
$class = 'title-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');


$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/** Content */
$title       = get_field('title');
$description = get_field('description');
$block_name  = (string) (get_field('block_name') ?: '');

$position = (string) (get_field('position') ?: 'center');

$allowed_positions = ['left', 'center', 'right'];
if (!in_array($position, $allowed_positions, true)) {
  $position = 'center';
}

$class .= ' title-block--' . $position;
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="title-block__inner fade-in">
      <div class="title-block__content">

        <?php if ($block_name !== ''): ?>
          <div class="title-block__name"><?php echo esc_html($block_name); ?></div>
        <?php endif; ?>

        <?php if (!empty($title)): ?>
          <div class="title-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if (!empty($description)): ?>
          <div class="title-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>
