<?php
/**
 * Template: Skalum – Team Block
 * Block name: acf/skalum-team-block
 */
if (!defined('ABSPATH')) exit;

/** @var array $block */
$id = $block['anchor'] ?? ('team-block-' . $block['id']);
$class = 'team-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';


/** Fields */
$title        = get_field('title');              // WYSIWYG
$block_name   = get_field('block_name');         // text (бейдж угорі)
$members_rep  = get_field('team_membres');       // repeater
$descriptions = get_field('descriptions');       // group: large(textarea), small(wysiwyg)

/** helper: initials for placeholder */
if (!function_exists('skalum_initials')) {
    function skalum_initials($name) {
        $parts = preg_split('/\s+/u', trim((string)$name));
        $ini = '';
        foreach ($parts as $p) { 
            if ($p !== '') { 
                $ini .= mb_strtoupper(mb_substr($p, 0, 1)); 
            } 
        }
        return mb_substr($ini, 0, 2);
    }
}
?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>" data-block="<?php echo esc_attr($block_name ?: 'Team'); ?>">
  <div class="container">
    <div class="team-block__inner">

      <header class="team-block__header">
        <?php if (!empty($block_name)): ?>
          <div class="team-block__name"><?php echo esc_html($block_name); ?></div>
        <?php endif; ?>

        <?php if ($title): ?>
          <div class="team-block__title"><?php echo wp_kses_post($title); ?></div>
        <?php endif; ?>
      </header>

      <?php if (!empty($members_rep) && is_array($members_rep)): ?>
        <ul class="team-grid" role="list">
          <?php foreach ($members_rep as $row): 
            $g = $row['team_member'] ?? [];
            $photo = $g['photo'] ?? null;
            $name  = $g['name'] ?? '';
            $pos   = $g['position'] ?? '';
            ?>
            <li class="team-card">
              <figure class="team-card__figure">
                <div class="team-card__image">
                  <?php if ($photo && !empty($photo['ID'])): 
                    echo wp_get_attachment_image(
                      $photo['ID'],
                      'large',
                      false,
                      [
                        'class'    => 'team-card__img',
                        'loading'  => 'lazy',
                        'decoding' => 'async',
                        // ширини для responsive
                        'sizes'    => '(max-width: 900px) 45vw, (max-width: 1280px) 22vw, 200px',
                      ]
                    );
                  else: ?>
                    <div class="team-card__placeholder" aria-hidden="true">
                      <span><?php echo esc_html(skalum_initials($name)); ?></span>
                    </div>
                  <?php endif; ?>
                </div>
                <figcaption class="team-card__caption">
                  <?php if ($name): ?><h3 class="team-card__name"><?php echo esc_html($name); ?></h3><?php endif; ?>
                  <?php if ($pos):  ?><p class="team-card__role"><?php echo esc_html($pos); ?></p><?php endif; ?>
                </figcaption>
              </figure>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php
        $large = $descriptions['large'] ?? '';
        $small = $descriptions['small'] ?? '';
        if ($large || $small):
      ?>
        <div class="team-desc">
          <?php if ($large): ?>
            <div class="team-desc__left">
              <p><?php echo wp_kses_post($large); ?></p>
            </div>
          <?php endif; ?>
          <?php if ($small): ?>
            <div class="team-desc__right">
              <?php echo wp_kses_post($small); ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
