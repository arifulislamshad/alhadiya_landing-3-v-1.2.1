<?php
/**
 * Custom Post Types
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create custom post types for reviews and FAQs
 */
function create_course_post_types() {
    // Reviews/Photos
    register_post_type('course_review', array(
        'labels' => array(
            'name' => __('Customer Reviews'),
            'singular_name' => __('Customer Review'),
            'add_new' => __('Add New Review'),
            'add_new_item' => __('Add New Customer Review'),
            'edit_item' => __('Edit Review'),
            'new_item' => __('New Review'),
            'view_item' => __('View Review'),
            'search_items' => __('Search Reviews'),
            'not_found' => __('No reviews found'),
            'not_found_in_trash' => __('No reviews found in trash')
        ),
        'public' => true,
        'supports' => array('title', 'thumbnail', 'editor'),
        'menu_icon' => 'dashicons-camera',
        'menu_position' => 25
    ));
    
    // FAQs
    register_post_type('course_faq', array(
        'labels' => array(
            'name' => __('FAQs'),
            'singular_name' => __('FAQ'),
            'add_new' => __('Add New FAQ'),
            'add_new_item' => __('Add New FAQ'),
            'edit_item' => __('Edit FAQ'),
            'new_item' => __('New FAQ'),
            'view_item' => __('View FAQ'),
            'search_items' => __('Search FAQs'),
            'not_found' => __('No FAQs found'),
            'not_found_in_trash' => __('No FAQs found in trash')
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'page-attributes'),
        'menu_icon' => 'dashicons-editor-help',
        'menu_position' => 26
    ));
}
add_action('init', 'create_course_post_types');

/**
 * Add review meta boxes
 */
function add_review_meta_boxes() {
    add_meta_box('review_details', 'Review Details', 'review_details_callback', 'course_review', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_review_meta_boxes');

/**
 * Review details callback
 */
function review_details_callback($post) {
    wp_nonce_field('save_review_details', 'review_details_nonce');
    
    $customer_name = get_post_meta($post->ID, '_customer_name', true);
    $rating = get_post_meta($post->ID, '_rating', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="customer_name">Customer Name:</label></th>';
    echo '<td><input type="text" id="customer_name" name="customer_name" value="' . esc_attr($customer_name) . '" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="rating">Rating:</label></th>';
    echo '<td><select id="rating" name="rating">';
    for ($i = 1; $i <= 5; $i++) {
        $selected = ($rating == $i) ? 'selected' : '';
        echo '<option value="' . $i . '" ' . $selected . '>' . $i . ' Star' . ($i > 1 ? 's' : '') . '</option>';
    }
    echo '</select></td></tr>';
    echo '</table>';
}

/**
 * Save review details
 */
function save_review_details($post_id) {
    if (!isset($_POST['review_details_nonce']) || !wp_verify_nonce($_POST['review_details_nonce'], 'save_review_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['customer_name'])) {
        update_post_meta($post_id, '_customer_name', sanitize_text_field($_POST['customer_name']));
    }
    
    if (isset($_POST['rating'])) {
        update_post_meta($post_id, '_rating', intval($_POST['rating']));
    }
}
add_action('save_post', 'save_review_details');