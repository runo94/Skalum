<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('contact-form-block-' . $block['id']);
$class = 'contact-form-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/** Top content */
$title = get_field('title');
$description = get_field('description');
$block_name = get_field('block_name');

/** Groups */
$planet = get_field('planet_part');   // [image, email, phone, office]
$contact = get_field('contact_part');  // [title, description, contact_form]

$email = get_field('contact_email', 'option');
$phone = get_field('contact_phone', 'option');
$office = get_field('contact_office', 'option');
$image = get_field('contact_image', 'option');
?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="contact-form-block__inner fade-in">

      <?php if ($block_name): ?>
        <div class="bn-wrapper">
          <div class="contact-form-block__name"><?php echo esc_html($block_name); ?></div>
        </div>
      <?php endif; ?>

      <?php if ($title): ?>
        <div class="contact-form-block__title"><?php echo wp_kses_post($title); ?></div>
      <?php endif; ?>

      <?php if ($description): ?>
        <div class="contact-form-block__text"><?php echo wp_kses_post($description); ?></div>
      <?php endif; ?>

      <div class="contact-form-block__grid">
        <?php
        /** LEFT: Planet part */
        ?>
          <aside class="contact-form-block__planet">
            <?php if (!empty($image)): ?>
              <div class="contact-form-block__planet-media">
                <?php echo wp_get_attachment_image((int) $image, 'large', false, ['class' => 'contact-form-block__planet-img']); ?>
              </div>
            <?php endif; ?>

            <div class="contact-form-block__planet-list">
              <?php if (!empty($email)): ?>
                <div class="contact-form-block__planet-item contact-form-block__planet-item--email">
                  <span class="contact-form-block__planet-label"><?php esc_html_e('Email', 'skalum'); ?></span>
                  <a class="contact-form-block__planet-link" href="mailto:<?php echo esc_attr($email); ?>">
                    <?php echo esc_html($email); ?>
                  </a>
                </div>
              <?php endif; ?>

              <?php if (!empty($phone)):
                $tel_href = preg_replace('/[^+\d]/', '', (string) $phone);
                ?>
                <div class="contact-form-block__planet-item contact-form-block__planet-item--phone">
                  <span class="contact-form-block__planet-label"><?php esc_html_e('Phone', 'skalum'); ?></span>
                  <a class="contact-form-block__planet-link" href="tel:<?php echo esc_attr($tel_href); ?>">
                    <?php echo esc_html($phone); ?>
                  </a>
                </div>
              <?php endif; ?>

              <?php if (!empty($office)): ?>
                <div class="contact-form-block__planet-item contact-form-block__planet-item--office">
                  <span class="contact-form-block__planet-label"><?php esc_html_e('Office', 'skalum'); ?></span>
                  <span class="contact-form-block__planet-value">
                    <?php echo esc_html($office); ?>
                  </span>
                </div>
              <?php endif; ?>
            </div>
          </aside>

        <?php
        /** RIGHT: Contact part (title, description, form) */
        $contact_title = $contact['title'] ?? '';
        $contact_description = $contact['description'] ?? '';
        $form_id = $contact['contact_form'] ?? 0;
        ?>
        <div class="contact-form-block__contact">
          <?php if ($contact_title): ?>
            <h3 class="contact-form-block__contact-title"><?php echo esc_html($contact_title); ?></h3>
          <?php endif; ?>

          <?php if ($contact_description): ?>
            <div class="contact-form-block__contact-text">
              <?php echo wp_kses_post($contact_description); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($form_id)): ?>
            <div class="contact-form-block__form">
              <?php
              // Ninja Forms shortcode render:
              echo do_shortcode('[ninja_form id="' . (int) $form_id . '"]');
              ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>