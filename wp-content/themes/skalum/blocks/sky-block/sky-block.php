<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('sky-block-' . $block['id']);
$class = 'sky-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

$title = get_field('sky-block_title');
$text = get_field('sky-block_text');
$image = get_field('sky-block_image');
$cta = get_field('sky-block_cta');

$bg_d_id = get_field('bg_desktop');
$bg_m_id = get_field('bg_mobile');
$bg_pos = get_field('bg_position') ?: 'center center';
$bg_size = get_field('bg_size') ?: 'cover';

$bg_d = $bg_d_id ? wp_get_attachment_image_url($bg_d_id, 'full') : '';
$bg_m = $bg_m_id ? wp_get_attachment_image_url($bg_m_id, 'full') : '';
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="sky-bg">
    <div class="container">
      <div class="sky-block__inner fade-in">

        <div class="sky-block__content">
          <?php if ($title): ?>
            <div class="sky-block__title"><?php echo wp_kses_post($title); ?></div>
          <?php endif; ?>

          <?php if ($text): ?>
            <div class="sky-block__text"><?php echo wp_kses_post($text); ?></div>
          <?php endif; ?>

          <?php if (!empty($cta)):
            $label = $cta['sky-block_cta_label'] ?? '';
            $url = $cta['sky-block_cta_url'] ?? '';
            $target = !empty($cta['sky-block_cta_target']) ? '_blank' : '_self';
            if ($label && $url): ?>
              <div class="sky-block__actions">
                <a class="btn" href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>">
                  <?php echo esc_html($label); ?>
                </a>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <?php if (!empty($image)): ?>
          <figure class="sky-block__media">
            <?php echo wp_get_attachment_image($image, 'full', false, ['loading' => 'lazy']); ?>
          </figure>
        <?php endif; ?>

      </div>
    </div>
  </div>
  <div id="particles-js"></div>
</section>

<?php if ($bg_d || $bg_m): ?>
  <style>
    #<?php echo esc_attr($id); ?> .sky-bg {
      <?php if ($bg_d): ?>
        background-image: url('<?php echo esc_url($bg_d); ?>');
      <?php endif; ?>
      background-position:
        <?php echo esc_html($bg_pos); ?>
      ;
      background-repeat: no-repeat;
      background-size:
        <?php echo esc_html($bg_size); ?>
      ;
      z-index: 2;
      position: relative;
    }

    <?php if ($bg_m): ?>
      @media (max-width: 767px) {
        #<?php echo esc_attr($id); ?> .sky-bg {
          background-image: url('<?php echo esc_url($bg_m); ?>');
          position: relative;
        }
      }

    <?php endif; ?>
  </style>
<?php endif; ?>