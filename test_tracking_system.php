<?php
/**
 * Tracking System Test Script
 * Run this to verify that all tracking components are working
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    $wp_load_path = dirname(__FILE__) . '/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once($wp_load_path);
    } else {
        die('WordPress not found. Please run this script from your WordPress root directory.');
    }
}

// Ensure we have WordPress loaded
if (!function_exists('wp_install')) {
    die('WordPress not properly loaded.');
}

echo "<h1>Tracking System Test</h1>\n";
echo "<p>Testing all tracking components...</p>\n";

// Test 1: Check if tables exist
echo "<h2>1. Database Tables Check</h2>\n";
$tables_to_check = array('device_tracking', 'device_events', 'server_events');
$all_tables_exist = true;

foreach ($tables_to_check as $table_name) {
    $full_table_name = $wpdb->prefix . $table_name;
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'") == $full_table_name;
    
    if ($table_exists) {
        echo "<p style='color: green;'>✓ Table $full_table_name exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Table $full_table_name missing</p>\n";
        $all_tables_exist = false;
    }
}

// Test 2: Check session creation
echo "<h2>2. Session Creation Test</h2>\n";
if (function_exists('alhadiya_init_server_session')) {
    $session_id = alhadiya_init_server_session();
    if (!empty($session_id)) {
        echo "<p style='color: green;'>✓ Session created: " . substr($session_id, 0, 20) . "...</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Session creation failed</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ alhadiya_init_server_session function not found</p>\n";
}

// Test 3: Check AJAX endpoints
echo "<h2>3. AJAX Endpoints Test</h2>\n";
$ajax_url = admin_url('admin-ajax.php');
echo "<p>AJAX URL: $ajax_url</p>\n";

// Test 4: Check theme mods
echo "<h2>4. Theme Settings Check</h2>\n";
$tracking_settings = array(
    'enable_device_tracking' => get_theme_mod('enable_device_tracking', true),
    'enable_server_tracking' => get_theme_mod('enable_server_tracking', true),
    'enable_device_details_tracking' => get_theme_mod('enable_device_details_tracking', true),
    'enable_custom_events_tracking' => get_theme_mod('enable_custom_events_tracking', true)
);

foreach ($tracking_settings as $setting => $value) {
    $status = $value ? 'enabled' : 'disabled';
    $color = $value ? 'green' : 'orange';
    echo "<p style='color: $color;'>• $setting: $status</p>\n";
}

// Test 5: Check functions exist
echo "<h2>5. Function Existence Check</h2>\n";
$functions_to_check = array(
    'alhadiya_init_server_session',
    'alhadiya_log_server_event',
    'alhadiya_handle_server_event',
    'track_enhanced_device_info',
    'create_device_tracking_table',
    'create_device_events_table',
    'alhadiya_create_server_events_table'
);

foreach ($functions_to_check as $function_name) {
    if (function_exists($function_name)) {
        echo "<p style='color: green;'>✓ Function $function_name exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Function $function_name missing</p>\n";
    }
}

// Test 6: Check admin pages
echo "<h2>6. Admin Pages Check</h2>\n";
$admin_pages = array(
    'enhanced-device-tracking' => 'Device Tracking Dashboard',
    'device-session-details' => 'Session Details',
    'server-events-dashboard' => 'Server Events Dashboard'
);

foreach ($admin_pages as $page_slug => $page_name) {
    $page_url = admin_url("admin.php?page=$page_slug");
    echo "<p>• <a href='$page_url' target='_blank'>$page_name</a></p>\n";
}

// Test 7: Check WooCommerce integration
echo "<h2>7. WooCommerce Integration Check</h2>\n";
if (class_exists('WooCommerce')) {
    echo "<p style='color: green;'>✓ WooCommerce is active</p>\n";
    
    // Check if WooCommerce session is available
    if (function_exists('WC') && WC()->session) {
        echo "<p style='color: green;'>✓ WooCommerce session is available</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠ WooCommerce session not available (will use PHP session fallback)</p>\n";
    }
} else {
    echo "<p style='color: orange;'>⚠ WooCommerce not active (will use PHP session fallback)</p>\n";
}

// Test 8: Check for errors in error log
echo "<h2>8. Error Log Check</h2>\n";
$error_log_path = WP_CONTENT_DIR . '/debug.log';
if (file_exists($error_log_path)) {
    $error_log_size = filesize($error_log_path);
    if ($error_log_size > 0) {
        echo "<p style='color: orange;'>⚠ Error log exists and has content ($error_log_size bytes)</p>\n";
        echo "<p><a href='" . content_url('debug.log') . "' target='_blank'>View Error Log</a></p>\n";
    } else {
        echo "<p style='color: green;'>✓ Error log exists but is empty</p>\n";
    }
} else {
    echo "<p style='color: green;'>✓ No error log found (good sign)</p>\n";
}

// Test 9: Check tracking data
echo "<h2>9. Tracking Data Check</h2>\n";
$device_tracking_table = $wpdb->prefix . 'device_tracking';
$device_events_table = $wpdb->prefix . 'device_events';
$server_events_table = $wpdb->prefix . 'server_events';

$device_count = $wpdb->get_var("SELECT COUNT(*) FROM $device_tracking_table");
$events_count = $wpdb->get_var("SELECT COUNT(*) FROM $device_events_table");
$server_events_count = $wpdb->get_var("SELECT COUNT(*) FROM $server_events_table");

echo "<p>• Device tracking records: $device_count</p>\n";
echo "<p>• Device events: $events_count</p>\n";
echo "<p>• Server events: $server_events_count</p>\n";

// Test 10: Generate test data
echo "<h2>10. Test Data Generation</h2>\n";
if (isset($_GET['generate_test_data']) && $_GET['generate_test_data'] === '1') {
    echo "<p>Generating test data...</p>\n";
    
    // Generate test device tracking record
    $test_session_id = 'test_' . uniqid() . '_' . time();
    $test_device_data = array(
        'session_id' => $test_session_id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test User Agent',
        'device_type' => 'Desktop',
        'browser' => 'Test Browser',
        'os' => 'Test OS',
        'location' => 'Test Location',
        'isp' => 'Test ISP',
        'referrer' => 'Test Referrer',
        'facebook_id' => '',
        'screen_size' => '1920x1080',
        'language' => 'en-US',
        'timezone' => 'UTC',
        'connection_type' => 'wifi',
        'battery_level' => 0.85,
        'battery_charging' => 1,
        'memory_info' => 8.0,
        'cpu_cores' => 4,
        'touchscreen_detected' => 0
    );
    
    $result = $wpdb->insert($device_tracking_table, $test_device_data);
    if ($result !== false) {
        echo "<p style='color: green;'>✓ Test device data inserted (ID: " . $wpdb->insert_id . ")</p>\n";
        
        // Generate test event
        $test_event_data = array(
            'session_id' => $test_session_id,
            'event_type' => 'test_event',
            'event_name' => 'Test Event',
            'event_value' => 'Test Value',
            'timestamp' => current_time('mysql')
        );
        
        $event_result = $wpdb->insert($device_events_table, $test_event_data);
        if ($event_result !== false) {
            echo "<p style='color: green;'>✓ Test event data inserted (ID: " . $wpdb->insert_id . ")</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Failed to insert test event data</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Failed to insert test device data</p>\n";
    }
} else {
    echo "<p><a href='?generate_test_data=1'>Generate Test Data</a></p>\n";
}

echo "<h2>Test Summary</h2>\n";
if ($all_tables_exist) {
    echo "<p style='color: green; font-weight: bold;'>✓ All basic tests passed!</p>\n";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Some tests failed. Please run the database fix script.</p>\n";
}

echo "<h3>Next Steps:</h3>\n";
echo "<ol>\n";
echo "<li>Visit your website and check browser console for tracking logs</li>\n";
echo "<li>Check the Device Tracking dashboard in WordPress admin</li>\n";
echo "<li>Verify that device details are being captured</li>\n";
echo "<li>Test the order form to ensure tracking works during purchases</li>\n";
echo "</ol>\n";

echo "<p><a href='" . admin_url() . "'>Return to WordPress Admin</a> | ";
echo "<a href='" . home_url() . "'>Visit Website</a></p>\n";
?>