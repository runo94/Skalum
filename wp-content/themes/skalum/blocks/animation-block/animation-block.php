<?php
/**
 * Template: Skalum – Animated Block
 * Block name: acf/skalum-animation-block
 */
if (!defined('ABSPATH'))
  exit;

/** @var array $block */
$id = $block['anchor'] ?? ('animation-block-' . $block['id']);
$class = 'animation-block'
  . (!empty($block['className']) ? ' ' . $block['className'] : '')
  . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile'); // у контексті шаблона
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';


/** ACF */
$start = get_field('start') ?: [];
$finish = get_field('finish') ?: [];
$animation_type = get_field('animation_type') ?: 'circles_and_bars';
$content_position = get_field('content_position') ?: 'content-right'; // content-left | content-right
$inside_image = get_field('inside_image'); // array|false

$theme_ver = wp_get_theme()->get('Version');
$assets = get_stylesheet_directory_uri() . '/blocks/animation-block/assets';
$images = $assets . '/images';

/** side renderer (closure) */
$render_side = function ($group, $mod = '') {
  if (empty($group))
    return;

  $title = $group['title'] ?? '';
  $desc = $group['description'] ?? '';
  $icon = $group['icon'] ?? null;

  echo '<div class="animation-block__side ' . esc_attr($mod ? 'animation-block__side--' . $mod : '') . '">';
  echo '<div class="animation-block__side-inner">';
  // ICON 
  if ($icon && is_array($icon)) {
    echo '<div class="animation-block__icon-wrap">';
    echo wp_get_attachment_image($icon['ID'], 'medium', false, [
      'class' => 'animation-block__icon',
      'loading' => 'lazy',
      'decoding' => 'async',
    ]);
    echo '</div>';
  }
  // TEXT 
  echo '<div class="animation-block__text">';
  if ($title)
    echo '<div class="animation-block__title">' . wp_kses_post($title) . '</div>';
  if ($desc)
    echo '<div class="animation-block__desc">' . wp_kses_post($desc) . '</div>';
  echo '</div>';
  echo '</div>'; // .animation-block__side-inner
  echo '</div>';
};

/** animation renderer (closure) */
$render_animation = function () use ($animation_type, $id, $images, $theme_ver, $inside_image) {
  ?>
  <div class="animation-block__animation">
    <div class="animation_part" data-animation-type="<?php echo esc_attr($animation_type); ?>">

      <?php if ($animation_type === 'circles_and_bars'): ?>
        <!-- ========== CARD ========== -->
        <div class="ssp-card" id="<?php echo esc_attr($id); ?>-card">
          <header class="ssp-header">
            <h3>Store Speed Performance</h3>
            <div class="ssp-delta">
              <span class="ssp-arrow">↑</span>
              <span class="ssp-delta-val">12</span><span>%</span>
              <span class="ssp-info" title="compared to previous period">i</span>
            </div>
            <div class="ssp-sub">Mobile</div>
          </header>

          <!-- GROUP: gauge + bars -->
          <div class="gc-group">
            <!-- GAUGE -->
            <div class="gauge-curtain gc">
              <img class="gc-img gc-base" src="<?php echo esc_url("$images/circle_1.svg?v=$theme_ver"); ?>" alt=""
                width="110" height="110" />
              <img class="gc-img gc-curtain-img" src="<?php echo esc_url("$images/circle_2.svg?v=$theme_ver"); ?>" alt=""
                width="110" height="110" />

              <svg class="gc-overlay" viewBox="0 0 220 220" width="220" height="220" aria-hidden="true">
                <circle class="gc-track" cx="110" cy="125" r="98" fill="none" />
                <g class="gc-needle">
                  <polygon points="0,0 8,18 -8,18" fill="#0e1117" />
                </g>
              </svg>

              <div class="gc-label" aria-live="polite">
                <span class="gc-value">54</span>
                <small>Over All</small>
              </div>
            </div>

            <!-- BARS -->
            <div class="gc-bars" aria-hidden="false">
              <div class="gc-bar" data-from="22" data-to="96">
                <span class="gc-bar-fill gc-bar-fill--base"></span>
                <span class="gc-bar-fill gc-bar-fill--hover"></span>
              </div>
              <div class="gc-bar" data-from="28" data-to="92">
                <span class="gc-bar-fill gc-bar-fill--base"></span>
                <span class="gc-bar-fill gc-bar-fill--hover"></span>
              </div>
              <div class="gc-bar" data-from="36" data-to="90">
                <span class="gc-bar-fill gc-bar-fill--base"></span>
                <span class="gc-bar-fill gc-bar-fill--hover"></span>
              </div>
            </div>
          </div>

          <!-- LIST -->
          <section class="ssp-list">
            <h4>1st October</h4>

            <div class="inner_block_list">
              <div>
                <div class="ssp-row">
                  <span class="ssp-dot"><i class="ssp-dot-base"></i><i class="ssp-dot-hover"></i></span>
                  <span class="ssp-label">Home</span>
                  <span class="ssp-metric" data-from="28" data-to="98">28</span>
                </div>
                <div class="ssp-row">
                  <span class="ssp-dot"><i class="ssp-dot-base"></i><i class="ssp-dot-hover"></i></span>
                  <span class="ssp-label">Collection</span>
                  <span class="ssp-metric" data-from="30" data-to="98">30</span>
                </div>
                <div class="ssp-row">
                  <span class="ssp-dot"><i class="ssp-dot-base"></i><i class="ssp-dot-hover"></i></span>
                  <span class="ssp-label">Product</span>
                  <span class="ssp-metric" data-from="34" data-to="89">34</span>
                </div>
              </div>

              <span class="ssp-note">
                <em class="ssp-note-slow">Your store slower than similar stores on Shopify.</em>
                <em class="ssp-note-fast">Your store is faster than similar stores on Shopify.</em>
              </span>
            </div>
          </section>
        </div>

        <?php if (!empty($inside_image) && is_array($inside_image)): ?>
          <div class="back_image">
            <?php echo wp_get_attachment_image($inside_image['ID'], 'full', false, [
              'class' => 'gc-inner',
              'loading' => 'lazy',
              'decoding' => 'async',
              'alt' => esc_attr($inside_image['alt'] ?? ''),
            ]); ?>
          </div>
        <?php endif; ?>
      <?php elseif ($animation_type === 'google_list'): ?>
        <div class="google_list_card">
          <canvas id="c" width="409" height="364" style="display:block"></canvas>
        </div>
      <?php elseif ($animation_type === 'static_image'): ?>
        <?php if (!empty($inside_image) && is_array($inside_image)): ?>
          <div class="static_image">
            <?php echo wp_get_attachment_image($inside_image['ID'], 'full', false, [
              'class' => 'gc-inner',
              'loading' => 'lazy',
              'decoding' => 'async',
              'alt' => esc_attr($inside_image['alt'] ?? ''),
            ]); ?>
          </div>
        <?php else: ?>
          <div class="ssp-card">
            <p>No image selected.</p>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <div class="ssp-card">
          <p>Animation type not implemented.</p>
        </div>
      <?php endif; ?>

    </div>
  </div>
  <?php
};

?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
  <div class="container">
    <div class="animation-block__inner <?php echo esc_attr($content_position); ?>">
      <?php $render_animation(); ?>
      <div class="animation-block__content">
        <?php $render_side($start, 'start'); ?>
        <?php $render_side($finish, 'finish'); ?>
      </div>
    </div>
  </div>
</section>