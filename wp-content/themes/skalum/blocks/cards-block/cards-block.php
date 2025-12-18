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

/** New: layout variant */
$layout = (string) (get_field('layout_variant') ?: 'default');
$badge  = (string) (get_field('badge') ?: '');

/** Optional: add modifier class */
$class .= $layout === 'results' ? ' cards-block--results' : ' cards-block--default';

function skalum_render_cards_block_card($icon, string $card_title, $card_desc, string $extra_class = ''): void {
  ?>
  <article class="cards-block__card<?php echo $extra_class ? ' ' . esc_attr($extra_class) : ''; ?>">
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
  <?php
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="cards-block__inner fade-in">

      <div class="cards-block__content">
        <?php if ($layout === 'results' && !empty($badge)) : ?>
          <div class="cards-block__badge"><?php echo esc_html($badge); ?></div>
        <?php endif; ?>

        <?php if ($title) : ?>
          <div class="cards-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if ($description) : ?>
          <div class="cards-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
      </div>

      <?php if (have_rows('cards')) : ?>

        <?php if ($layout === 'default') : ?>
          <!-- ✅ CURRENT (unchanged logic) -->
          <div class="cards-block__grid">
            <?php while (have_rows('cards')) : the_row(); ?>
              <?php
              $icon       = get_sub_field('icon');
              $card_title = (string) get_sub_field('title');
              $card_desc  = get_sub_field('description');
              skalum_render_cards_block_card($icon, $card_title, $card_desc);
              ?>
            <?php endwhile; ?>
          </div>

        <?php else : ?>
          <!-- ✅ RESULTS layout: all except last are "stats", last is "wide" -->
          <?php
          // Collect rows first (so we can split last)
          $cards = [];
          while (have_rows('cards')) : the_row();
            $cards[] = [
              'icon'  => get_sub_field('icon'),
              'title' => (string) get_sub_field('title'),
              'desc'  => get_sub_field('description'),
            ];
          endwhile;

          $count = count($cards);
          $wide  = null;
          $stats = $cards;

          if ($count >= 2) {
            $wide  = $cards[$count - 1];
            $stats = array_slice($cards, 0, $count - 1);
          }
          ?>

          <div class="cards-block__grid cards-block__grid--results">

            <?php if (!empty($stats)) : ?>
              <div class="cards-block__stats">
                <?php foreach ($stats as $c) : ?>
                  <?php skalum_render_cards_block_card($c['icon'], $c['title'], $c['desc'], 'cards-block__card--stat'); ?>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($wide)) : ?>
              <?php skalum_render_cards_block_card($wide['icon'], $wide['title'], $wide['desc'], 'cards-block__card--wide'); ?>
            <?php endif; ?>

          </div>

        <?php endif; ?>

      <?php endif; ?>

    </div>
  </div>
</section>
