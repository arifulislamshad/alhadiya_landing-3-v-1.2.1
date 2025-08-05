<?php
/**
 * Database Tables Fix Script
 * Run this script to ensure all tracking tables are created with proper structure
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress context, try to load it
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

echo "<h1>Database Tables Fix Script</h1>\n";
echo "<p>Fixing tracking tables...</p>\n";

// Function to create device tracking table
function create_device_tracking_table_fix() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'device_tracking';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
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
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    echo "<p>Device tracking table creation result:</p>\n";
    echo "<pre>" . print_r($result, true) . "</pre>\n";
    
    return $result;
}

// Function to create device events table
function create_device_events_table_fix() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_events';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
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

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    echo "<p>Device events table creation result:</p>\n";
    echo "<pre>" . print_r($result, true) . "</pre>\n";
    
    return $result;
}

// Function to create server events table
function create_server_events_table_fix() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'server_events';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
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
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    echo "<p>Server events table creation result:</p>\n";
    echo "<pre>" . print_r($result, true) . "</pre>\n";
    
    return $result;
}

// Check if tables exist and create them
$tables_to_check = array(
    'device_tracking' => 'create_device_tracking_table_fix',
    'device_events' => 'create_device_events_table_fix',
    'server_events' => 'create_server_events_table_fix'
);

foreach ($tables_to_check as $table_name => $create_function) {
    $full_table_name = $wpdb->prefix . $table_name;
    
    echo "<h3>Checking table: $full_table_name</h3>\n";
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$full_table_name'") != $full_table_name) {
        echo "<p>Table does not exist. Creating...</p>\n";
        $create_function();
    } else {
        echo "<p>Table exists. Checking structure...</p>\n";
        
        // Check if all required columns exist
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $full_table_name");
        $column_names = array_column($columns, 'Field');
        
        echo "<p>Current columns: " . implode(', ', $column_names) . "</p>\n";
        
        // Recreate table to ensure proper structure
        echo "<p>Recreating table to ensure proper structure...</p>\n";
        $wpdb->query("DROP TABLE IF EXISTS $full_table_name");
        $create_function();
    }
}

// Test session creation
echo "<h3>Testing Session Creation</h3>\n";

// Simulate session creation
$test_session_id = 'test_' . uniqid() . '_' . time();
$cookie_domain = parse_url(get_site_url(), PHP_URL_HOST);

echo "<p>Test session ID: $test_session_id</p>\n";
echo "<p>Cookie domain: $cookie_domain</p>\n";

// Test database insertion
$test_data = array(
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

$table_name = $wpdb->prefix . 'device_tracking';
$result = $wpdb->insert($table_name, $test_data);

if ($result !== false) {
    echo "<p style='color: green;'>✓ Test data inserted successfully. Insert ID: " . $wpdb->insert_id . "</p>\n";
    
    // Clean up test data
    $wpdb->delete($table_name, array('session_id' => $test_session_id));
    echo "<p>Test data cleaned up.</p>\n";
} else {
    echo "<p style='color: red;'>✗ Failed to insert test data. Error: " . $wpdb->last_error . "</p>\n";
}

echo "<h3>Database Fix Complete!</h3>\n";
echo "<p>All tracking tables have been checked and created/updated as needed.</p>\n";
echo "<p><a href='" . admin_url() . "'>Return to WordPress Admin</a></p>\n";
?>