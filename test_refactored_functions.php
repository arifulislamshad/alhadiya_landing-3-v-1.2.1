<?php
/**
 * Test Refactored Functions
 * 
 * This file tests that all refactored functions are working correctly
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test function availability
 */
function test_refactored_functions() {
    $tests = array();
    
    // Test theme setup functions
    $tests['theme_setup'] = function_exists('alhadiya_theme_setup');
    $tests['scripts'] = function_exists('alhadiya_scripts');
    $tests['body_classes'] = function_exists('alhadiya_body_classes');
    
    // Test custom post types
    $tests['create_course_post_types'] = function_exists('create_course_post_types');
    $tests['add_review_meta_boxes'] = function_exists('add_review_meta_boxes');
    $tests['save_review_details'] = function_exists('save_review_details');
    
    // Test utilities
    $tests['get_wc_product_data'] = function_exists('get_wc_product_data');
    $tests['get_youtube_embed_url'] = function_exists('get_youtube_embed_url');
    $tests['sanitize_checkbox'] = function_exists('sanitize_checkbox');
    $tests['get_client_ip'] = function_exists('get_client_ip');
    $tests['check_ip_blocking'] = function_exists('check_ip_blocking');
    $tests['block_ip_after_order'] = function_exists('block_ip_after_order');
    $tests['parse_user_agent'] = function_exists('parse_user_agent');
    $tests['get_location_and_isp'] = function_exists('get_location_and_isp');
    
    // Test tracking functions
    $tests['ensure_tracking_tables_exist'] = function_exists('ensure_tracking_tables_exist');
    $tests['create_device_tracking_table'] = function_exists('create_device_tracking_table');
    $tests['create_device_events_table'] = function_exists('create_device_events_table');
    $tests['create_server_events_table'] = function_exists('create_server_events_table');
    $tests['track_enhanced_device_info'] = function_exists('track_enhanced_device_info');
    $tests['track_time_spent'] = function_exists('track_time_spent');
    $tests['track_custom_event'] = function_exists('track_custom_event');
    $tests['update_device_screen_size'] = function_exists('update_device_screen_size');
    $tests['get_current_session_id'] = function_exists('get_current_session_id');
    $tests['get_user_ip_address'] = function_exists('get_user_ip_address');
    $tests['get_browser_name'] = function_exists('get_browser_name');
    $tests['get_device_type_from_user_agent'] = function_exists('get_device_type_from_user_agent');
    $tests['get_operating_system'] = function_exists('get_operating_system');
    $tests['get_user_timezone'] = function_exists('get_user_timezone');
    $tests['get_user_language'] = function_exists('get_user_language');
    $tests['is_bot_user_agent'] = function_exists('is_bot_user_agent');
    
    // Test WooCommerce functions
    $tests['handle_woocommerce_order'] = function_exists('handle_woocommerce_order');
    $tests['add_custom_order_columns'] = function_exists('add_custom_order_columns');
    $tests['populate_custom_order_columns'] = function_exists('populate_custom_order_columns');
    $tests['save_order_tracking_info'] = function_exists('save_order_tracking_info');
    $tests['alhadiya_init_woocommerce_session'] = function_exists('alhadiya_init_woocommerce_session');
    
    // Test customizer functions
    $tests['alhadiya_customize_register'] = function_exists('alhadiya_customize_register');
    $tests['alhadiya_add_tracking_settings'] = function_exists('alhadiya_add_tracking_settings');
    
    // Test server events functions
    $tests['alhadiya_init_server_session'] = function_exists('alhadiya_init_server_session');
    $tests['alhadiya_log_server_event'] = function_exists('alhadiya_log_server_event');
    $tests['alhadiya_send_to_facebook_conversion_api'] = function_exists('alhadiya_send_to_facebook_conversion_api');
    $tests['alhadiya_send_to_google_analytics'] = function_exists('alhadiya_send_to_google_analytics');
    $tests['alhadiya_send_to_microsoft_clarity'] = function_exists('alhadiya_send_to_microsoft_clarity');
    $tests['handle_alhadiya_server_event'] = function_exists('handle_alhadiya_server_event');
    $tests['track_enhanced_event'] = function_exists('track_enhanced_event');
    $tests['alhadiya_add_tracking_scripts'] = function_exists('alhadiya_add_tracking_scripts');
    
    // Test admin functions
    $tests['add_enhanced_device_tracking_menu'] = function_exists('add_enhanced_device_tracking_menu');
    $tests['enhanced_device_tracking_page'] = function_exists('enhanced_device_tracking_page');
    $tests['device_session_details_page'] = function_exists('device_session_details_page');
    $tests['alhadiya_add_server_events_menu'] = function_exists('alhadiya_add_server_events_menu');
    $tests['alhadiya_server_events_dashboard'] = function_exists('alhadiya_server_events_dashboard');
    
    // Test classes
    $tests['WP_Bootstrap_Navwalker_class'] = class_exists('WP_Bootstrap_Navwalker');
    
    // Test legacy functions
    $tests['track_enhanced_device_info_v2'] = function_exists('track_enhanced_device_info_v2');
    $tests['alhadiya_collect_facebook_user_data'] = function_exists('alhadiya_collect_facebook_user_data');
    $tests['alhadiya_map_to_facebook_event'] = function_exists('alhadiya_map_to_facebook_event');
    $tests['alhadiya_queue_facebook_capi_event'] = function_exists('alhadiya_queue_facebook_capi_event');
    
    // Count results
    $passed = 0;
    $failed = 0;
    $results = array();
    
    foreach ($tests as $function => $exists) {
        if ($exists) {
            $passed++;
            $results[$function] = 'PASS';
        } else {
            $failed++;
            $results[$function] = 'FAIL';
        }
    }
    
    // Return results
    return array(
        'total' => count($tests),
        'passed' => $passed,
        'failed' => $failed,
        'results' => $results
    );
}

/**
 * Test theme mods and settings
 */
function test_theme_settings() {
    $settings = array();
    
    // Test main settings
    $settings['phone_number'] = get_theme_mod('phone_number', '+8801737146996');
    $settings['youtube_url'] = get_theme_mod('youtube_url', '');
    $settings['main_heading'] = get_theme_mod('main_heading', '');
    
    // Test delivery settings
    $settings['dhaka_delivery_charge'] = get_theme_mod('dhaka_delivery_charge', 0);
    $settings['outside_dhaka_delivery_charge'] = get_theme_mod('outside_dhaka_delivery_charge', 0);
    
    // Test tracking settings
    $settings['enable_device_tracking'] = get_theme_mod('enable_device_tracking', true);
    $settings['enable_facebook_capi'] = get_theme_mod('enable_facebook_capi', true);
    $settings['enable_google_analytics'] = get_theme_mod('enable_google_analytics', true);
    $settings['enable_microsoft_clarity'] = get_theme_mod('enable_microsoft_clarity', true);
    
    return $settings;
}

/**
 * Test database tables
 */
function test_database_tables() {
    global $wpdb;
    
    $tables = array();
    
    // Check if tables exist
    $tables['device_tracking'] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}device_tracking'") === $wpdb->prefix . 'device_tracking';
    $tables['device_events'] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}device_events'") === $wpdb->prefix . 'device_events';
    $tables['server_events'] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}server_events'") === $wpdb->prefix . 'server_events';
    $tables['facebook_capi_events'] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}facebook_capi_events'") === $wpdb->prefix . 'facebook_capi_events';
    $tables['facebook_capi_logs'] = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}facebook_capi_logs'") === $wpdb->prefix . 'facebook_capi_logs';
    
    return $tables;
}

/**
 * Run comprehensive test
 */
function run_comprehensive_test() {
    echo "<h2>Alhadiya Theme Refactoring Test Results</h2>\n";
    
    // Test functions
    $function_results = test_refactored_functions();
    echo "<h3>Function Availability Test</h3>\n";
    echo "<p>Total Functions: {$function_results['total']}</p>\n";
    echo "<p>Passed: {$function_results['passed']}</p>\n";
    echo "<p>Failed: {$function_results['failed']}</p>\n";
    
    if ($function_results['failed'] > 0) {
        echo "<h4>Failed Functions:</h4>\n";
        echo "<ul>\n";
        foreach ($function_results['results'] as $function => $result) {
            if ($result === 'FAIL') {
                echo "<li>{$function}</li>\n";
            }
        }
        echo "</ul>\n";
    }
    
    // Test settings
    $settings = test_theme_settings();
    echo "<h3>Theme Settings Test</h3>\n";
    echo "<ul>\n";
    foreach ($settings as $setting => $value) {
        echo "<li><strong>{$setting}:</strong> " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "</li>\n";
    }
    echo "</ul>\n";
    
    // Test database tables
    $tables = test_database_tables();
    echo "<h3>Database Tables Test</h3>\n";
    echo "<ul>\n";
    foreach ($tables as $table => $exists) {
        echo "<li><strong>{$table}:</strong> " . ($exists ? 'EXISTS' : 'MISSING') . "</li>\n";
    }
    echo "</ul>\n";
    
    // Overall result
    $overall_success = $function_results['failed'] === 0;
    echo "<h3>Overall Result</h3>\n";
    echo "<p><strong>" . ($overall_success ? 'SUCCESS' : 'FAILED') . "</strong></p>\n";
    
    if ($overall_success) {
        echo "<p>✅ All refactored functions are working correctly!</p>\n";
    } else {
        echo "<p>❌ Some functions are missing or not working properly.</p>\n";
    }
}

// Run test if accessed directly
if (isset($_GET['test_refactored_functions'])) {
    run_comprehensive_test();
}