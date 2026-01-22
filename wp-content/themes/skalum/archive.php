<?php get_header(); ?>

<section class="blog">
    <div class="container">

        <div class="blog__header">
            <span class="blog__badge">Blog</span>
            <h1 class="blog__title">Blog</h1>
        </div>

        <div class="blog__grid" data-page="1">
            <?php if (have_posts()): ?>
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php get_template_part('template-parts/blog/card'); ?>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts found</p>
            <?php endif; ?>
        </div>

        <button class="blog__load">Load More</button>

    </div>
</section>

<?php get_footer(); ?>