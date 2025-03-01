<?php get_header(); ?>
<main>
    <?php while (have_posts()) : the_post(); ?>
        <article class="project">
            <h1><?php the_title(); ?></h1>
            <div class="project-meta">
                <p><strong>Name:</strong> <?= esc_html(get_post_meta(get_the_ID(), '_project_name', true)) ?></p>
                <p><strong>Description:</strong> <?= esc_html(get_post_meta(get_the_ID(), '_project_description', true)) ?></p>
                <p><strong>URL:</strong> <a href="<?= esc_url(get_post_meta(get_the_ID(), '_project_url', true)) ?>">Visit Project</a></p>
                <p><strong>Duration:</strong> <?= esc_html(get_post_meta(get_the_ID(), '_project_start_date', true)) ?> to <?= esc_html(get_post_meta(get_the_ID(), '_project_end_date', true)) ?></p>
            </div>
            <?php the_content(); ?>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>