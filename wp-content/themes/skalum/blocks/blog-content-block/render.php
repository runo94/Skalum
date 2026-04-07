<?php
if (!defined('ABSPATH')) exit;

$id = !empty($block['anchor']) ? $block['anchor'] : ('blog-content-block-' . ($block['id'] ?? uniqid()));

$class = 'blog-content-block';
$class .= !empty($block['className']) ? ' ' . $block['className'] : '';
$class .= !empty($block['align']) ? ' align' . $block['align'] : '';

$toc_title = get_field('toc_title') ?: 'Contents';
$show_toc  = get_field('show_toc');
$show_toc  = $show_toc !== false;

$bg = (string) get_field('background_color');
$rounded = get_field('rounded');
$rounded = is_array($rounded) ? $rounded : [];

if ($bg) {
    $class .= ' blog-content-block--bg-' . sanitize_html_class($bg);
}

foreach ($rounded as $value) {
    $class .= ' blog-content-block--rounded-' . sanitize_html_class($value);
}

$template = [
    [
        'core/paragraph',
        [
            'content' => '<strong>SEO in 2026 is no longer about rankings or traffic volume.</strong> It has become a strategic layer that connects visibility, trust, and revenue.'
        ]
    ],
    ['core/separator'],
    [
        'core/heading',
        [
            'level' => 2,
            'content' => 'SEO Is No Longer a Channel'
        ]
    ],
    [
        'core/paragraph',
        [
            'content' => 'For a long time, SEO was treated like a standalone marketing channel — something separate from branding, product, or sales.'
        ]
    ],
];

$allowed_blocks = [
    'core/heading',
    'core/paragraph',
    'core/list',
    'core/image',
    'core/quote',
    'core/separator',
    'core/buttons',
    'core/button',
    'core/group',
    'core/columns',
    'core/column',
    'core/table',
    'core/spacer',
    'core/html',
    'core/embed',
];
?>

<section
    id="<?php echo esc_attr($id); ?>"
    class="<?php echo esc_attr($class); ?>"
    data-blog-content-block
>
      <div class="blog-content-block__inner">
    <?php if ($show_toc): ?>
      <aside class="blog-content-block__sidebar">
        <div class="blog-content-block__toc">
          <div class="blog-content-block__toc-title">
            <?php echo esc_html($toc_title); ?>
          </div>

          <nav class="blog-content-block__toc-nav" aria-label="<?php echo esc_attr($toc_title); ?>">
            <ol class="blog-content-block__toc-list" data-blog-content-block-list></ol>
          </nav>
        </div>
      </aside>
    <?php endif; ?>

    <div class="blog-content-block__content-wrap">
      <div class="blog-content-block__content" data-blog-content-block-content>
                <InnerBlocks
                    allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_blocks)); ?>"
                    template="<?php echo esc_attr(wp_json_encode($template)); ?>"
                />
            </div>
        </div>
    </div>
</section>