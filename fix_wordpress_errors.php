<?php
/**
 * WordPress Error Fix Script
 * Fixes cron errors and session issues
 * 
 * Usage: Upload this file to your WordPress root directory and run it once
 * Then delete the file for security
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Load WordPress
    require_once('wp-config.php');
    require_once('wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

echo "<h2>WordPress Error Fix Script</h2>";

// 1. Fix Cron Error
echo "<h3>1. Fixing Cron Error...</h3>";
global $wpdb;

// Delete corrupted cron option
$result = $wpdb->delete($wpdb->options, array('option_name' => 'cron'));
if ($result !== false) {
    echo "✅ Cron option deleted successfully.<br>";
} else {
    echo "❌ Failed to delete cron option.<br>";
}

// Clear cron cache
wp_clear_scheduled_hook('alhadiya_batch_process_events');
echo "✅ Cleared scheduled cron hooks.<br>";

// 2. Test Session Function
echo "<h3>2. Testing Session Function...</h3>";

// Test WooCommerce session
if (class_exists('WooCommerce')) {
    echo "✅ WooCommerce is active.<br>";
    
    try {
        if (function_exists('WC') && WC()->session) {
            echo "✅ WooCommerce session is working.<br>";
        } else {
            echo "⚠️ WooCommerce session not initialized yet.<br>";
        }
    } catch (Exception $e) {
        echo "❌ WooCommerce session error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "⚠️ WooCommerce not active, using PHP sessions.<br>";
}

// Test PHP session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ PHP session started.<br>";
} else {
    echo "✅ PHP session already active.<br>";
}

// 3. Test Database Tables
echo "<h3>3. Checking Database Tables...</h3>";

$tables = array(
    $wpdb->prefix . 'device_tracking',
    $wpdb->prefix . 'device_events', 
    $wpdb->prefix . 'server_events'
);

foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
    if ($exists) {
        echo "✅ Table $table exists.<br>";
    } else {
        echo "❌ Table $table missing.<br>";
    }
}

// 4. Test Theme Functions
echo "<h3>4. Testing Theme Functions...</h3>";

if (function_exists('alhadiya_init_server_session')) {
    echo "✅ alhadiya_init_server_session function exists.<br>";
    
    try {
        $session_id = alhadiya_init_server_session();
        echo "✅ Session ID generated: " . substr($session_id, 0, 20) . "...<br>";
    } catch (Exception $e) {
        echo "❌ Session function error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ alhadiya_init_server_session function not found.<br>";
}

// 5. Check File Permissions
echo "<h3>5. Checking File Permissions...</h3>";

$wp_content = WP_CONTENT_DIR;
if (is_writable($wp_content)) {
    echo "✅ wp-content directory is writable.<br>";
} else {
    echo "❌ wp-content directory is not writable.<br>";
}

$wp_uploads = wp_upload_dir();
if (is_writable($wp_uploads['basedir'])) {
    echo "✅ uploads directory is writable.<br>";
} else {
    echo "❌ uploads directory is not writable.<br>";
}

// 6. Check PHP Extensions
echo "<h3>6. Checking PHP Extensions...</h3>";

$required_extensions = array('session', 'json', 'curl');
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext extension loaded.<br>";
    } else {
        echo "❌ $ext extension not loaded.<br>";
    }
}

// 7. Check WordPress Debug
echo "<h3>7. WordPress Debug Status...</h3>";

if (defined('WP_DEBUG') && WP_DEBUG) {
    echo "✅ WP_DEBUG is enabled.<br>";
} else {
    echo "⚠️ WP_DEBUG is disabled. Enable for better error reporting.<br>";
}

if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    echo "✅ WP_DEBUG_LOG is enabled.<br>";
} else {
    echo "⚠️ WP_DEBUG_LOG is disabled.<br>";
}

echo "<h3>✅ Fix Script Completed!</h3>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Delete this file for security</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Test your website functionality</li>";
echo "<li>Check if cron errors are resolved</li>";
echo "</ol>";

echo "<p><strong>If errors persist:</strong></p>";
echo "<ul>";
echo "<li>Check wp-content/debug.log for specific errors</li>";
echo "<li>Contact your hosting provider about session configuration</li>";
echo "<li>Ensure WooCommerce is properly installed and activated</li>";
echo "</ul>";
?>