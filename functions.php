<?php
// 1. Theme Setup
function portfolio_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus([
        'primary' => __('Primary Menu', 'portfolio-theme'),
        'footer' => __('Footer Menu', 'portfolio-theme')
    ]);
}
add_action('after_setup_theme', 'portfolio_theme_setup');

// 2. Register Project Post Type
function register_project_post_type() {
    $labels = [
        'name' => __('Projects', 'portfolio-theme'),
        'singular_name' => __('Project', 'portfolio-theme'),
        'menu_name' => __('Projects', 'portfolio-theme'),
        'all_items' => __('All Projects', 'portfolio-theme')
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'projects'],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-portfolio'
    ];
    register_post_type('project', $args);
}
add_action('init', 'register_project_post_type');

// 3. Project Meta Fields
function project_meta_boxes() {
    add_meta_box(
        'project_details',
        __('Project Details', 'portfolio-theme'),
        'render_project_meta',
        'project',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_project', 'project_meta_boxes');

function render_project_meta($post) {
    wp_nonce_field('project_meta_action', 'project_meta_nonce');

    $fields = [
        'start_date' => __('Start Date', 'portfolio-theme'),
        'end_date' => __('End Date', 'portfolio-theme'),
        'project_url' => __('Project URL', 'portfolio-theme')
    ];

    echo '<div class="project-meta-grid">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, "_project_$key", true);
        printf(
            '<div class="meta-field">
                <label for="%1$s">%2$s</label>
                <input type="%3$s" id="%1$s" name="%1$s" value="%4$s">
            </div>',
            esc_attr($key),
            esc_html($label),
            $key === 'project_url' ? 'url' : 'date',
            esc_attr($value)
        );
    }
    echo '</div>';
}

function save_project_meta($post_id) {
    if (!isset($_POST['project_meta_nonce'])) return;
    if (!wp_verify_nonce($_POST['project_meta_nonce'], 'project_meta_action')) return;

    $fields = ['start_date', 'end_date', 'project_url'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                "_project_$field",
                sanitize_text_field($_POST[$field])
            );
        }
    }
}
add_action('save_post_project', 'save_project_meta');

// 4. REST API Endpoint
add_action('rest_api_init', function() {
    register_rest_route('portfolio/v1', '/projects', [
        'methods' => 'GET',
        'callback' => 'get_projects_api',
        'permission_callback' => '__return_true'
    ]);
});

function get_projects_api() {
    $projects = get_posts([
        'post_type' => 'project',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ]);

    $data = [];
    foreach ($projects as $project) {
        $data[] = [
            'title' => sanitize_text_field($project->post_title),
            'url' => esc_url(get_permalink($project->ID)),
            'start_date' => sanitize_text_field(get_post_meta($project->ID, '_project_start_date', true)),
            'end_date' => sanitize_text_field(get_post_meta($project->ID, '_project_end_date', true)),
            'thumbnail' => esc_url(get_the_post_thumbnail_url($project->ID, 'full'))
        ];
    }
    
    return new WP_REST_Response($data, 200);
}

// 5. Enqueue Assets
function portfolio_assets() {
    wp_enqueue_style(
        'portfolio-main',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );
    
    wp_enqueue_script(
        'portfolio-scripts',
        get_template_directory_uri() . '/js/scripts.js',
        [],
        filemtime(get_template_directory() . '/js/scripts.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'portfolio_assets');