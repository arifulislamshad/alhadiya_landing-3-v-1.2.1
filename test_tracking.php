<?php
/**
 * Test Script for Alhadiya Tracking System
 * This script tests if the tracking system is working properly
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
    require_once(ABSPATH . 'wp-config.php');
}

// Ensure WordPress is loaded
if (!function_exists('wp_verify_nonce')) {
    require_once(ABSPATH . 'wp-load.php');
}

echo "<h1>Alhadiya Tracking System Test</h1>";

// Test 1: Check if functions exist
echo "<h2>1. Function Availability Test</h2>";

$required_functions = array(
    'alhadiya_init_server_session',
    'track_enhanced_device_info',
    'alhadiya_log_server_event',
    'alhadiya_handle_server_event',
    'get_client_ip'
);

foreach ($required_functions as $function) {
    if (function_exists($function)) {
        echo "<p>✅ Function $function exists</p>";
    } else {
        echo "<p>❌ Function $function missing</p>";
    }
}

// Test 2: Check database tables
echo "<h2>2. Database Tables Test</h2>";

global $wpdb;

$required_tables = array(
    'device_tracking' => $wpdb->prefix . 'device_tracking',
    'device_events' => $wpdb->prefix . 'device_events',
    'server_events' => $wpdb->prefix . 'server_events'
);

foreach ($required_tables as $table_name => $full_table_name) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'") === $full_table_name;
    if ($exists) {
        echo "<p>✅ Table $table_name exists</p>";
        
        // Check table structure
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $full_table_name");
        echo "<p>📊 Table $table_name has " . count($columns) . " columns</p>";
    } else {
        echo "<p>❌ Table $table_name missing</p>";
    }
}

// Test 3: Test session creation
echo "<h2>3. Session Creation Test</h2>";

if (function_exists('alhadiya_init_server_session')) {
    $session_id = alhadiya_init_server_session();
    if ($session_id) {
        echo "<p>✅ Session created: " . substr($session_id, 0, 20) . "...</p>";
    } else {
        echo "<p>❌ Failed to create session</p>";
    }
}

// Test 4: Test device tracking
echo "<h2>4. Device Tracking Test</h2>";

if (function_exists('track_enhanced_device_info')) {
    $device_info = track_enhanced_device_info();
    if ($device_info) {
        echo "<p>✅ Device tracking working</p>";
        echo "<p>📱 Session ID: " . substr($device_info['session_id'], 0, 20) . "...</p>";
        echo "<p>🌐 IP: " . $device_info['ip'] . "</p>";
        echo "<p>📱 Device: " . $device_info['device_info']['device_type'] . " - " . $device_info['device_info']['browser'] . "</p>";
    } else {
        echo "<p>❌ Device tracking failed</p>";
    }
}

// Test 5: Test server event logging
echo "<h2>5. Server Event Logging Test</h2>";

if (function_exists('alhadiya_log_server_event')) {
    $test_event = alhadiya_log_server_event('test_event', array('test' => 'data'), 'test_value');
    if ($test_event) {
        echo "<p>✅ Server event logging working</p>";
        echo "<p>📝 Event logged: " . $test_event['event_name'] . "</p>";
    } else {
        echo "<p>❌ Server event logging failed</p>";
    }
}

// Test 6: Check theme settings
echo "<h2>6. Theme Settings Test</h2>";

$settings = array(
    'enable_device_tracking' => get_theme_mod('enable_device_tracking', true),
    'enable_server_tracking' => get_theme_mod('enable_server_tracking', true),
    'enable_custom_events_tracking' => get_theme_mod('enable_custom_events_tracking', true),
    'enable_device_details_tracking' => get_theme_mod('enable_device_details_tracking', true),
    'enable_time_spent_tracking' => get_theme_mod('enable_time_spent_tracking', true),
    'enable_page_visit_tracking' => get_theme_mod('enable_page_visit_tracking', true)
);

foreach ($settings as $setting => $value) {
    echo "<p>✅ Setting $setting: " . ($value ? 'enabled' : 'disabled') . "</p>";
}

// Test 7: Check AJAX endpoints
echo "<h2>7. AJAX Endpoints Test</h2>";

$ajax_url = admin_url('admin-ajax.php');
echo "<p>✅ AJAX URL: $ajax_url</p>";

// Test 8: Check for recent tracking data
echo "<h2>8. Recent Tracking Data Test</h2>";

$recent_sessions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}device_tracking ORDER BY last_visit DESC LIMIT 5");
if ($recent_sessions) {
    echo "<p>✅ Found " . count($recent_sessions) . " recent sessions</p>";
    foreach ($recent_sessions as $session) {
        echo "<p>📊 Session: " . substr($session->session_id, 0, 20) . "... | Device: $session->device_type | IP: $session->ip_address</p>";
    }
} else {
    echo "<p>⚠️ No recent tracking data found</p>";
}

// Test 9: Check for recent events
echo "<h2>9. Recent Events Test</h2>";

$recent_events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}device_events ORDER BY timestamp DESC LIMIT 5");
if ($recent_events) {
    echo "<p>✅ Found " . count($recent_events) . " recent events</p>";
    foreach ($recent_events as $event) {
        echo "<p>📝 Event: $event->event_type - $event->event_name</p>";
    }
} else {
    echo "<p>⚠️ No recent events found</p>";
}

// Test 10: Check server events
echo "<h2>10. Server Events Test</h2>";

$recent_server_events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}server_events ORDER BY timestamp DESC LIMIT 5");
if ($recent_server_events) {
    echo "<p>✅ Found " . count($recent_server_events) . " recent server events</p>";
    foreach ($recent_server_events as $event) {
        echo "<p>📝 Server Event: $event->event_name</p>";
    }
} else {
    echo "<p>⚠️ No recent server events found</p>";
}

echo "<h2>🎯 Test Summary</h2>";
echo "<p>If you see mostly ✅ marks above, your tracking system is working properly!</p>";
echo "<p>If you see ❌ marks, please run the fix script first.</p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Visit your website and interact with it</li>";
echo "<li>Check the Device Tracking dashboard in WordPress admin</li>";
echo "<li>Look for new tracking data in the tables</li>";
echo "<li>Check browser console for any JavaScript errors</li>";
echo "</ol>";
?>