<?php
$theme_uri = get_template_directory_uri();
$year = date_i18n('Y');
$site_name = get_bloginfo('name');

$opts = get_field('socials', 'option') ?: [];
$links = [
  'instagram' => ['icon' => $theme_uri . '/assets/img/insta.svg', 'label' => 'Instagram'],
  'facebook' => ['icon' => $theme_uri . '/assets/img/fb.svg', 'label' => 'Facebook'],
  'linkedin' => ['icon' => $theme_uri . '/assets/img/mdi_linkedin.svg', 'label' => 'LinkedIn'],
  'whatsapp' => ['icon' => $theme_uri . '/assets/img/whats.svg', 'label' => 'WhatsApp'],
];

$social = [];
foreach ($links as $key => $meta) {
  $row = $opts[$key] ?? null;                    // ACF link array або null
  if (!empty($row['url'])) {
    $social[] = [
      'url' => $row['url'],
      'target' => $row['target'] ?: '_blank',
      'label' => $meta['label'],
      'icon' => $meta['icon'],
    ];
  }
}
?>

<footer class="site-footer">
  <div class="container">

    <div class="site-footer__top">
      <div class="site-footer__brand">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__logo">
          <?php echo esc_html($site_name); ?>
        </a>
      </div>

      <?php if ($social): ?>
        <ul class="site-footer__social" role="list">
          <?php foreach ($social as $s): ?>
            <li>
              <a class="socbtn" href="<?php echo esc_url($s['url']); ?>" target="<?php echo esc_attr($s['target']); ?>"
                rel="noopener" aria-label="<?php echo esc_attr($s['label']); ?>">
                <img src="<?php echo esc_url($s['icon']); ?>" alt="" loading="lazy" decoding="async">
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <hr class="site-footer__divider" aria-hidden="true">

    <div class="site-footer__bottom">
      <div class="site-footer__copy">
        <?php printf(esc_html__('Copyright © %1$s | All Rights Reserved', 'skalum'), esc_html($year)); ?>
      </div>

      <nav class="site-footer__legal" aria-label="<?php esc_attr_e('Legal links', 'skalum'); ?>">
        <?php
        if (has_nav_menu('footer')) {
          wp_nav_menu([
            'theme_location' => 'footer',
            'container' => false,
            'menu_class' => 'site-footer__legal-list',
            'items_wrap' => '<ul role="list" class="%2$s">%3$s</ul>',
            'depth' => 1,
            'fallback_cb' => false,
            'link_before' => '<span>',
            'link_after' => '</span>',
          ]);
        } else {
          echo '<ul role="list" class="site-footer__legal-list">';
          echo '<li><a href="#">' . esc_html__('Terms and conditions', 'skalum') . '</a></li>';
          echo '<li><a href="#">' . esc_html__('Privacy policy', 'skalum') . '</a></li>';
          echo '<li><a href="#">' . esc_html__('Legal Notice', 'skalum') . '</a></li>';
          echo '</ul>';
        }
        ?>
      </nav>
    </div>

  </div>
</footer>
<?php get_template_part('template-parts/footer/site', 'footer'); ?>
<?php wp_footer(); ?>
</body>

</html>