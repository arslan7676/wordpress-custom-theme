<?php
/**
 * Template Name: Blog
 */
get_header();
?>
<main class="blog-page">
    <section class="blog-hero">
        <h1><?php the_title(); ?></h1>
    </section>
    <div class="blog-grid container">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $query = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 6,
            'paged' => $paged
        ]);

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post(); ?>
                <article class="blog-card">
                    <div class="card-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                    <div class="card-content">
                        <h3><?php the_title(); ?></h3>
                        <div class="meta">
                            <span><?php echo get_the_date(); ?></span>
                            <span><?php echo get_comments_number(); ?> comments</span>
                        </div>
                        <?php the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>" class="read-more">Read More →</a>
                    </div>
                </article>
            <?php endwhile; ?>

            <div class="pagination">
                <?php
                echo paginate_links([
                    'total' => $query->max_num_pages,
                    'prev_text' => __('← Previous'),
                    'next_text' => __('Next →')
                ]);
                ?>
            </div>
            
        <?php else : ?>
            <p><?php esc_html_e('No posts found'); ?></p>
        <?php endif;
        wp_reset_postdata(); ?>
    </div>
</main>

<?php get_footer(); ?>