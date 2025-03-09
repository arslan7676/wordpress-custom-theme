<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="header-content container">
            <div class="site-branding">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                    <?php bloginfo('name'); ?>
                </a>
            </div>

            <nav class="main-navigation" aria-label="Main Menu">
                <?php wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'menu',
                    'fallback_cb' => false
                ]); ?>
            </nav>
        </div>
    </header>

    <main class="main-content">