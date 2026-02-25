<?php
/**
 * Blog breadcrumbs
 * @var array $args
 */

defined('ABSPATH') || exit;

$post_id = (int) ($args['post_id'] ?? get_the_ID());
$blog_id = (int) get_option('page_for_posts');

$home_url = home_url('/');
if (function_exists('pll_get_post')) {
    $translated_blog_id = pll_get_post($blog_id);
    $blog_url = $translated_blog_id ? get_permalink($translated_blog_id) : home_url('/blog/');
} else {
    $blog_url = get_permalink($blog_id);
}
$blog_label = $blog_id ? get_the_title($blog_id) : __('Blog', 'skalum');
?>

<nav class="breadcrumbs" aria-label="Breadcrumbs">
    <ol class="breadcrumbs__list">
        <li class="breadcrumbs__item">
            <a class="breadcrumbs__link" href="<?php echo esc_url($home_url); ?>">
                <?php esc_html_e('Home', 'skalum'); ?>
            </a>
        </li>

        <li class="breadcrumbs__sep">/</li>

        <li class="breadcrumbs__item">
            <a class="breadcrumbs__link" href="<?php echo esc_url($blog_url); ?>">
                <?php echo esc_html($blog_label); ?>
            </a>
        </li>

        <li class="breadcrumbs__sep">/</li>

        <li class="breadcrumbs__item breadcrumbs__item--current" aria-current="page">
            <?php echo esc_html(get_the_title($post_id)); ?>
        </li>
    </ol>
</nav>