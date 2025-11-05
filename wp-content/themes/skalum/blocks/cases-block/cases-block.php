<?php
/**
 * @var array $block WP block settings.
 */

$id = $block['anchor'] ?? 'cases-' . $block['id'];
$class = 'cases-block' . (!empty($block['className']) ? ' ' . $block['className'] : '');

$block_name = get_field('block_name') ?: 'Work Cases';
$title = get_field('title');
$cta = get_field('cta');
$cases = get_field('cases');
?>

<section id="<?= esc_attr($id) ?>" class="<?= esc_attr($class) ?>">
  <div class="container">
    <div class="cases-block__inner">

      <div class="cases-block__header">
        <?php if ($block_name): ?>
          <div class="cases-block__label"><?= esc_html($block_name) ?></div>
        <?php endif; ?>
        <div class="cases-block__header-bottom">
          <?php if ($title): ?>
            <div class="cases-block__title"><?= wp_kses_post($title) ?></div>
          <?php endif; ?>

          <?php if ($cta): ?>
            <a href="<?= esc_url($cta['url']) ?>" class="cases-block__cta" <?= $cta['target'] ? 'target="_blank"' : '' ?>>
              <?= esc_html($cta['title']) ?: 'View All Cases' ?> →
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="cases-block__slider">
        <?php if ($cases):
          foreach ($cases as $index => $case): ?>
            <div class="case-slide <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
              <div class="case-slide__content">

                <div class="case-slide__list">
                  <?php foreach ($cases as $i => $c): ?>
                    <div class="case-item <?= $i === $index ? 'active' : '' ?>" data-index="<?= $i ?>">
                      <div class="case-header">
                        <div class="left">
                          <div class="case-item__num"><?= $i + 1 ?></div>
                          <div class="case-item__title"><?= esc_html($c['title']) ?></div>
                        </div>
                        <div class="case-item__arrow">→ View case</div>
                      </div>
                      <div class="case-item__desc"><?= wp_kses_post($c['description']) ?></div>
                      <div class="case-item__timer">
                        <div class="case-item__timer-fill"></div>
                      </div>

                      <div class="case-slide__detail">
                        <div class="case-detail">

                          <?php if ($img = wp_get_attachment_image_url($case['image'], 'large')): ?>
                            <div class="case-detail__chart">
                              <img src="<?= esc_url($img) ?>" alt="Chart">
                            </div>
                          <?php endif; ?>

                          <div class="case-detail__label">
                            <span class="dot"></span>
                            <?= esc_html($case['case_label']) ?>
                          </div>
                          
                          <div class="case-detail__metrics">
                            <div class="metric">
                              <div class="metric__top">
                                <div class="metric__num"><?= esc_html($case['first_column']['num_1']) ?></div>
                                <svg width="20" height="22" viewBox="0 0 20 22" fill="none"
                                  xmlns="http://www.w3.org/2000/svg">
                                  <path
                                    d="M16.0824 11.2035C16.2595 11.0264 16.2595 10.7393 16.0824 10.5622L13.1966 7.67639C13.0195 7.4993 12.7324 7.4993 12.5553 7.67639C12.3782 7.85347 12.3782 8.14059 12.5553 8.31767L15.1204 10.8828L12.5553 13.448C12.3782 13.625 12.3782 13.9122 12.5553 14.0892C12.7324 14.2663 13.0195 14.2663 13.1966 14.0892L16.0824 11.2035ZM3.75293 10.8828V11.3363H15.7617V10.8828V10.4294H3.75293V10.8828Z"
                                    fill="white" fill-opacity="0.2" />
                                </svg>
                                <div class="metric__arrow"><?= esc_html($case['first_column']['num_2']) ?></div>
                              </div>
                              <div class="metric__desc"><?= esc_html($case['first_column']['description']) ?></div>
                            </div>
                            <div class="metric">
                              <div class="metric__top">
                                <div class="metric__num"><?= esc_html($case['second_column']['num_1']) ?></div>
                                <svg width="20" height="22" viewBox="0 0 20 22" fill="none"
                                  xmlns="http://www.w3.org/2000/svg">
                                  <path
                                    d="M16.0824 11.2035C16.2595 11.0264 16.2595 10.7393 16.0824 10.5622L13.1966 7.67639C13.0195 7.4993 12.7324 7.4993 12.5553 7.67639C12.3782 7.85347 12.3782 8.14059 12.5553 8.31767L15.1204 10.8828L12.5553 13.448C12.3782 13.625 12.3782 13.9122 12.5553 14.0892C12.7324 14.2663 13.0195 14.2663 13.1966 14.0892L16.0824 11.2035ZM3.75293 10.8828V11.3363H15.7617V10.8828V10.4294H3.75293V10.8828Z"
                                    fill="white" fill-opacity="0.2" />
                                </svg>
                                <div class="metric__arrow"><?= esc_html($case['second_column']['num_2']) ?></div>
                              </div>
                              <div class="metric__desc"><?= esc_html($case['second_column']['description']) ?></div>
                            </div>
                            <div class="metric">
                              <div class="metric__top">
                                <div class="metric__num"><?= esc_html($case['third_column']['num_1']) ?></div>
                                <svg width="20" height="22" viewBox="0 0 20 22" fill="none"
                                  xmlns="http://www.w3.org/2000/svg">
                                  <path
                                    d="M16.0824 11.2035C16.2595 11.0264 16.2595 10.7393 16.0824 10.5622L13.1966 7.67639C13.0195 7.4993 12.7324 7.4993 12.5553 7.67639C12.3782 7.85347 12.3782 8.14059 12.5553 8.31767L15.1204 10.8828L12.5553 13.448C12.3782 13.625 12.3782 13.9122 12.5553 14.0892C12.7324 14.2663 13.0195 14.2663 13.1966 14.0892L16.0824 11.2035ZM3.75293 10.8828V11.3363H15.7617V10.8828V10.4294H3.75293V10.8828Z"
                                    fill="white" fill-opacity="0.2" />
                                </svg>
                                <div class="metric__arrow"><?= esc_html($case['third_column']['num_2']) ?></div>
                              </div>
                              <div class="metric__desc"><?= esc_html($case['third_column']['description']) ?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="case-slide__detail">
                  <div class="case-detail">
                    <div class="case-detail__label">
                      <span class="dot"></span>
                      <?= esc_html($case['case_label']) ?>
                    </div>

                    <?php if ($img = wp_get_attachment_image_url($case['image'], 'large')): ?>
                      <div class="case-detail__chart">
                        <img src="<?= esc_url($img) ?>" alt="Chart">
                      </div>
                    <?php endif; ?>

                    <div class="case-detail__metrics">
                      <div class="metric">
                        <div class="metric__top">
                          <div class="metric__num"><?= esc_html($case['first_column']['num_1']) ?></div>
                          <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M16.0824 11.2035C16.2595 11.0264 16.2595 10.7393 16.0824 10.5622L13.1966 7.67639C13.0195 7.4993 12.7324 7.4993 12.5553 7.67639C12.3782 7.85347 12.3782 8.14059 12.5553 8.31767L15.1204 10.8828L12.5553 13.448C12.3782 13.625 12.3782 13.9122 12.5553 14.0892C12.7324 14.2663 13.0195 14.2663 13.1966 14.0892L16.0824 11.2035ZM3.75293 10.8828V11.3363H15.7617V10.8828V10.4294H3.75293V10.8828Z"
                              fill="white" fill-opacity="0.2" />
                          </svg>
                          <div class="metric__arrow"><?= esc_html($case['first_column']['num_2']) ?></div>
                        </div>
                        <div class="metric__desc"><?= esc_html($case['first_column']['description']) ?></div>
                      </div>
                      <div class="metric">
                        <div class="metric__top">
                          <div class="metric__num"><?= esc_html($case['second_column']['num_1']) ?></div>
                          <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M16.0824 11.2035C16.2595 11.0264 16.2595 10.7393 16.0824 10.5622L13.1966 7.67639C13.0195 7.4993 12.7324 7.4993 12.5553 7.67639C12.3782 7.85347 12.3782 8.14059 12.5553 8.31767L15.1204 10.8828L12.5553 13.448C12.3782 13.625 12.3782 13.9122 12.5553 14.0892C12.7324 14.2663 13.0195 14.2663 13.1966 14.0892L16.0824 11.2035ZM3.75293 10.8828V11.3363H15.7617V10.8828V10.4294H3.75293V10.8828Z"
                              fill="white" fill-opacity="0.2" />
                          </svg>
                          <div class="metric__arrow"><?= esc_html($case['second_column']['num_2']) ?></div>
                        </div>
                        <div class="metric__desc"><?= esc_html($case['second_column']['description']) ?></div>
                      </div>
                      <div class="metric">
                        <div class="metric__top">
                          <div class="metric__num"><?= esc_html($case['third_column']['num_1']) ?></div>
                          <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M16.0824 11.2035C16.2595 11.0264 16.2595 10.7393 16.0824 10.5622L13.1966 7.67639C13.0195 7.4993 12.7324 7.4993 12.5553 7.67639C12.3782 7.85347 12.3782 8.14059 12.5553 8.31767L15.1204 10.8828L12.5553 13.448C12.3782 13.625 12.3782 13.9122 12.5553 14.0892C12.7324 14.2663 13.0195 14.2663 13.1966 14.0892L16.0824 11.2035ZM3.75293 10.8828V11.3363H15.7617V10.8828V10.4294H3.75293V10.8828Z"
                              fill="white" fill-opacity="0.2" />
                          </svg>
                          <div class="metric__arrow"><?= esc_html($case['third_column']['num_2']) ?></div>
                        </div>
                        <div class="metric__desc"><?= esc_html($case['third_column']['description']) ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; endif; ?>
      </div>

    </div>
  </div>
</section>