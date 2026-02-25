<?php
/**
 * Block: Testimonials Carousel
 */

$id = $block['anchor'] ?? ('testimonials-block-' . $block['id']);
$class = 'testimonials-block'
    . (!empty($block['className']) ? ' ' . $block['className'] : '')
    . (!empty($block['align']) ? ' align' . $block['align'] : '');

$hide_on_mobile = (bool) get_field('hide_on_mobile');
$class .= $hide_on_mobile ? ' u-hide-mobile' : '';

$block_name = get_field('block_name');
$title = get_field('title');
$testimonials = get_field('testimonials'); // Repeater
?>

<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>">
    <div class="container">
        <div class="testimonials-block__inner fade-in">

            <!-- Header -->
            <div class="testimonials-block__header">
                <?php if ($block_name): ?>
                    <div class="testimonials-block__name"><?php echo esc_html($block_name); ?></div>
                <?php endif; ?>

                <?php if ($title): ?>
                    <div class="testimonials-block__title"><?php echo wp_kses_post($title); ?></div>
                <?php endif; ?>
            </div>

            <!-- Slider -->
            <?php if ($testimonials && count($testimonials) > 0): ?>
                <div class="testimonials-block__slider">
                    <?php foreach ($testimonials as $item): 
                        $desc = $item['description'];
                        $name = $item['client_name'];
                        $company = $item['company'];
                    ?>
                        <div class="testimonial-card">
                            <div class="testimonial-card__stars">
                                ★★★★★
                            </div>
                            <div class="testimonial-card__text">
                                <?php echo wp_kses_post($desc); ?>
                            </div>
                            <div class="testimonial-card__author">
                                <strong><?php echo esc_html($name); ?></strong>
                                <?php if ($company): ?>
                                    <span><?php echo esc_html($company); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Dots -->
                <div class="testimonials-block__dots"></div>
            <?php else: ?>
                <p>Testimonials is empty.</p>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php
// --- Schema: Reviews for Testimonials block ---
if ($testimonials && count($testimonials) > 0) {

  $site_name = get_bloginfo('name');
  $site_url  = home_url('/');

  $schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [],
  ];

  foreach ($testimonials as $item) {
    $desc    = $item['description'] ?? '';
    $name    = $item['client_name'] ?? '';
    $company = $item['company'] ?? '';

    if (!$desc || !$name) {
      continue;
    }

    $review_body = wp_strip_all_tags($desc);
    if (!$review_body) {
      continue;
    }

    $author = ['@type' => 'Person', 'name' => wp_strip_all_tags($name)];

    if ($company) {
      $author['worksFor'] = [
        '@type' => 'Organization',
        'name'  => wp_strip_all_tags($company),
      ];
    }

    $schema['@graph'][] = [
      '@type' => 'Review',
      'reviewBody' => $review_body,
      'reviewRating' => [
        '@type'       => 'Rating',
        'ratingValue' => '5',
        'bestRating'  => '5',
        'worstRating' => '1',
      ],
      'author' => $author,
      'publisher' => [
        '@type' => 'Organization',
        'name'  => $site_name,
        'url'   => $site_url,
      ],
      'itemReviewed' => [
        '@type' => 'Organization',
        'name'  => $site_name,
        'url'   => $site_url,
      ],
    ];
  }

  if (!empty($schema['@graph'])) :
?>
<script type="application/ld+json">
<?= wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php
  endif;
}
?>
