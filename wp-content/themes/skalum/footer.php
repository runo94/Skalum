<?php
$theme_uri = get_template_directory_uri();
$year = date_i18n('Y');
$site_name = get_bloginfo('name');

$current_lang = function_exists('pll_current_language')
  ? pll_current_language('slug')
  : 'en';

if (!in_array($current_lang, ['en', 'de'], true)) {
  $current_lang = 'en';
}

/**
 * Shared options
 */
$opts_socials = get_field('socials', 'option') ?: [];
$cta_link = get_field('cta_link', 'option') ?: [];
$email = get_field('email', 'option');
$phone = get_field('phone', 'option');

/**
 * Group fields
 */
$services_group = get_field('services', 'option') ?: [];
$navigations_group = get_field('navigations', 'option') ?: [];
$cases_group = get_field('cases', 'option') ?: [];

/**
 * Per-language fields from groups
 */
$footer_services = $services_group['our_services_' . $current_lang] ?? [];
$footer_navigation = $navigations_group['navigation_' . $current_lang] ?? [];
$footer_cases = $cases_group['cases_' . $current_lang] ?? [];
$footer_links = get_field('links_' . $current_lang, 'option') ?: [];

/**
 * Social icons
 */
$social_map = [
  'instagram' => ['icon' => $theme_uri . '/assets/img/insta.svg', 'label' => 'Instagram'],
  'facebook'  => ['icon' => $theme_uri . '/assets/img/fb.svg', 'label' => 'Facebook'],
  'linkedin'  => ['icon' => $theme_uri . '/assets/img/mdi_linkedin.svg', 'label' => 'LinkedIn'],
  'whatsapp'  => ['icon' => $theme_uri . '/assets/img/whats.svg', 'label' => 'WhatsApp'],
];

$socials = [];
foreach ($social_map as $key => $meta) {
  $row = $opts_socials[$key] ?? null;

  if (!empty($row['url'])) {
    $socials[] = [
      'url'    => $row['url'],
      'target' => !empty($row['target']) ? $row['target'] : '_blank',
      'label'  => $meta['label'],
      'icon'   => $meta['icon'],
    ];
  }
}

/**
 * Static headings for now
 */
$footer_titles = [
  'services'   => $current_lang === 'de' ? 'Unsere Leistungen' : 'Our Services',
  'navigation' => 'Navigation',
  'cases'      => 'Cases',
  'contact'    => $current_lang === 'de' ? 'Kontakt' : 'Get in touch',
];

/**
 * Temporary legal links fallback
 */
$legal_links = [
  [
    'title' => $current_lang === 'de' ? 'AGB' : 'Terms and conditions',
    'url'   => '#',
  ],
  [
    'title' => $current_lang === 'de' ? 'Datenschutz' : 'Privacy policy',
    'url'   => '#',
  ],
  [
    'title' => $current_lang === 'de' ? 'Impressum' : 'Legal Notice',
    'url'   => '#',
  ],
];
?>

<footer class="site-footer">
  <div class="container">
    <div class="site-footer__inner">

      <div class="site-footer__brand">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__logo">
          <?php if (has_custom_logo()): ?>
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            ?>

            <?php if (!empty($logo[0])): ?>
              <img
                src="<?php echo esc_url($logo[0]); ?>"
                alt="<?php echo esc_attr($site_name); ?>"
                class="site-footer__logo-image"
                loading="eager"
                decoding="async"
              />
            <?php else: ?>
              <span class="site-footer__brand-text"><?php echo esc_html($site_name ?: 'Skalum'); ?></span>
            <?php endif; ?>
          <?php else: ?>
            <span class="site-footer__brand-text"><?php echo esc_html($site_name ?: 'Skalum'); ?></span>
          <?php endif; ?>
        </a>
      </div>

      <div class="site-footer__main">

        <?php if (!empty($footer_services)): ?>
          <div class="site-footer__col site-footer__col--services">
            <h3 class="site-footer__title"><?php echo esc_html($footer_titles['services']); ?></h3>

            <div class="site-footer__services-grid">
              <?php foreach ($footer_services as $service): ?>
                <?php
                $service_links = $service['links'] ?? [];
                $service_title = $service['title'] ?? '';
                $service_description = $service['description'] ?? '';
                $service_icon = $service['icon'] ?? '';
                ?>

                <div class="site-footer__service-item">
                  <?php if (!empty($service_links)): ?>
                    <div class="site-footer__service-title">
                      <?php foreach ($service_links as $link):
                        ?>

                        <a
                          href="<?php echo esc_url($link['link']['url']); ?>"
                          target="<?php echo esc_attr(!empty($link['link']['target']) ? $link['link']['target'] : '_self'); ?>"
                          <?php echo !empty($link['link']['target']) && $link['link']['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                          class="site-footer__service-link"
                        >
                          <?php echo esc_html($link['link']['title'] ?: $service_title ?: 'Service'); ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  <?php elseif (!empty($service_title)): ?>
                    <div class="site-footer__service-links">
                      <span class="site-footer__service-link site-footer__service-link--static">
                        <?php echo esc_html($service_title); ?>
                      </span>
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($service_icon) || !empty($service_description)): ?>
                    <div class="site-footer__service-meta">
                      <?php if (!empty($service_icon)): ?>
                        <img
                          src="<?php echo esc_url($service_icon); ?>"
                          alt="<?php echo esc_attr($service_title ?: 'Service'); ?>"
                          class="site-footer__service-icon"
                          loading="lazy"
                          decoding="async"
                        />
                      <?php endif; ?>

                      <?php if (!empty($service_description)): ?>
                        <div class="site-footer__service-description">
                          <?php echo esc_html($service_description); ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($footer_navigation)): ?>
          <div class="site-footer__col site-footer__col--navigation">
            <h3 class="site-footer__title"><?php echo esc_html($footer_titles['navigation']); ?></h3>

            <ul class="site-footer__menu" role="list">
              <?php foreach ($footer_navigation as $item): ?>
                <?php
                $link = $item['link'] ?? null;
                if (empty($link['url'])) {
                  continue;
                }
                ?>
                <li>
                  <a
                    href="<?php echo esc_url($link['url']); ?>"
                    target="<?php echo esc_attr(!empty($link['target']) ? $link['target'] : '_self'); ?>"
                    <?php echo !empty($link['target']) && $link['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                  >
                    <?php echo esc_html($link['title'] ?: 'Link'); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (!empty($footer_cases)):
          $cases_links = $footer_cases['link'];
          ?>
          <div class="site-footer__col site-footer__col--cases">
            <h3 class="site-footer__title"><?php echo esc_html($footer_titles['cases']); ?></h3>

            <ul class="site-footer__menu" role="list">
              <?php foreach ($footer_cases as $cases_link_row): 
                $link = $cases_link_row;
                if (empty($link['links']['url'])) {
                  continue;
                }
              ?>
                <a href="<?php echo esc_url($link['links']['url']); ?>">
                  <?php echo esc_html($link['links']['title']); ?>
                </a>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="site-footer__col site-footer__col--contact">
          <h3 class="site-footer__title"><?php echo esc_html($footer_titles['contact']); ?></h3>

          <?php if (!empty($cta_link['url'])): ?>
            <a
              class="site-footer__cta"
              href="<?php echo esc_url($cta_link['url']); ?>"
              target="<?php echo esc_attr(!empty($cta_link['target']) ? $cta_link['target'] : '_self'); ?>"
              <?php echo !empty($cta_link['target']) && $cta_link['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
            >
              <span><?php echo esc_html($cta_link['title'] ?: 'Get Free Audit'); ?></span>
            </a>
          <?php endif; ?>

          <?php if ($email): ?>
            <a class="site-footer__contact-link" href="mailto:<?php echo esc_attr($email); ?>">
              <?php echo esc_html($email); ?>
            </a>
          <?php endif; ?>

          <?php if ($phone): ?>
            <a class="site-footer__contact-link" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">
              <?php echo esc_html($phone); ?>
            </a>
          <?php endif; ?>

          <?php if (!empty($socials)): ?>
            <ul class="site-footer__social" role="list">
              <?php foreach ($socials as $s): ?>
                <li>
                  <a
                    class="socbtn"
                    href="<?php echo esc_url($s['url']); ?>"
                    target="<?php echo esc_attr($s['target']); ?>"
                    rel="noopener noreferrer"
                    aria-label="<?php echo esc_attr($s['label']); ?>"
                  >
                    <img src="<?php echo esc_url($s['icon']); ?>" alt="" loading="lazy" decoding="async" />
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!empty($footer_links)): ?>
        <div class="site-footer__seo">
          <ul class="site-footer__seo-list" role="list">
            <?php foreach ($footer_links as $item): ?>
              <?php
              $link = $item['link'] ?? null;
              if (empty($link['url'])) {
                continue;
              }
              ?>
              <li>
                <a
                  href="<?php echo esc_url($link['url']); ?>"
                  target="<?php echo esc_attr(!empty($link['target']) ? $link['target'] : '_self'); ?>"
                  <?php echo !empty($link['target']) && $link['target'] === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
                >
                  <?php echo esc_html($link['title'] ?: 'Link'); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

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
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
