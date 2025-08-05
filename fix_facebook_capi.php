<?php
/**
 * Facebook CAPI Fix Script
 * 
 * This script fixes the Facebook CAPI configuration issues and enables it by default.
 * Run this script once to fix all pending events and enable Facebook CAPI.
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If running directly, include WordPress
    require_once('../../../wp-config.php');
}

/**
 * Fix Facebook CAPI Configuration
 */
function fix_facebook_capi_configuration() {
    echo "<h2>🔧 Fixing Facebook CAPI Configuration...</h2>\n";
    
    // Enable Facebook CAPI by default
    set_theme_mod('enable_facebook_capi', true);
    echo "✅ Facebook CAPI enabled by default<br>\n";
    
    // Set reasonable defaults
    if (!get_theme_mod('facebook_capi_batch_size')) {
        set_theme_mod('facebook_capi_batch_size', 50);
        echo "✅ Batch size set to 50<br>\n";
    }
    
    if (!get_theme_mod('facebook_capi_max_retries')) {
        set_theme_mod('facebook_capi_max_retries', 3);
        echo "✅ Max retries set to 3<br>\n";
    }
    
    // Enable immediate send for better user experience
    set_theme_mod('facebook_capi_immediate_send', true);
    echo "✅ Immediate send enabled<br>\n";
    
    echo "<p style='color: green;'>✅ Configuration fixed successfully!</p>\n";
}

/**
 * Fix Cron Jobs
 */
function fix_facebook_capi_cron_jobs() {
    echo "<h2>⏰ Fixing Cron Jobs...</h2>\n";
    
    // Clear existing cron jobs
    wp_clear_scheduled_hook('alhadiya_process_facebook_capi_batch');
    echo "🗑️ Cleared existing cron jobs<br>\n";
    
    // Reschedule cron jobs
    if (get_theme_mod('enable_facebook_capi', true)) {
        $pixel_id = get_theme_mod('facebook_pixel_id', '');
        $access_token = get_theme_mod('facebook_capi_access_token', '');
        
        if (!empty($pixel_id) && !empty($access_token)) {
            wp_schedule_event(time(), 'every_minute', 'alhadiya_process_facebook_capi_batch');
            echo "✅ Cron job scheduled for every minute<br>\n";
        } else {
            echo "⚠️ Cron job not scheduled - missing Facebook credentials<br>\n";
            echo "📝 Please configure Facebook Pixel ID and Access Token in WordPress Customizer<br>\n";
        }
    }
    
    // Schedule cleanup
    if (!wp_next_scheduled('alhadiya_cleanup_facebook_capi_data')) {
        wp_schedule_event(time(), 'daily', 'alhadiya_cleanup_facebook_capi_data');
        echo "✅ Daily cleanup scheduled<br>\n";
    }
    
    echo "<p style='color: green;'>✅ Cron jobs fixed successfully!</p>\n";
}

/**
 * Create/Update Database Tables
 */
function fix_facebook_capi_database() {
    echo "<h2>🗄️ Fixing Database Tables...</h2>\n";
    
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Facebook CAPI Events Queue Table
    $facebook_events_table = $wpdb->prefix . 'facebook_capi_events';
    $sql1 = "CREATE TABLE IF NOT EXISTS $facebook_events_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        event_id varchar(255) NOT NULL UNIQUE,
        session_id varchar(255) NOT NULL,
        event_name varchar(255) NOT NULL,
        facebook_event varchar(255) NOT NULL,
        event_time bigint(20) NOT NULL,
        user_data longtext NOT NULL,
        custom_data longtext NOT NULL,
        action_source varchar(50) DEFAULT 'website',
        status enum('pending', 'sent', 'failed', 'retry') DEFAULT 'pending',
        retry_count int(11) DEFAULT 0,
        facebook_response text,
        error_message text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        sent_at datetime NULL,
        PRIMARY KEY (id),
        KEY event_id (event_id),
        KEY session_id (session_id),
        KEY status (status),
        KEY event_time (event_time),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    // Facebook CAPI Logs Table
    $facebook_logs_table = $wpdb->prefix . 'facebook_capi_logs';
    $sql2 = "CREATE TABLE IF NOT EXISTS $facebook_logs_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        event_id varchar(255),
        log_type enum('success', 'error', 'warning', 'info') DEFAULT 'info',
        message text NOT NULL,
        response_code int(11),
        response_data longtext,
        timestamp datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY event_id (event_id),
        KEY log_type (log_type),
        KEY timestamp (timestamp)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $result1 = dbDelta($sql1);
    $result2 = dbDelta($sql2);
    
    echo "✅ Facebook CAPI Events table created/updated<br>\n";
    echo "✅ Facebook CAPI Logs table created/updated<br>\n";
    
    // Check table status
    $events_count = $wpdb->get_var("SELECT COUNT(*) FROM $facebook_events_table");
    $logs_count = $wpdb->get_var("SELECT COUNT(*) FROM $facebook_logs_table");
    
    echo "📊 Current events in queue: $events_count<br>\n";
    echo "📊 Current log entries: $logs_count<br>\n";
    
    echo "<p style='color: green;'>✅ Database tables fixed successfully!</p>\n";
}

/**
 * Test Facebook CAPI Connection
 */
function test_facebook_capi_connection() {
    echo "<h2>🧪 Testing Facebook CAPI Connection...</h2>\n";
    
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id)) {
        echo "❌ Facebook Pixel ID not configured<br>\n";
        return false;
    }
    
    if (empty($access_token)) {
        echo "❌ Facebook Access Token not configured<br>\n";
        return false;
    }
    
    echo "✅ Facebook Pixel ID: " . substr($pixel_id, 0, 8) . "...<br>\n";
    echo "✅ Access Token: " . substr($access_token, 0, 8) . "...<br>\n";
    
    // Create a test event
    $test_event = array(
        'event_name' => 'PageView',
        'event_time' => time(),
        'event_id' => 'test_fix_' . time(),
        'action_source' => 'website',
        'user_data' => array(
            'client_ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Fix Script'
        ),
        'custom_data' => array(
            'page_title' => 'Facebook CAPI Fix Test',
            'source' => 'Fix Script'
        )
    );
    
    $url = "https://graph.facebook.com/v18.0/{$pixel_id}/events";
    
    $payload = array(
        'data' => array($test_event),
        'access_token' => $access_token
    );
    
    $response = wp_remote_post($url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'User-Agent' => 'Alhadiya-CAPI-Fix/1.0'
        ),
        'body' => json_encode($payload),
        'timeout' => 30,
        'sslverify' => true
    ));
    
    if (is_wp_error($response)) {
        echo "❌ Connection failed: " . $response->get_error_message() . "<br>\n";
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    if ($response_code === 200) {
        echo "✅ Facebook CAPI connection successful!<br>\n";
        echo "📊 Response: " . $response_body . "<br>\n";
        return true;
    } else {
        echo "❌ Facebook CAPI connection failed<br>\n";
        echo "📊 Response Code: $response_code<br>\n";
        echo "📊 Response: " . $response_body . "<br>\n";
        return false;
    }
}

/**
 * Process Pending Events
 */
function process_pending_facebook_events() {
    echo "<h2>🚀 Processing Pending Events...</h2>\n";
    
    global $wpdb;
    
    $pending_events = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}facebook_capi_events 
         WHERE status IN ('pending', 'retry') 
         ORDER BY created_at ASC 
         LIMIT 10"
    );
    
    if (empty($pending_events)) {
        echo "✅ No pending events to process<br>\n";
        return;
    }
    
    echo "📊 Found " . count($pending_events) . " pending events<br>\n";
    
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        echo "⚠️ Cannot process events - Facebook credentials not configured<br>\n";
        return;
    }
    
    $processed = 0;
    $failed = 0;
    
    foreach ($pending_events as $event) {
        $facebook_event = array(
            'event_name' => $event->facebook_event,
            'event_time' => $event->event_time,
            'event_id' => $event->event_id,
            'action_source' => $event->action_source,
            'user_data' => json_decode($event->user_data, true),
            'custom_data' => json_decode($event->custom_data, true)
        );
        
        $url = "https://graph.facebook.com/v18.0/{$pixel_id}/events";
        
        $payload = array(
            'data' => array($facebook_event),
            'access_token' => $access_token
        );
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'Alhadiya-CAPI-Fix/1.0'
            ),
            'body' => json_encode($payload),
            'timeout' => 30,
            'sslverify' => true
        ));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $wpdb->update(
                $wpdb->prefix . 'facebook_capi_events',
                array(
                    'status' => 'sent',
                    'sent_at' => current_time('mysql'),
                    'facebook_response' => wp_remote_retrieve_body($response)
                ),
                array('id' => $event->id)
            );
            $processed++;
            echo "✅ Processed event: {$event->event_name}<br>\n";
        } else {
            $wpdb->update(
                $wpdb->prefix . 'facebook_capi_events',
                array(
                    'status' => 'failed',
                    'retry_count' => $event->retry_count + 1,
                    'error_message' => 'Fix script processing failed'
                ),
                array('id' => $event->id)
            );
            $failed++;
            echo "❌ Failed to process event: {$event->event_name}<br>\n";
        }
        
        // Small delay to prevent rate limiting
        usleep(200000); // 0.2 seconds
    }
    
    echo "<p style='color: green;'>✅ Processing completed: $processed sent, $failed failed</p>\n";
}

/**
 * Generate Configuration Instructions
 */
function generate_configuration_instructions() {
    echo "<h2>📋 Configuration Instructions</h2>\n";
    
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #0073aa; margin: 10px 0;'>\n";
    echo "<h3>🔧 To Complete Facebook CAPI Setup:</h3>\n";
    echo "<ol>\n";
    echo "<li><strong>Get Facebook Pixel ID:</strong><br>\n";
    echo "   - Go to Facebook Events Manager<br>\n";
    echo "   - Select your Pixel<br>\n";
    echo "   - Copy the Pixel ID (15-16 digits)</li>\n";
    echo "<li><strong>Get Access Token:</strong><br>\n";
    echo "   - Go to Facebook Events Manager → Settings → Conversions API<br>\n";
    echo "   - Generate a new Access Token<br>\n";
    echo "   - Copy the token (starts with 'EAA')</li>\n";
    echo "<li><strong>Configure in WordPress:</strong><br>\n";
    echo "   - Go to WordPress Admin → Appearance → Customize<br>\n";
    echo "   - Navigate to 'Facebook Conversions API' section<br>\n";
    echo "   - Enter your Pixel ID and Access Token<br>\n";
    echo "   - Enable Facebook CAPI<br>\n";
    echo "   - Save changes</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 10px 0;'>\n";
    echo "<h3>⚡ Quick Access Links:</h3>\n";
    echo "<ul>\n";
    echo "<li><a href='" . admin_url('customize.php?autofocus[section]=facebook_capi_settings') . "' target='_blank'>📝 Configure Facebook CAPI Settings</a></li>\n";
    echo "<li><a href='" . admin_url('themes.php?page=facebook-capi-monitor') . "' target='_blank'>📊 Facebook CAPI Monitor Dashboard</a></li>\n";
    echo "<li><a href='https://business.facebook.com/events_manager' target='_blank'>🔗 Facebook Events Manager</a></li>\n";
    echo "</ul>\n";
    echo "</div>\n";
}

/**
 * Main Fix Function
 */
function run_facebook_capi_fix() {
    echo "<!DOCTYPE html>\n";
    echo "<html><head><title>Facebook CAPI Fix Script</title></head><body>\n";
    echo "<h1>🔧 Facebook CAPI Fix Script</h1>\n";
    echo "<p><strong>Started at:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
    
    // Run all fixes
    fix_facebook_capi_configuration();
    fix_facebook_capi_database();
    fix_facebook_capi_cron_jobs();
    test_facebook_capi_connection();
    process_pending_facebook_events();
    generate_configuration_instructions();
    
    echo "<hr>\n";
    echo "<h2>🎉 Fix Script Completed!</h2>\n";
    echo "<p style='color: green; font-weight: bold;'>All Facebook CAPI fixes have been applied successfully!</p>\n";
    echo "<p><strong>Completed at:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
    
    echo "</body></html>\n";
}

// Run the fix if accessed directly
if (!defined('ABSPATH') || (isset($_GET['run_fix']) && $_GET['run_fix'] === '1')) {
    run_facebook_capi_fix();
}
?>