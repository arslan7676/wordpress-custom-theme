// Register 'Project' Post Type
function create_project_post_type() {
    register_post_type('project', [
        'labels' => [
            'name' => 'Projects',
            'singular_name' => 'Project'
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'projects'],
        'supports' => ['title', 'editor', 'thumbnail']
    ]);
}
add_action('init', 'create_project_post_type');

// Add Project Meta Boxes
function add_project_meta_boxes() {
    add_meta_box(
        'project_details',
        'Project Details',
        'render_project_meta_box',
        'project',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_project', 'add_project_meta_boxes');

// Render Meta Box
function render_project_meta_box($post) {
    wp_nonce_field('project_meta_box', 'project_meta_box_nonce');

    $fields = [
        'name' => 'Project Name',
        'description' => 'Project Description',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'url' => 'Project URL'
    ];

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, "_project_$key", true);
        echo "<p><label for='project_$key'>$label:</label>";
        if ($key === 'description') {
            echo "<textarea id='project_$key' name='project_$key'>" . esc_textarea($value) . "</textarea>";
        } elseif ($key === 'url') {
            echo "<input type='url' id='project_$key' name='project_$key' value='" . esc_url($value) . "' />";
        } else {
            echo "<input type='text' id='project_$key' name='project_$key' value='" . esc_attr($value) . "' />";
        }
        echo "</p>";
    }
}

// Save Meta Data
function save_project_meta_data($post_id) {
    if (!isset($_POST['project_meta_box_nonce']) || !wp_verify_nonce($_POST['project_meta_box_nonce'], 'project_meta_box')) return;

    $fields = ['name', 'description', 'start_date', 'end_date', 'url'];
    foreach ($fields as $field) {
        if (isset($_POST["project_$field"])) {
            $value = sanitize_text_field($_POST["project_$field"]);
            if ($field === 'url') $value = esc_url_raw($value);
            if ($field === 'description') $value = sanitize_textarea_field($value);
            update_post_meta($post_id, "_project_$field", $value);
        }
    }
}
add_action('save_post_project', 'save_project_meta_data');
function register_theme_menus() {
    register_nav_menus(['primary-menu' => 'Primary Menu']);
}
add_action('init', 'register_theme_menus');
add_action('rest_api_init', function() {
    register_rest_route('projects/v1', '/list', [
        'methods' => 'GET',
        'callback' => 'get_projects_api',
        'permission_callback' => '__return_true'
    ]);
});

function get_projects_api() {
    $projects = get_posts([
        'post_type' => 'project',
        'posts_per_page' => -1
    ]);

    $data = [];
    foreach ($projects as $project) {
        $data[] = [
            'title' => $project->post_title,
            'url' => get_permalink($project->ID),
            'start_date' => get_post_meta($project->ID, '_project_start_date', true),
            'end_date' => get_post_meta($project->ID, '_project_end_date', true)
        ];
    }

    return new WP_REST_Response($data, 200);
}

add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('project')) {
        $meta_query = [];
        if (!empty($_GET['start_date'])) {
            $meta_query[] = [
                'key' => '_project_start_date',
                'value' => sanitize_text_field($_GET['start_date']),
                'compare' => '>=',
                'type' => 'DATE'
            ];
        }
        if (!empty($_GET['end_date'])) {
            $meta_query[] = [
                'key' => '_project_end_date',
                'value' => sanitize_text_field($_GET['end_date']),
                'compare' => '<=',
                'type' => 'DATE'
            ];
        }
        if (!empty($meta_query)) $query->set('meta_query', $meta_query);
    }
});

// Add Admin Menu
add_action('admin_menu', function() {
    add_submenu_page(
        'upload.php',
        'Unused Media',
        'Unused Media',
        'manage_options',
        'unused-media',
        'render_unused_media_page'
    );
});

// Render Admin Page
function render_unused_media_page() {
    if (!current_user_can('manage_options')) return;

    // Fetch all media
    $media = get_posts([
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'inherit'
    ]);

    // Check for unused media
    $unused_media = [];
    foreach ($media as $attachment) {
        $is_used = false;
        $attachment_url = wp_get_attachment_url($attachment->ID);

        // Check if used in posts
        $posts_with_media = get_posts([
            's' => $attachment_url,
            'post_type' => 'any',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ]);

        if (!empty($posts_with_media)) $is_used = true;

        // Check custom fields
        if (!$is_used) {
            global $wpdb;
            $custom_fields = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $wpdb->postmeta WHERE meta_value LIKE %s",
                    '%' . $wpdb->esc_like($attachment_url) . '%'
                )
            );
            if (!empty($custom_fields)) $is_used = true;
        }

        if (!$is_used) $unused_media[] = $attachment;
    }

    // Display results
    echo '<div class="wrap"><h1>Unused Media</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    foreach ($unused_media as $file) {
        echo '<tr>';
        echo '<td>' . wp_get_attachment_image($file->ID, 'thumbnail') . '</td>';
        echo '<td><button class="delete-media" data-id="' . esc_attr($file->ID) . '">Delete</button></td>';
        echo '</tr>';
    }
    echo '</table></div>';
}

// Enqueue JavaScript
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'media_page_unused-media') {
        wp_enqueue_script('unused-media', get_template_directory_uri() . '/unused-media.js', ['jquery']);
        wp_localize_script('unused-media', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('delete_media_nonce')
        ]);
    }
});

// Handle AJAX Request
add_action('wp_ajax_delete_unused_media', function() {
    check_ajax_referer('delete_media_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die();

    $attachment_id = intval($_POST['id']);
    if (wp_delete_attachment($attachment_id, true)) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
});
