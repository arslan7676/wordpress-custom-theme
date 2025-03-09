</main> <!-- Close main-content -->

<footer class="site-footer">
    <div class="footer-content container">
        <div class="footer-widgets">
            <section class="footer-menu">
                <h3>Quick Links</h3>
                <?php wp_nav_menu([
                    'theme_location' => 'footer',
                    'container' => false,
                    'menu_class' => 'footer-nav'
                ]); ?>
            </section>

            <section class="footer-info">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
            </section>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>