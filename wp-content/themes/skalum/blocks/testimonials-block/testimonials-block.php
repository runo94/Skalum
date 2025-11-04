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
        <div class="testimonials-block__inner">

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