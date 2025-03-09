<?php
get_header();
?>

<article class="single-project">
    <?php while (have_posts()) : the_post(); ?>
        <div class="project-header">
            <h1><?php the_title(); ?></h1>
            <div class="project-meta">
                <?php
                $start = get_post_meta(get_the_ID(), '_project_start_date', true);
                $end = get_post_meta(get_the_ID(), '_project_end_date', true);
                if ($start || $end) :
                ?>
                    <div class="project-dates">
                        <?php if ($start) : ?>
                            <span><?php echo esc_html(date_i18n('F Y', strtotime($start))); ?></span>
                        <?php endif;
                        if ($start && $end) : ?> - <?php endif;
                        if ($end) : ?>
                            <span><?php echo esc_html(date_i18n('F Y', strtotime($end))); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($url = get_post_meta(get_the_ID(), '_project_url', true)) : ?>
                    <a href="<?php echo esc_url($url); ?>" class="project-link" target="_blank">
                        <?php esc_html_e('View Project', 'portfolio-theme'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="project-content">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</article>

<?php get_footer(); ?>