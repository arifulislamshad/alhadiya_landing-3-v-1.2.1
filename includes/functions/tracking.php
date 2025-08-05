<?php
/**
 * Tracking Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ensure tracking tables exist
 */
function ensure_tracking_tables_exist() {
    create_device_tracking_table();
    create_device_events_table();
    create_server_events_table();
}
add_action('init', 'ensure_tracking_tables_exist');

/**
 * Create device tracking table
 */
function create_device_tracking_table() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'device_tracking';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        session_id varchar(255) NOT NULL,
        ip_address varchar(45) NOT NULL,
        user_agent text NOT NULL,
        browser varchar(100) NOT NULL,
        os varchar(100) NOT NULL,
        device_type varchar(50) NOT NULL,
        screen_width int(11) DEFAULT NULL,
        screen_height int(11) DEFAULT NULL,
        country varchar(100) DEFAULT 'Unknown',
        city varchar(100) DEFAULT 'Unknown',
        isp varchar(200) DEFAULT 'Unknown',
        timezone varchar(100) DEFAULT 'Unknown',
        language varchar(10) DEFAULT 'Unknown',
        referrer text,
        landing_page varchar(500) NOT NULL,
        first_visit_time datetime DEFAULT CURRENT_TIMESTAMP,
        last_activity_time datetime DEFAULT CURRENT_TIMESTAMP,
        time_spent int(11) DEFAULT 0,
        page_views int(11) DEFAULT 1,
        is_bot tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY session_id (session_id),
        KEY ip_address (ip_address),
        KEY device_type (device_type),
        KEY country (country),
        KEY created_at (created_at),
        KEY last_activity_time (last_activity_time)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Create device events table
 */
function create_device_events_table() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'device_events';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        session_id varchar(255) NOT NULL,
        event_name varchar(255) NOT NULL,
        event_data longtext,
        event_value varchar(500),
        event_time datetime DEFAULT CURRENT_TIMESTAMP,
        page_url varchar(500),
        user_agent text,
        ip_address varchar(45),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY session_id (session_id),
        KEY event_name (event_name),
        KEY event_time (event_time),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Create server events table
 */
function create_server_events_table() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'server_events';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        event_id varchar(255) NOT NULL UNIQUE,
        session_id varchar(255) NOT NULL,
        event_name varchar(255) NOT NULL,
        event_data longtext,
        event_value varchar(500),
        user_data longtext,
        custom_data longtext,
        action_source varchar(50) DEFAULT 'website',
        status enum('pending', 'sent', 'failed', 'retry') DEFAULT 'pending',
        retry_count int(11) DEFAULT 0,
        facebook_response text,
        error_message text,
        event_time bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        sent_at datetime NULL,
        PRIMARY KEY (id),
        KEY event_id (event_id),
        KEY session_id (session_id),
        KEY event_name (event_name),
        KEY status (status),
        KEY event_time (event_time),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Track enhanced device info
 */
function track_enhanced_device_info() {
    if (check_ip_blocking()) {
        return array('session_id' => null, 'blocked' => true);
    }
    
    global $wpdb;
    
    $session_id = get_current_session_id();
    $ip_address = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $parsed_ua = parse_user_agent($user_agent);
    $location_data = get_location_and_isp($ip_address);
    
    $device_data = array(
        'session_id' => $session_id,
        'ip_address' => $ip_address,
        'user_agent' => $user_agent,
        'browser' => $parsed_ua['browser'],
        'os' => $parsed_ua['os'],
        'device_type' => $parsed_ua['device'],
        'country' => $location_data['country'],
        'city' => $location_data['city'],
        'isp' => $location_data['isp'],
        'timezone' => get_user_timezone(),
        'language' => get_user_language(),
        'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
        'landing_page' => $_SERVER['REQUEST_URI'] ?? '',
        'is_bot' => is_bot_user_agent($user_agent) ? 1 : 0
    );
    
    $table_name = $wpdb->prefix . 'device_tracking';
    
    // Check if session already exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id FROM $table_name WHERE session_id = %s",
        $session_id
    ));
    
    if ($existing) {
        // Update existing session
        $wpdb->update(
            $table_name,
            array(
                'last_activity_time' => current_time('mysql'),
                'page_views' => $wpdb->prepare('page_views + 1', array()),
                'updated_at' => current_time('mysql')
            ),
            array('session_id' => $session_id),
            array('%s', '%d', '%s'),
            array('%s')
        );
    } else {
        // Insert new session
        $wpdb->insert(
            $table_name,
            array_merge($device_data, array(
                'first_visit_time' => current_time('mysql'),
                'last_activity_time' => current_time('mysql'),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }
    
    return array('session_id' => $session_id, 'blocked' => false);
}

/**
 * Track time spent
 */
function track_time_spent() {
    if (!isset($_POST['session_id']) || !isset($_POST['time_spent'])) {
        wp_die();
    }
    
    $session_id = sanitize_text_field($_POST['session_id']);
    $time_spent = intval($_POST['time_spent']);
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    
    $wpdb->update(
        $table_name,
        array(
            'time_spent' => $time_spent,
            'last_activity_time' => current_time('mysql')
        ),
        array('session_id' => $session_id),
        array('%d', '%s'),
        array('%s')
    );
    
    wp_die();
}
add_action('wp_ajax_track_time_spent', 'track_time_spent');
add_action('wp_ajax_nopriv_track_time_spent', 'track_time_spent');

/**
 * Track custom event
 */
function track_custom_event() {
    if (!isset($_POST['event_name']) || !isset($_POST['session_id'])) {
        wp_die();
    }
    
    $event_name = sanitize_text_field($_POST['event_name']);
    $session_id = sanitize_text_field($_POST['session_id']);
    $event_data = isset($_POST['event_data']) ? sanitize_textarea_field($_POST['event_data']) : '';
    $event_value = isset($_POST['event_value']) ? sanitize_text_field($_POST['event_value']) : '';
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_events';
    
    $wpdb->insert(
        $table_name,
        array(
            'session_id' => $session_id,
            'event_name' => $event_name,
            'event_data' => $event_data,
            'event_value' => $event_value,
            'page_url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip_address' => get_client_ip(),
            'created_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    wp_die();
}
add_action('wp_ajax_track_custom_event', 'track_custom_event');
add_action('wp_ajax_nopriv_track_custom_event', 'track_custom_event');

/**
 * Update device screen size
 */
function update_device_screen_size() {
    if (!isset($_POST['screen_width']) || !isset($_POST['screen_height']) || !isset($_POST['session_id'])) {
        wp_die();
    }
    
    $screen_width = intval($_POST['screen_width']);
    $screen_height = intval($_POST['screen_height']);
    $session_id = sanitize_text_field($_POST['session_id']);
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    
    $wpdb->update(
        $table_name,
        array(
            'screen_width' => $screen_width,
            'screen_height' => $screen_height
        ),
        array('session_id' => $session_id),
        array('%d', '%d'),
        array('%s')
    );
    
    wp_die();
}
add_action('wp_ajax_update_screen_size', 'update_device_screen_size');
add_action('wp_ajax_nopriv_update_screen_size', 'update_device_screen_size');

/**
 * Get current session ID
 */
function get_current_session_id() {
    if (!isset($_COOKIE['alhadiya_session'])) {
        $session_id = uniqid('session_', true);
        setcookie('alhadiya_session', $session_id, time() + (86400 * 30), '/');
    } else {
        $session_id = $_COOKIE['alhadiya_session'];
    }
    
    return $session_id;
}

/**
 * Get user IP address
 */
function get_user_ip_address() {
    return get_client_ip();
}

/**
 * Get browser name
 */
function get_browser_name($user_agent) {
    $parsed = parse_user_agent($user_agent);
    return $parsed['browser'];
}

/**
 * Get device type from user agent
 */
function get_device_type_from_user_agent($user_agent) {
    $parsed = parse_user_agent($user_agent);
    return $parsed['device'];
}

/**
 * Get operating system
 */
function get_operating_system($user_agent) {
    $parsed = parse_user_agent($user_agent);
    return $parsed['os'];
}

/**
 * Get user timezone
 */
function get_user_timezone() {
    return 'Asia/Dhaka'; // Default to Bangladesh timezone
}

/**
 * Get user language
 */
function get_user_language() {
    return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
}

/**
 * Check if user agent is a bot
 */
function is_bot_user_agent($user_agent) {
    $bot_patterns = array(
        '/bot/i', '/crawler/i', '/spider/i', '/scraper/i',
        '/googlebot/i', '/bingbot/i', '/yandex/i', '/baiduspider/i',
        '/facebookexternalhit/i', '/twitterbot/i', '/linkedinbot/i'
    );
    
    foreach ($bot_patterns as $pattern) {
        if (preg_match($pattern, $user_agent)) {
            return true;
        }
    }
    
    return false;
}