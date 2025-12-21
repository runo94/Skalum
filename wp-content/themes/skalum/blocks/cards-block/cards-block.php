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

/** Variant */
$layout = (string) (get_field('layout_variant') ?: 'default');

/** Results-only extras (in your JSON it's always available, but used only in results layout) */
$badge = (string) (get_field('badge') ?: '');

/** Optional modifier class */
$class .= ($layout === 'results') ? ' cards-block--results' : ' cards-block--default';

if (!function_exists('skalum_render_cards_block_card')) {
  /**
   * Render one card.
   *
   * @param array|null $icon (ACF image array)
   * @param string     $card_title
   * @param mixed      $card_desc (wysiwyg html)
   * @param string     $list_style default|chevron
   * @param string     $extra_class optional extra class for <article>
   */
  function skalum_render_cards_block_card($icon, string $card_title, $card_desc, string $list_style = 'default', string $extra_class = ''): void {
    $article_class = 'cards-block__card';
    if ($extra_class) {
      $article_class .= ' ' . $extra_class;
    }

    // modifier for lists inside description
    if ($list_style === 'chevron') {
      $article_class .= ' cards-block__card--chevron';
    }
    ?>
    <article class="<?php echo esc_attr($article_class); ?>">
      <header class="cards-block__card-head">
        <?php if (!empty($icon['ID'])): ?>
          <?php
          echo wp_get_attachment_image(
            (int) $icon['ID'],
            'full',
            false,
            [
              'class'    => 'cards-block__icon',
              'alt'      => esc_attr(($icon['alt'] ?? '') ?: $card_title),
              'loading'  => 'lazy',
              'decoding' => 'async',
            ]
          );
          ?>
        <?php endif; ?>

        <?php if ($card_title !== ''): ?>
          <div class="cards-block__card-title"><?php echo esc_html($card_title); ?></div>
        <?php endif; ?>
      </header>

      <?php if (!empty($card_desc)): ?>
        <div class="cards-block__card-body">
          <?php echo wp_kses_post($card_desc); ?>
        </div>
      <?php endif; ?>
    </article>
    <?php
  }
}
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="cards-block__inner fade-in">

      <div class="cards-block__content">
        <?php if ($layout === 'results' && $badge !== ''): ?>
          <div class="cards-block__badge"><?php echo esc_html($badge); ?></div>
        <?php endif; ?>

        <?php if (!empty($title)): ?>
          <div class="cards-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>

        <?php if (!empty($description)): ?>
          <div class="cards-block__text"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
      </div>

      <?php if (have_rows('cards')): ?>
        <div class="cards-block__grid<?php echo ($layout === 'results') ? ' cards-block__grid--results' : ''; ?>">
          <?php while (have_rows('cards')): the_row(); ?>
            <?php
            $icon       = get_sub_field('icon');
            $card_title = (string) get_sub_field('title');
            $card_desc  = get_sub_field('description');

            // in your JSON default_value is "", so normalize it to "default"
            $list_style = (string) get_sub_field('list_style');
            $list_style = $list_style ?: 'default';

            // In results layout you were adding stat class; keep it as modifier if you need it in CSS
            $extra_class = ($layout === 'results') ? 'cards-block__card--stat' : '';

            skalum_render_cards_block_card($icon, $card_title, $card_desc, $list_style, $extra_class);
            ?>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
