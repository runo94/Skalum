<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('transparent-block-' . $block['id']);
$class = 'transparent-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';


/** Content */
$title = get_field('title');
$description = get_field('description');
$cta = get_field('cta');
$block_name = get_field('block_name');

$bg_d_id = get_field('bg_desktop');
$bg_m_id = get_field('bg_mobile');
$bg_pos  = get_field('bg_position') ?: 'center center';
$bg_size = get_field('bg_size') ?: 'cover';

$bg_d = $bg_d_id ? wp_get_attachment_image_url($bg_d_id, 'full') : '';
$bg_m = $bg_m_id ? wp_get_attachment_image_url($bg_m_id, 'full') : '';

/** CTA parsed */
$cta_url    = is_array($cta) && !empty($cta['url']) ? $cta['url'] : '';
$cta_title  = is_array($cta) && !empty($cta['title']) ? $cta['title'] : '';
$cta_target = is_array($cta) && !empty($cta['target']) ? $cta['target'] : '_self';

?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="transparent-block__inner">


      <div class="transparent-block__content">

        <?php if ($title): ?>
          <div class="transparent-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if ($description): ?>
          <div class="transparent-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>


          <?php if ($cta_url && $cta_title) : ?>
            <div class="transparent-block__actions">
              <a
                class="btn transparent-block__cta"
                href="<?php echo esc_url($cta_url); ?>"
                target="<?php echo esc_attr($cta_target); ?>"
                rel="<?php echo esc_attr($cta_target === '_blank' ? 'noopener' : ''); ?>"
              >
                <?php echo esc_html($cta_title); ?>
              </a>
            </div>
          <?php endif; ?>

      </div>
    </div>
  </div>
  <div id="stars"></div>
</section>

<?php if ($bg_d || $bg_m): ?>
  <style>
    #<?php echo esc_js(text: $id); ?> {
      <?php if ($bg_d): ?>
        background-image: url('<?php echo esc_url($bg_d); ?>');
      <?php endif; ?>
      background-position: <?php echo esc_html($bg_pos); ?>;
      background-repeat: no-repeat;
      background-size: <?php echo esc_html($bg_size); ?>;
    }

    <?php if ($bg_m): ?>
    @media (max-width: 767px) {
      #<?php echo esc_js($id); ?> {
        background-image: url('<?php echo esc_url($bg_m); ?>');
      }
    }
    <?php endif; ?>
  </style>
<?php endif; ?>