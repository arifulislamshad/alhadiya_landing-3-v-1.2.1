<?php
/**
 * Customizer Settings
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize register
 */
function alhadiya_customize_register($wp_customize) {
    // Main Settings Section
    $wp_customize->add_section('alhadiya_main_settings', array(
        'title' => __('Main Settings', 'alhadiya'),
        'priority' => 30,
    ));
    
    // Main Heading
    $wp_customize->add_setting('main_heading_text', array(
        'default' => 'অর্গানিক হাতের মেহেদি বানানোর কোর্স মাত্র ৪৯০ টাকা',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('main_heading_text', array(
        'label' => __('Main Heading', 'alhadiya'),
        'section' => 'alhadiya_main_settings',
        'type' => 'text',
    ));
    
    // YouTube Video URL
    $wp_customize->add_setting('youtube_video_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('youtube_video_url', array(
        'label' => __('YouTube Video URL', 'alhadiya'),
        'section' => 'alhadiya_main_settings',
        'type' => 'url',
    ));
    
    // Phone Number
    $wp_customize->add_setting('phone_number', array(
        'default' => '+8801737146996',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('phone_number', array(
        'label' => __('Phone Number', 'alhadiya'),
        'section' => 'alhadiya_main_settings',
        'type' => 'text',
    ));
    
    // Delivery Charges Section
    $wp_customize->add_section('alhadiya_delivery_settings', array(
        'title' => __('Delivery Settings', 'alhadiya'),
        'priority' => 31,
    ));
    
    // Dhaka Delivery Charge
    $wp_customize->add_setting('dhaka_delivery_charge', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('dhaka_delivery_charge', array(
        'label' => __('Dhaka Delivery Charge', 'alhadiya'),
        'section' => 'alhadiya_delivery_settings',
        'type' => 'number',
    ));
    
    // Outside Dhaka Delivery Charge
    $wp_customize->add_setting('outside_dhaka_delivery_charge', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('outside_dhaka_delivery_charge', array(
        'label' => __('Outside Dhaka Delivery Charge', 'alhadiya'),
        'section' => 'alhadiya_delivery_settings',
        'type' => 'number',
    ));
    
    // Course Sections
    $wp_customize->add_section('alhadiya_course_sections', array(
        'title' => __('Course Sections', 'alhadiya'),
        'priority' => 32,
    ));
    
    // Section 1
    $wp_customize->add_setting('section1_title', array(
        'default' => '🌱 অর্গানিক মেহেদী তৈরির সহজ উপায়',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('section1_title', array(
        'label' => __('Section 1 Title', 'alhadiya'),
        'section' => 'alhadiya_course_sections',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('section1_color', array(
        'default' => '#28a745',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section1_color', array(
        'label' => __('Section 1 Color', 'alhadiya'),
        'section' => 'alhadiya_course_sections',
    )));
    
    // Course Items
    for ($i = 1; $i <= 11; $i++) {
        $wp_customize->add_setting("course_item{$i}_text", array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control("course_item{$i}_text", array(
            'label' => __("Course Item {$i} Text", 'alhadiya'),
            'section' => 'alhadiya_course_sections',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("course_item{$i}_color", array(
            'default' => '#28a745',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "course_item{$i}_color", array(
            'label' => __("Course Item {$i} Color", 'alhadiya'),
            'section' => 'alhadiya_course_sections',
        )));
    }
    
    // Section 2
    $wp_customize->add_setting('section2_title', array(
        'default' => '🔥 মেহেদী রঙ বাড়ানোর গোপন টিপস',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('section2_title', array(
        'label' => __('Section 2 Title', 'alhadiya'),
        'section' => 'alhadiya_course_sections',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('section2_color', array(
        'default' => '#dc3545',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section2_color', array(
        'label' => __('Section 2 Color', 'alhadiya'),
        'section' => 'alhadiya_course_sections',
    )));
    
    // Section 3
    $wp_customize->add_setting('section3_title', array(
        'default' => '💎 প্রফেশনাল মেহেদী ব্যবসা',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('section3_title', array(
        'label' => __('Section 3 Title', 'alhadiya'),
        'section' => 'alhadiya_course_sections',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('section3_color', array(
        'default' => '#6f42c1',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section3_color', array(
        'label' => __('Section 3 Color', 'alhadiya'),
        'section' => 'alhadiya_course_sections',
    )));
}
add_action('customize_register', 'alhadiya_customize_register');

/**
 * Add tracking settings to customizer
 */
function alhadiya_add_tracking_settings($wp_customize) {
    // Tracking Settings Section
    $wp_customize->add_section('alhadiya_tracking_settings', array(
        'title' => __('Tracking Settings', 'alhadiya'),
        'priority' => 35,
    ));
    
    // Enable Device Tracking
    $wp_customize->add_setting('enable_device_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_device_tracking', array(
        'label' => __('Enable Device Tracking', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'checkbox',
    ));
    
    // Enable Facebook CAPI
    $wp_customize->add_setting('enable_facebook_capi', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_facebook_capi', array(
        'label' => __('Enable Facebook CAPI', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'checkbox',
    ));
    
    // Facebook Pixel ID
    $wp_customize->add_setting('facebook_pixel_id', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('facebook_pixel_id', array(
        'label' => __('Facebook Pixel ID', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'text',
    ));
    
    // Facebook CAPI Access Token
    $wp_customize->add_setting('facebook_capi_access_token', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('facebook_capi_access_token', array(
        'label' => __('Facebook CAPI Access Token', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'text',
    ));
    
    // Enable Google Analytics
    $wp_customize->add_setting('enable_google_analytics', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_google_analytics', array(
        'label' => __('Enable Google Analytics', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'checkbox',
    ));
    
    // Google Analytics ID
    $wp_customize->add_setting('google_analytics_id', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('google_analytics_id', array(
        'label' => __('Google Analytics ID', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'text',
    ));
    
    // Enable Microsoft Clarity
    $wp_customize->add_setting('enable_microsoft_clarity', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_microsoft_clarity', array(
        'label' => __('Enable Microsoft Clarity', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'checkbox',
    ));
    
    // Microsoft Clarity ID
    $wp_customize->add_setting('microsoft_clarity_id', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('microsoft_clarity_id', array(
        'label' => __('Microsoft Clarity ID', 'alhadiya'),
        'section' => 'alhadiya_tracking_settings',
        'type' => 'text',
    ));
}
add_action('customize_register', 'alhadiya_add_tracking_settings');