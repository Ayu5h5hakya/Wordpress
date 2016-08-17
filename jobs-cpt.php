<?php
function hrm_jobs_posttype() {

    $labels = array(
        'name'               => 'Reminders',
        'singular_name'      => 'reminder',
        'menu_name'          => 'reminders',
        'name_admin_bar'     => 'Reminders',
        'add_new'            => 'Add New Task',
        'add_new_item'       => 'Add New Task',
        'new_item'           => 'New Task',
        'edit_item'          => 'Edit Task',
        'view_item'          => 'View Task',
        'all_items'          => 'All Tasks',
        'search_items'       => 'Search Tasks',
        'not_found'          => 'No tasks found.',
        'not_found_in_trash' => 'No tasks found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-welcome-write-blog',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'jobs' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array( 'title' )
    );
    register_post_type( 'job', $args );
}
//fires after wordpress has finished loading the page but before any headers are set
add_action( 'init', 'hrm_jobs_posttype' );

// Flush rewrite rules to add "jobs" as a permalink slug
function hrm_jobs_my_rewrite_flush() {
    hrm_jobs_posttype();
    flush_rewrite_rules();
}
//The mentioned function is run when the plugin is activated
register_activation_hook( __FILE__, 'hrm_jobs_my_rewrite_flush' );
