<?php
/**
 * Case card partial
 * Expects global $post or $args['post_id'].
 */

$post_id = $args['post_id'] ?? get_the_ID();

$title = get_the_title($post_id);
$link = get_permalink($post_id);

$card = get_post_meta($post_id, '_case_card_data', true);
$card = is_array($card) ? $card : [];

$client_name = trim((string) ($card['client_name'] ?? ''));
$website = trim((string) ($card['website'] ?? ''));
$logo_id = (int) ($card['logo_id'] ?? 0);

$name = $client_name !== '' ? $client_name : $title;

$excerpt = wp_strip_all_tags((string) ($card['excerpt'] ?? ''));
if ($excerpt === '') {
    $excerpt = wp_strip_all_tags(get_the_excerpt($post_id));
}

$tags = get_the_terms($post_id, 'case_category');
$tags = (!empty($tags) && !is_wp_error($tags)) ? $tags : [];

$thumb_id = get_post_thumbnail_id($post_id);
?>

<article class="case-card fade-in">
    <div class="case-card__header">
        <div class="case-card__client">
            <h3 class="case-card__name"><?= esc_html($name); ?></h3>

            <?php if ($website !== ''): ?>
                <a class="case-card__subtitle" href="<?= esc_url($website); ?>" target="_blank" rel="noopener nofollow">
                    <?= esc_html(preg_replace('#^https?://|/$#', '', $website)); ?>
                    <?= skalum_external_link_icon('case-card__subtitle-icon'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if ($logo_id): ?>
            <div class="case-card__logo">
                <?= wp_get_attachment_image($logo_id, 'medium', false, [
                    'class' => 'case-card__logo-img',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ]); ?>
            </div>
        <?php endif; ?>
    </div>

    <a class="case-card__image" href="<?= esc_url($link); ?>"
        aria-label="<?= esc_attr(sprintf(
            /* translators: %s: case name. */
            __('View case study: %s', 'skalum'),
            $name
        )); ?>">
        <?php if ($thumb_id): ?>
            <?= wp_get_attachment_image($thumb_id, 'medium_large', false, [
                'class' => 'case-card__thumb',
                'loading' => 'lazy',
                'decoding' => 'async',
            ]); ?>
        <?php else: ?>
            <span class="case-card__placeholder" aria-hidden="true"></span>
        <?php endif; ?>
    </a>

    <?php if ($excerpt !== ''): ?>
        <div class="case-card__content">
            <p><?= esc_html(wp_trim_words($excerpt, 18)); ?></p>
        </div>
    <?php endif; ?>

    <div class="case-card__footer">
        <?php if ($tags): ?>
            <div class="case-card__tags">
                <?php foreach ($tags as $tag): ?>
                    <span><?= esc_html($tag->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a class="case-card__btn" href="<?= esc_url($link); ?>">
            <svg class="case-card__btn-arrow" viewBox="0 0 26 8" fill="none" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M0 4h24M21 1l3 3-3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            <?php esc_html_e('View case study', 'skalum'); ?>
        </a>
    </div>
</article>
