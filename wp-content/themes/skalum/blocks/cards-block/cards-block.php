<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? ('cards-block-' . $block['id']);
$class = 'cards-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

/** Content */
$title       = get_field('title');
$description = get_field('description');
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="cards-block__inner">

      <div class="cards-block__content">
        <?php if ($title) : ?>
          <div class="cards-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if ($description) : ?>
          <div class="cards-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
      </div>

      <?php if (have_rows('cards')) : ?>
        <div class="cards-block__grid">
          <?php while (have_rows('cards')) : the_row(); ?>
            <?php
            $icon        = get_sub_field('icon');        // image array
            $card_title  = (string) get_sub_field('title');
            $card_desc   = get_sub_field('description'); // wysiwyg (can contain lists)
            ?>
            <article class="cards-block__card">
              <header class="cards-block__card-head">
                <?php if (!empty($icon['ID'])) : ?>
                  <?php
                  echo wp_get_attachment_image(
                    $icon['ID'],
                    'full',
                    false,
                    [
                      'class' => 'cards-block__icon',
                      'alt'   => esc_attr($icon['alt'] ?: $card_title),
                      'loading' => 'lazy',
                      'decoding' => 'async',
                    ]
                  );
                  ?>
                <?php endif; ?>

                <?php if ($card_title) : ?>
                  <div class="cards-block__card-title"><?php echo esc_html($card_title); ?></div>
                <?php endif; ?>
              </header>

              <?php if ($card_desc) : ?>
                <div class="cards-block__card-body">
                  <?php echo wp_kses_post($card_desc); ?>
                </div>
              <?php endif; ?>
            </article>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
