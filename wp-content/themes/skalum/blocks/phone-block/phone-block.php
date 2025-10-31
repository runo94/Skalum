<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('phone-block-' . $block['id']);
$class = 'phone-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';


/** Content */
$title = get_field('title');
$description = get_field('description');
$cta = get_field('cta');
$block_name = get_field('block_name');
$image_position = get_field('image_position');
// $list = get_field('list');


?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="phone-block__inner <?php echo $image_position ?>">

      <div class="phone-block__image">
        <canvas id="pushes" width="404" height="621" style="width:404px;height:621px;display:block"></canvas>
        <div id="phone_time"></div>
        <div id="phone_date"></div>
      </div>

      <div class="phone-block__content">

        <?php if ($block_name): ?>
          <div class="phone-block__name"><?php echo esc_html($block_name); ?></div>
        <?php endif; ?>

        <?php if ($title): ?>
          <div class="phone-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if ($description): ?>
          <div class="phone-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>

        <?php if (have_rows('info_list')): ?>
          <ul class="phone-block__list">
            <?php while (have_rows('info_list')):
              the_row();
              $text = get_sub_field('text');
              ?>
              <li class="phone-block__item">
                <?php if ($text): ?>
                  <?php echo esc_html($text); ?>
                <?php endif; ?>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php endif; ?>


        <?php
        if (!empty($cta) && is_array($cta)) {
          $label = trim($cta['cta_label'] ?? '');
          $url = trim($cta['cta_url'] ?? '');
          $target = !empty($cta['cta_target']) ? '_blank' : '_self';

          if ($label && $url): ?>
            <div class="phone-block__actions">
              <a class="btn" href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>">
                <?php echo esc_html($label); ?>
              </a>
            </div>
          <?php endif;
        }
        ?>
      </div>
    </div>
  </div>
</section>