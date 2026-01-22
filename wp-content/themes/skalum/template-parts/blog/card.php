<article class="blog-card">
    <a href="<?php the_permalink(); ?>">

        <div class="blog-card__img">
            <?php the_post_thumbnail('large'); ?>
        </div>

        <div class="blog-card__body">
            <div class="blog-card__meta">
                <span class="blog-card__cat">
                    <?php echo get_the_category()[0]->name ?? ''; ?>
                </span>
                <span class="blog-card__date">
                    <?php echo get_the_date('F j, Y'); ?>
                </span>
            </div>

            <h3><?php the_title(); ?></h3>
        </div>

    </a>
</article>
