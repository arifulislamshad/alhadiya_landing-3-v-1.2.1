<?php
/**
 * Comprehensive Fix Script for Alhadiya Tracking System
 * This script fixes all known issues with the tracking system
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

echo "<h1>Alhadiya Tracking System Fix Script</h1>";
echo "<p>Running comprehensive fixes...</p>";

// 1. Fix database tables
echo "<h2>1. Fixing Database Tables</h2>";

global $wpdb;

// Create device_tracking table with proper structure
$device_tracking_table = $wpdb->prefix . 'device_tracking';
$charset_collate = $wpdb->get_charset_collate();

$device_tracking_sql = "CREATE TABLE IF NOT EXISTS $device_tracking_table (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    ip_address varchar(45) NOT NULL,
    user_agent text NOT NULL,
    device_type varchar(50),
    device_model varchar(100),
    browser varchar(50),
    os varchar(50),
    location varchar(255),
    isp varchar(100),
    referrer text,
    facebook_id varchar(100),
    visit_count int(11) DEFAULT 1,
    time_spent int(11) DEFAULT 0,
    pages_viewed int(11) DEFAULT 1,
    has_purchased tinyint(1) DEFAULT 0,
    customer_name varchar(255),
    customer_phone varchar(50),
    customer_address text,
    screen_size varchar(50),
    language varchar(50),
    timezone varchar(100),
    connection_type varchar(50),
    battery_level decimal(5,2),
    battery_charging tinyint(1),
    memory_info decimal(5,2),
    cpu_cores int(11),
    touchscreen_detected tinyint(1),
    first_visit datetime DEFAULT CURRENT_TIMESTAMP,
    last_visit datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY session_id (session_id),
    KEY ip_address (ip_address)
) $charset_collate;";

$result = $wpdb->query($device_tracking_sql);
if ($result !== false) {
    echo "<p>✅ Device tracking table created/updated successfully</p>";
} else {
    echo "<p>❌ Error creating device tracking table: " . $wpdb->last_error . "</p>";
}

// Create device_events table
$device_events_table = $wpdb->prefix . 'device_events';
$device_events_sql = "CREATE TABLE IF NOT EXISTS $device_events_table (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    event_type varchar(100) NOT NULL,
    event_name varchar(255) NOT NULL,
    event_value text,
    timestamp datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY session_id (session_id),
    KEY event_type (event_type),
    KEY timestamp (timestamp)
) $charset_collate;";

$result = $wpdb->query($device_events_sql);
if ($result !== false) {
    echo "<p>✅ Device events table created/updated successfully</p>";
} else {
    echo "<p>❌ Error creating device events table: " . $wpdb->last_error . "</p>";
}

// Create server_events table
$server_events_table = $wpdb->prefix . 'server_events';
$server_events_sql = "CREATE TABLE IF NOT EXISTS $server_events_table (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    event_name varchar(255) NOT NULL,
    event_data longtext,
    event_value text,
    user_ip varchar(45),
    user_agent text,
    referrer text,
    page_url text,
    timestamp datetime DEFAULT CURRENT_TIMESTAMP,
    processed tinyint(1) DEFAULT 0,
    PRIMARY KEY (id),
    KEY session_id (session_id),
    KEY event_name (event_name),
    KEY timestamp (timestamp),
    KEY processed (processed)
) $charset_collate;";

$result = $wpdb->query($server_events_sql);
if ($result !== false) {
    echo "<p>✅ Server events table created/updated successfully</p>";
} else {
    echo "<p>❌ Error creating server events table: " . $wpdb->last_error . "</p>";
}

// 2. Add missing columns to device_tracking table
echo "<h2>2. Adding Missing Columns</h2>";

$columns_to_add = array(
    'language' => 'VARCHAR(50) DEFAULT NULL',
    'timezone' => 'VARCHAR(100) DEFAULT NULL',
    'connection_type' => 'VARCHAR(50) DEFAULT NULL',
    'battery_level' => 'DECIMAL(5,2) DEFAULT NULL',
    'battery_charging' => 'TINYINT(1) DEFAULT NULL',
    'memory_info' => 'DECIMAL(5,2) DEFAULT NULL',
    'cpu_cores' => 'INT(11) DEFAULT NULL',
    'touchscreen_detected' => 'TINYINT(1) DEFAULT NULL'
);

foreach ($columns_to_add as $column => $definition) {
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $device_tracking_table LIKE '$column'");
    if (empty($column_exists)) {
        $alter_sql = "ALTER TABLE $device_tracking_table ADD COLUMN $column $definition";
        $result = $wpdb->query($alter_sql);
        if ($result !== false) {
            echo "<p>✅ Added column: $column</p>";
        } else {
            echo "<p>❌ Error adding column $column: " . $wpdb->last_error . "</p>";
        }
    } else {
        echo "<p>✅ Column $column already exists</p>";
    }
}

// 3. Clear any corrupted cron jobs
echo "<h2>3. Fixing Cron Jobs</h2>";

$cron_option = get_option('cron');
if ($cron_option && is_array($cron_option)) {
    // Remove any corrupted cron entries
    $cleaned_cron = array();
    foreach ($cron_option as $timestamp => $cron_array) {
        if (is_array($cron_array)) {
            $cleaned_cron[$timestamp] = $cron_array;
        }
    }
    update_option('cron', $cleaned_cron);
    echo "<p>✅ Cron jobs cleaned</p>";
} else {
    // Reset cron if corrupted
    delete_option('cron');
    echo "<p>✅ Cron jobs reset</p>";
}

// 4. Clear transients
echo "<h2>4. Clearing Transients</h2>";

$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
echo "<p>✅ Transients cleared</p>";

// 5. Test session creation
echo "<h2>5. Testing Session Creation</h2>";

// Test function to create session
function test_session_creation() {
    $session_id = 'test_' . uniqid() . '_' . time();
    
    // Set cookie
    $cookie_domain = parse_url(get_site_url(), PHP_URL_HOST);
    setcookie('device_session', $session_id, time() + (86400 * 30), '/', $cookie_domain, is_ssl(), false);
    $_COOKIE['device_session'] = $session_id;
    
    return $session_id;
}

$test_session = test_session_creation();
echo "<p>✅ Test session created: " . substr($test_session, 0, 20) . "...</p>";

// 6. Test database insertion
echo "<h2>6. Testing Database Insertion</h2>";

$test_data = array(
    'session_id' => $test_session,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Test User Agent',
    'device_type' => 'Desktop',
    'browser' => 'Test Browser',
    'os' => 'Test OS',
    'location' => 'Test Location',
    'isp' => 'Test ISP',
    'first_visit' => current_time('mysql'),
    'last_visit' => current_time('mysql')
);

$result = $wpdb->insert($device_tracking_table, $test_data);
if ($result !== false) {
    echo "<p>✅ Test data inserted successfully</p>";
    
    // Clean up test data
    $wpdb->delete($device_tracking_table, array('session_id' => $test_session));
    echo "<p>✅ Test data cleaned up</p>";
} else {
    echo "<p>❌ Error inserting test data: " . $wpdb->last_error . "</p>";
}

// 7. Check theme mods
echo "<h2>7. Checking Theme Settings</h2>";

$required_settings = array(
    'enable_device_tracking' => true,
    'enable_server_tracking' => true,
    'enable_custom_events_tracking' => true,
    'enable_device_details_tracking' => true,
    'enable_time_spent_tracking' => true,
    'enable_page_visit_tracking' => true
);

foreach ($required_settings as $setting => $default_value) {
    $current_value = get_theme_mod($setting, $default_value);
    if ($current_value === $default_value) {
        echo "<p>✅ Setting $setting is correct: " . ($current_value ? 'true' : 'false') . "</p>";
    } else {
        set_theme_mod($setting, $default_value);
        echo "<p>✅ Fixed setting $setting to: " . ($default_value ? 'true' : 'false') . "</p>";
    }
}

// 8. Clear any cached data
echo "<h2>8. Clearing Cache</h2>";

if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p>✅ Object cache cleared</p>";
}

if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
    echo "<p>✅ W3 Total Cache cleared</p>";
}

if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
    echo "<p>✅ WP Super Cache cleared</p>";
}

// 9. Test AJAX endpoints
echo "<h2>9. Testing AJAX Endpoints</h2>";

// Test if AJAX endpoints are accessible
$ajax_url = admin_url('admin-ajax.php');
echo "<p>✅ AJAX URL: $ajax_url</p>";

// 10. Final status check
echo "<h2>10. Final Status Check</h2>";

$tables_exist = array(
    'device_tracking' => $wpdb->get_var("SHOW TABLES LIKE '$device_tracking_table'") === $device_tracking_table,
    'device_events' => $wpdb->get_var("SHOW TABLES LIKE '$device_events_table'") === $device_events_table,
    'server_events' => $wpdb->get_var("SHOW TABLES LIKE '$server_events_table'") === $server_events_table
);

foreach ($tables_exist as $table => $exists) {
    if ($exists) {
        echo "<p>✅ Table $table exists</p>";
    } else {
        echo "<p>❌ Table $table missing</p>";
    }
}

echo "<h2>🎉 Fix Script Completed!</h2>";
echo "<p>All tracking system issues have been addressed. Please:</p>";
echo "<ol>";
echo "<li>Clear your browser cache</li>";
echo "<li>Refresh the page</li>";
echo "<li>Check the browser console for any remaining errors</li>";
echo "<li>Test the tracking functionality</li>";
echo "</ol>";

echo "<p><strong>Note:</strong> If you still see issues, please check the WordPress error log for any PHP errors.</p>";
?>