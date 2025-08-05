<?php
/**
 * Theme Setup Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup function
 */
function alhadiya_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'alhadiya'),
    ));
}
add_action('after_setup_theme', 'alhadiya_theme_setup');

/**
 * Enqueue styles and scripts
 */
function alhadiya_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css');
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_style('alhadiya-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true);
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
    wp_enqueue_script('jquery');
    
    // Localize script for AJAX
    wp_localize_script('jquery', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('alhadiya_nonce'),
        'wc_ajax_url' => class_exists('WC_AJAX') ? WC_AJAX::get_endpoint('%%endpoint%%') : '',
        'dhaka_delivery_charge' => get_theme_mod('dhaka_delivery_charge', 0),
        'outside_dhaka_delivery_charge' => get_theme_mod('outside_dhaka_delivery_charge', 0),
        'phone_number' => get_theme_mod('phone_number', '+8801737146996'),
        'device_info_nonce' => wp_create_nonce('alhadiya_device_info_nonce'),
        'event_nonce' => wp_create_nonce('alhadiya_event_nonce'),
        'screen_size_nonce' => wp_create_nonce('alhadiya_screen_size_nonce'),
        'activity_nonce' => wp_create_nonce('alhadiya_activity_nonce'),
        'server_event_nonce' => wp_create_nonce('alhadiya_server_event_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'alhadiya_scripts');

/**
 * Add body classes
 */
function alhadiya_body_classes($classes) {
    // Add custom body classes if needed
    return $classes;
}
add_filter('body_class', 'alhadiya_body_classes');