<?php
/**
 * Case card partial
 * Expects global $post or $args['post_id'].
 */

$post_id = $args['post_id'] ?? get_the_ID();

$title = get_the_title($post_id);
$link = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);
$card = get_post_meta($post_id, '_case_card_data', true);

$client_name = (string) ($card['client_name'] ?? '');
$website = (string) ($card['website'] ?? '');
$logo_id = (int) ($card['logo_id'] ?? 0);

$tags = get_the_terms($post_id, 'case_category');
?>

<article class="case-card fade-in">
    <div class="case-card__header <?= $logo_id ? 'case-card__header--with-logo' : ''; ?>">
        <?php if ($logo_id): ?>
            <?= wp_get_attachment_image($logo_id, 'thumbnail', false, [
                'class' => 'case-card__logo',
                'loading' => 'lazy',
            ]); ?>
        <?php endif; ?>

        <h3 class="case-card__name"><?= esc_html($title); ?></h3>

        <?php if (!empty($website)): ?>
            <a class="case-card__subtitle" href="<?= esc_url($website); ?>" target="_blank" rel="noopener">
                <?= esc_html(preg_replace('#^https?://#', '', $website)); ?> <span aria-hidden="true">↗</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="case-card__image">
        <a href="<?= esc_url($link); ?>">
            <?= get_the_post_thumbnail($post_id, 'medium', ['loading' => 'lazy']); ?>
        </a>
    </div>

    <div class="case-card__content">
        <p><?= esc_html(wp_trim_words(wp_strip_all_tags($excerpt), 18)); ?></p>
    </div>

    <div class="case-card__footer">
        <?php if (!empty($tags) && !is_wp_error($tags)): ?>
            <div class="case-card__tags">
                <?php foreach ($tags as $tag): ?>
                    <span><?= esc_html($tag->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a class="case-card__btn" href="<?= esc_url($link); ?>">View case study →</a>
    </div>
</article>