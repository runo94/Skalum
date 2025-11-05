<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="format-detection" content="telephone=no">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <?php get_template_part('template-parts/header/site', 'header'); ?>
  <?php
  $home_url = home_url('/');
  $site_name = get_bloginfo('name');
  ?>

  <header class="site-header">
    <div class="site-header__inner">
      <a href="<?php echo esc_url($home_url); ?>" class="site-header__brand">
        <span class="brand-text"><?php echo esc_html($site_name ?: 'Skalum'); ?></span>
      </a>

      <nav class="nav-pill" aria-label="<?php esc_attr_e('Main', 'yourtheme'); ?>">
        <?php
        wp_nav_menu([
          'theme_location' => 'header',
          'container' => false,
          'menu_class' => 'nav-pill__list',
          'fallback_cb' => false,
        ]);
        ?>

        <?php
        // Toggle DE <-> EN (DE is default)
        function skl_lang_toggle_target(): array
        {
          // якщо немає Polylang — ховаємо перемикач
          if (!function_exists('pll_current_language')) {
            return ['url' => '#', 'label' => 'EN', 'enabled' => false];
          }

          $current = pll_current_language('slug'); // 'de' або 'en'
          $target = ($current === 'de') ? 'en' : 'de';

          // Підпис на кнопці: показуємо, КУДИ перемикаємо
          $labels = ['de' => 'Deutsch', 'en' => 'English'];
          $label = $labels[$target] ?? strtoupper($target);

          $url = '#';

          // 1) Спробуємо отримати URL перекладу поточного об’єкта (post/term/архів)
          if (is_singular() && function_exists('pll_get_post')) {
            $tr_id = pll_get_post(get_queried_object_id(), $target);
            if ($tr_id) {
              $url = get_permalink($tr_id);
            }
          } elseif ((is_category() || is_tag() || is_tax()) && function_exists('pll_get_term')) {
            $term = get_queried_object();
            $tr_id = pll_get_term($term->term_id, $target);
            if ($tr_id) {
              $url = get_term_link((int) $tr_id);
            }
          }

          // 2) Якщо не вдалося — візьмемо URL мов з pll_the_languages (враховує hide default lang)
          if ($url === '#' && function_exists('pll_the_languages')) {
            $langs = pll_the_languages(['raw' => 1, 'hide_if_empty' => 0]);
            if (!empty($langs[$target]['url'])) {
              $url = $langs[$target]['url'];
            }
          }

          // 3) Останній фолбек — головна потрібної мови
          if ($url === '#' && function_exists('pll_home_url')) {
            $url = pll_home_url($target);
          }

          return ['url' => $url, 'label' => $label, 'enabled' => true];
        }

        $toggle = skl_lang_toggle_target();
        ?>
        <?php if (!empty($toggle['enabled'])): ?>
          <div class="nav-pill__lang">
            <a class="lang-btn" href="<?php echo esc_url($toggle['url']); ?>"
              aria-label="<?php echo esc_attr(sprintf(__('Switch language to %s', 'yourtheme'), $toggle['label'])); ?>">
              <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5" />
                <path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18" fill="none" stroke="currentColor"
                  stroke-width="1.5" />
              </svg>
              <span><?php echo esc_html($toggle['label']); ?></span>
            </a>
          </div>
        <?php endif; ?>

      </nav>

      <a class="cta-btn" href="#contact">Get Free Audit</a>

      <button class="burger" id="burgerBtn" aria-label="<?php esc_attr_e('Open menu', 'yourtheme'); ?>"
        aria-controls="mobileNav" aria-expanded="false">
        <span class="burger__bar"></span>
        <span class="burger__bar"></span>
        <span class="burger__bar"></span>
      </button>
    </div>
  </header>

  <?php $toggle = function_exists('skl_lang_toggle_target') ? skl_lang_toggle_target() : ['url'=>'#','label'=>'EN']; ?>

<nav class="mobile-nav" id="mobileNav" hidden>
  <div class="mobile-nav__inner">
    <?php
      wp_nav_menu([
        'theme_location' => 'header',   // твоє меню
        'container'      => false,
        'menu_class'     => 'mobile-nav__list',
        'fallback_cb'    => false,
      ]);
    ?>

    <div class="mobile-nav__lang">
      <a class="lang-pill" href="<?php echo esc_url($toggle['url']); ?>">
        <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
          <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5"/>
          <path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18" fill="none" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        <span><?php echo esc_html($toggle['label']); ?></span>
      </a>
    </div>
  </div>
</nav>