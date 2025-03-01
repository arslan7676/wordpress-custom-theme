<?php get_header(); ?>
<main>
    <form method="GET" class="project-filters">
        <input type="date" name="start_date" value="<?= esc_attr($_GET['start_date'] ?? '') ?>">
        <input type="date" name="end_date" value="<?= esc_attr($_GET['end_date'] ?? '') ?>">
        <button type="submit">Filter</button>
    </form>
    <div class="projects-grid">
        <?php while (have_posts()) : the_post(); ?>
            <article class="project">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p>Duration: <?= esc_html(get_post_meta(get_the_ID(), '_project_start_date', true)) ?> - <?= esc_html(get_post_meta(get_the_ID(), '_project_end_date', true)) ?></p>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>