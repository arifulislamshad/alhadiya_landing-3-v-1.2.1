<?php
/**
 * Comprehensive Fix Script for WordPress Theme Tracking System
 * This script addresses all remaining issues in functions.php and index.php
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// FIX 1: ENSURE PROPER NONCE LOCALIZATION
// ========================================

// Remove the duplicate nonce localization and ensure proper one
remove_action('wp_enqueue_scripts', 'alhadiya_add_server_tracking_nonce', 20);

// Add proper nonce localization
function alhadiya_fix_nonce_localization() {
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
add_action('wp_enqueue_scripts', 'alhadiya_fix_nonce_localization', 15);

// ========================================
// FIX 2: ENSURE DATABASE TABLES EXIST
// ========================================

// Force create tables on every page load during development
function alhadiya_force_create_tables() {
    global $wpdb;
    
    // Device tracking table
    $device_tracking_table = $wpdb->prefix . 'device_tracking';
    $charset_collate = $wpdb->get_charset_collate();
    
    $device_sql = "CREATE TABLE IF NOT EXISTS $device_tracking_table (
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
    
    // Device events table
    $device_events_table = $wpdb->prefix . 'device_events';
    $events_sql = "CREATE TABLE IF NOT EXISTS $device_events_table (
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
    
    // Server events table
    $server_events_table = $wpdb->prefix . 'server_events';
    $server_sql = "CREATE TABLE IF NOT EXISTS $server_events_table (
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
    
    // Create tables
    dbDelta($device_sql);
    dbDelta($events_sql);
    dbDelta($server_sql);
    
    // Add missing columns if they don't exist
    $columns_to_add = array(
        'language' => 'VARCHAR(50) DEFAULT NULL',
        'timezone' => 'VARCHAR(100) DEFAULT NULL',
        'connection_type' => 'VARCHAR(50) DEFAULT NULL',
        'battery_level' => 'DECIMAL(5,2) DEFAULT NULL',
        'battery_charging' => 'TINYINT(1) DEFAULT NULL',
        'memory_info' => 'DECIMAL(5,2) DEFAULT NULL',
        'cpu_cores' => 'INT DEFAULT NULL',
        'touchscreen_detected' => 'TINYINT(1) DEFAULT NULL'
    );
    
    foreach ($columns_to_add as $column => $definition) {
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $device_tracking_table LIKE '$column'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $device_tracking_table ADD COLUMN $column $definition");
        }
    }
}
add_action('init', 'alhadiya_force_create_tables');

// ========================================
// FIX 3: IMPROVE SESSION MANAGEMENT
// ========================================

// Enhanced session initialization
function alhadiya_enhanced_session_init() {
    // Check if we already have a session ID
    if (isset($_COOKIE['device_session'])) {
        $session_id = sanitize_text_field($_COOKIE['device_session']);
        
        // Validate session ID format
        if (preg_match('/^(session_|ss_)/', $session_id)) {
            return $session_id;
        }
    }
    
    // Generate new session ID
    $session_id = 'session_' . uniqid() . '_' . time();
    
    // Set cookie
    $cookie_domain = parse_url(get_site_url(), PHP_URL_HOST);
    setcookie('device_session', $session_id, time() + (86400 * 30), '/', $cookie_domain, is_ssl(), false);
    
    return $session_id;
}

// ========================================
// FIX 4: IMPROVE DEVICE TRACKING
// ========================================

// Enhanced device tracking function
function alhadiya_enhanced_device_tracking() {
    if (!get_theme_mod('enable_device_tracking', true)) {
        return false;
    }
    
    global $wpdb;
    
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
    $ip = get_client_ip();
    $referrer = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';
    
    // Get session ID
    $session_id = alhadiya_enhanced_session_init();
    
    // Parse user agent
    $device_info = parse_user_agent($user_agent);
    
    // Get location and ISP
    $location_data = get_location_and_isp($ip);
    
    $table_name = $wpdb->prefix . 'device_tracking';
    
    // Check if session exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE session_id = %s",
        $session_id
    ));
    
    if ($existing) {
        // Update existing record
        $wpdb->update(
            $table_name,
            array(
                'visit_count' => $existing->visit_count + 1,
                'pages_viewed' => $existing->pages_viewed + 1,
                'last_visit' => current_time('mysql')
            ),
            array('session_id' => $session_id)
        );
    } else {
        // Insert new record
        $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'ip_address' => $ip,
                'user_agent' => $user_agent,
                'device_type' => $device_info['device_type'],
                'device_model' => $device_info['device_model'],
                'browser' => $device_info['browser'],
                'os' => $device_info['os'],
                'location' => $location_data['location'],
                'isp' => $location_data['isp'],
                'referrer' => $referrer,
                'facebook_id' => '',
                'first_visit' => current_time('mysql'),
                'last_visit' => current_time('mysql')
            )
        );
    }
    
    return array(
        'session_id' => $session_id,
        'ip' => $ip,
        'user_agent' => $user_agent,
        'device_info' => $device_info,
        'location_data' => $location_data,
        'referrer' => $referrer,
        'facebook_id' => '',
        'timestamp' => current_time('mysql')
    );
}

// ========================================
// FIX 5: IMPROVE AJAX HANDLERS
// ========================================

// Enhanced server event handler
function alhadiya_enhanced_server_event_handler() {
    if (!isset($_POST['server_event_nonce']) || !wp_verify_nonce($_POST['server_event_nonce'], 'alhadiya_server_event_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    $event_name = isset($_POST['event_name']) ? sanitize_text_field($_POST['event_name']) : '';
    $event_data = isset($_POST['event_data']) ? $_POST['event_data'] : array();
    $event_value = isset($_POST['event_value']) ? sanitize_text_field($_POST['event_value']) : '';
    
    if (empty($event_name)) {
        wp_send_json_error('Event name is required');
        return;
    }
    
    // Get session ID
    $session_id = alhadiya_enhanced_session_init();
    
    // Log to server events table
    global $wpdb;
    $table_name = $wpdb->prefix . 'server_events';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'session_id' => $session_id,
            'event_name' => $event_name,
            'event_data' => json_encode($event_data),
            'event_value' => $event_value,
            'user_ip' => get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '',
            'page_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'timestamp' => current_time('mysql')
        )
    );
    
    if ($result) {
        wp_send_json_success('Event logged successfully');
    } else {
        wp_send_json_error('Failed to log event');
    }
}

// Remove old handler and add new one
remove_action('wp_ajax_alhadiya_server_event', 'alhadiya_handle_server_event');
remove_action('wp_ajax_nopriv_alhadiya_server_event', 'alhadiya_handle_server_event');
add_action('wp_ajax_alhadiya_server_event', 'alhadiya_enhanced_server_event_handler');
add_action('wp_ajax_nopriv_alhadiya_server_event', 'alhadiya_enhanced_server_event_handler');

// ========================================
// FIX 6: ADD MISSING HELPER FUNCTIONS
// ========================================

// Get client IP function
if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
}

// Parse user agent function
if (!function_exists('parse_user_agent')) {
    function parse_user_agent($user_agent) {
        $device_type = 'Desktop';
        $device_model = 'Unknown';
        $browser = 'Unknown';
        $os = 'Unknown';
        
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i', $user_agent)) {
            $device_type = 'Mobile';
            
            if (preg_match('/iPhone/i', $user_agent)) {
                $device_model = 'iPhone';
                if (preg_match('/iPhone OS ([0-9_]+)/i', $user_agent, $matches)) {
                    $os = 'iOS ' . str_replace('_', '.', $matches[1]);
                }
            } elseif (preg_match('/iPad/i', $user_agent)) {
                $device_model = 'iPad';
                $device_type = 'Tablet';
            } elseif (preg_match('/Android/i', $user_agent)) {
                $device_model = 'Android Device';
                if (preg_match('/Android ([0-9.]+)/i', $user_agent, $matches)) {
                    $os = 'Android ' . $matches[1];
                }
            }
        }
        
        if (preg_match('/Chrome/i', $user_agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $user_agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $user_agent)) {
            $browser = 'Edge';
        }
        
        if ($device_type == 'Desktop') {
            if (preg_match('/Windows NT ([0-9.]+)/i', $user_agent, $matches)) {
                $os = 'Windows ' . $matches[1];
            } elseif (preg_match('/Mac OS X ([0-9_]+)/i', $user_agent, $matches)) {
                $os = 'macOS ' . str_replace('_', '.', $matches[1]);
            } elseif (preg_match('/Linux/i', $user_agent)) {
                $os = 'Linux';
            }
        }
        
        return array(
            'device_type' => $device_type,
            'device_model' => $device_model,
            'browser' => $browser,
            'os' => $os
        );
    }
}

// Get location and ISP function
if (!function_exists('get_location_and_isp')) {
    function get_location_and_isp($ip) {
        $location = 'Unknown';
        $isp = 'Unknown';
        
        $services = array(
            "http://ipapi.co/{$ip}/json/",
            "http://ip-api.com/json/{$ip}",
            "https://ipinfo.io/{$ip}/json"
        );
        
        foreach ($services as $service) {
            $response = wp_remote_get($service, array('timeout' => 5));
            
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                
                if ($data) {
                    if (isset($data['city']) && isset($data['country'])) {
                        $location = $data['city'] . ', ' . $data['country'];
                    }
                    if (isset($data['org'])) {
                        $isp = $data['org'];
                    } elseif (isset($data['isp'])) {
                        $isp = $data['isp'];
                    }
                    break;
                }
            }
        }
        
        return array(
            'location' => $location,
            'isp' => $isp
        );
    }
}

// ========================================
// FIX 7: REPLACE TRACKING FUNCTIONS
// ========================================

// Replace the main tracking function
function alhadiya_replace_tracking_function() {
    return alhadiya_enhanced_device_tracking();
}

// Hook the replacement
add_action('wp', function() {
    if (!is_admin() && get_theme_mod('enable_device_tracking', true)) {
        alhadiya_replace_tracking_function();
    }
});

// ========================================
// FIX 8: ADD DEBUGGING
// ========================================

// Add debugging function
function alhadiya_debug_tracking() {
    if (current_user_can('manage_options')) {
        error_log('Alhadiya Tracking Debug: Session ID = ' . alhadiya_enhanced_session_init());
        error_log('Alhadiya Tracking Debug: Device tracking enabled = ' . (get_theme_mod('enable_device_tracking', true) ? 'true' : 'false'));
    }
}
add_action('init', 'alhadiya_debug_tracking');

// ========================================
// FIX 9: ENSURE COMPATIBILITY
// ========================================

// Ensure compatibility with existing code
function alhadiya_ensure_compatibility() {
    // Make sure track_enhanced_device_info function exists
    if (!function_exists('track_enhanced_device_info')) {
        function track_enhanced_device_info() {
            return alhadiya_enhanced_device_tracking();
        }
    }
    
    // Make sure alhadiya_init_server_session function exists
    if (!function_exists('alhadiya_init_server_session')) {
        function alhadiya_init_server_session() {
            return alhadiya_enhanced_session_init();
        }
    }
}
add_action('init', 'alhadiya_ensure_compatibility');

echo "Comprehensive fix script loaded successfully!";
?>