<?php
/**
 * Alhadiya Theme Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load the modular structure
require_once get_template_directory() . '/includes/loader.php';

// Legacy functions that need to be kept for backward compatibility
// These functions are now handled by the modular structure but kept here for any existing references

/**
 * Legacy function for backward compatibility
 * @deprecated Use track_enhanced_device_info() instead
 */
function track_enhanced_device_info_v2() {
    return track_enhanced_device_info();
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_collect_facebook_user_data() instead
 */
function alhadiya_collect_facebook_user_data() {
    $ip = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    return array(
        'em' => hash('sha256', 'user@example.com'), // Placeholder email
        'ph' => hash('sha256', '+8801737146996'), // Placeholder phone
        'external_id' => uniqid(),
        'client_ip_address' => $ip,
        'client_user_agent' => $user_agent,
        'fbc' => 'fb.1.' . time() . '.' . uniqid(),
        'fbp' => 'fb.1.' . time() . '.' . uniqid(),
        'fbc' => 'fb.1.' . time() . '.' . uniqid()
    );
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_map_to_facebook_event() instead
 */
function alhadiya_map_to_facebook_event($event_name) {
    $mapping = array(
        'page_view' => 'PageView',
        'purchase' => 'Purchase',
        'add_to_cart' => 'AddToCart',
        'initiate_checkout' => 'InitiateCheckout',
        'view_content' => 'ViewContent',
        'lead' => 'Lead',
        'contact' => 'Contact'
    );
    
    return isset($mapping[$event_name]) ? $mapping[$event_name] : false;
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_queue_facebook_capi_event() instead
 */
function alhadiya_queue_facebook_capi_event($event_name, $event_data = array(), $session_id = null) {
    if (!get_theme_mod('enable_facebook_capi', true)) {
        return false;
    }
    
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        return false;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_capi_events';
    
    $event_id = uniqid('fb_event_', true);
    $event_time = time();
    
    $wpdb->insert(
        $table_name,
        array(
            'event_id' => $event_id,
            'session_id' => $session_id ?: get_current_session_id(),
            'event_name' => $event_name,
            'facebook_event' => alhadiya_map_to_facebook_event($event_name),
            'event_time' => $event_time,
            'user_data' => json_encode(alhadiya_collect_facebook_user_data()),
            'custom_data' => json_encode($event_data),
            'created_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s')
    );
    
    return $event_id;
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_force_process_all_pending_events() instead
 */
function alhadiya_force_process_all_pending_events() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'facebook_capi_events';
    $pending_events = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE status = 'pending' ORDER BY created_at ASC"
    );
    
    $processed = 0;
    $failed = 0;
    
    foreach ($pending_events as $event) {
        $success = false;
        
        // Try to send to Facebook CAPI
        if (get_theme_mod('enable_facebook_capi', true)) {
            $success = alhadiya_send_to_facebook_conversion_api($event);
        }
        
        // Update status
        $status = $success ? 'sent' : 'failed';
        $sent_at = $success ? current_time('mysql') : null;
        
        $wpdb->update(
            $table_name,
            array(
                'status' => $status,
                'sent_at' => $sent_at
            ),
            array('event_id' => $event->event_id),
            array('%s', '%s'),
            array('%s')
        );
        
        if ($success) {
            $processed++;
        } else {
            $failed++;
        }
    }
    
    return array('processed' => $processed, 'failed' => $failed);
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_log_facebook_capi() instead
 */
function alhadiya_log_facebook_capi($type, $message, $event_id = null, $response_code = null, $response_data = null) {
    global $wpdb;
    
    $logs_table = $wpdb->prefix . 'facebook_capi_logs';
    
    $wpdb->insert(
        $logs_table,
        array(
            'event_id' => $event_id,
            'log_type' => $type,
            'message' => $message,
            'response_code' => $response_code,
            'response_data' => $response_data ? json_encode($response_data) : null,
            'timestamp' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%d', '%s', '%s')
    );
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_send_to_facebook_capi() instead
 */
function alhadiya_send_to_facebook_capi($events_batch) {
    if (!get_theme_mod('enable_facebook_capi', true)) {
        return false;
    }
    
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        return false;
    }
    
    $payload = array(
        'data' => $events_batch,
        'access_token' => $access_token
    );
    
    $response = wp_remote_post(
        "https://graph.facebook.com/v17.0/{$pixel_id}/events",
        array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($payload),
            'timeout' => 30
        )
    );
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    return isset($data['events_received']) && $data['events_received'] > 0;
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_send_single_event_immediately() instead
 */
function alhadiya_send_single_event_immediately($event_db_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'facebook_capi_events';
    $event = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $event_db_id
    ));
    
    if (!$event) {
        return false;
    }
    
    $success = alhadiya_send_to_facebook_conversion_api($event);
    
    // Update status
    $status = $success ? 'sent' : 'failed';
    $sent_at = $success ? current_time('mysql') : null;
    
    $wpdb->update(
        $table_name,
        array(
            'status' => $status,
            'sent_at' => $sent_at
        ),
        array('id' => $event_db_id),
        array('%s', '%s'),
        array('%d')
    );
    
    return $success;
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_process_facebook_capi_batch() instead
 */
function alhadiya_process_facebook_capi_batch() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'facebook_capi_events';
    $pending_events = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE status = 'pending' ORDER BY created_at ASC LIMIT 50"
    );
    
    if (empty($pending_events)) {
        return;
    }
    
    $events_batch = array();
    
    foreach ($pending_events as $event) {
        $event_data = json_decode($event->custom_data, true);
        $user_data = json_decode($event->user_data, true);
        
        $events_batch[] = array(
            'event_name' => $event->facebook_event,
            'event_time' => $event->event_time,
            'action_source' => 'website',
            'user_data' => $user_data,
            'custom_data' => $event_data
        );
    }
    
    $success = alhadiya_send_to_facebook_capi($events_batch);
    
    if ($success) {
        // Mark events as sent
        $event_ids = array_column($pending_events, 'event_id');
        $wpdb->query("UPDATE $table_name SET status = 'sent', sent_at = NOW() WHERE event_id IN ('" . implode("','", $event_ids) . "')");
    }
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_add_facebook_capi_cron_intervals() instead
 */
function alhadiya_add_facebook_capi_cron_intervals($schedules) {
    $schedules['every_minute'] = array(
        'interval' => 60,
        'display' => __('Every Minute', 'alhadiya')
    );
    
    return $schedules;
}
add_filter('cron_schedules', 'alhadiya_add_facebook_capi_cron_intervals');

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_manual_trigger_facebook_batch() instead
 */
function alhadiya_manual_trigger_facebook_batch() {
    alhadiya_process_facebook_capi_batch();
    wp_die(__('Facebook CAPI batch processing completed.', 'alhadiya'));
}
add_action('wp_ajax_manual_facebook_batch', 'alhadiya_manual_trigger_facebook_batch');

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_cleanup_facebook_capi_data() instead
 */
function alhadiya_cleanup_facebook_capi_data() {
    global $wpdb;
    
    $events_table = $wpdb->prefix . 'facebook_capi_events';
    $logs_table = $wpdb->prefix . 'facebook_capi_logs';
    
    // Delete old events (older than 30 days)
    $wpdb->query("DELETE FROM $events_table WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    
    // Delete old logs (older than 7 days)
    $wpdb->query("DELETE FROM $logs_table WHERE timestamp < DATE_SUB(NOW(), INTERVAL 7 DAY)");
}
add_action('wp_scheduled_delete', 'alhadiya_cleanup_facebook_capi_data');

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_test_facebook_capi_connection() instead
 */
function alhadiya_test_facebook_capi_connection() {
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        wp_send_json_error(__('Facebook CAPI not configured.', 'alhadiya'));
    }
    
    $test_event = array(
        'event_name' => 'PageView',
        'event_time' => time(),
        'action_source' => 'website',
        'user_data' => alhadiya_collect_facebook_user_data(),
        'custom_data' => array('test' => true)
    );
    
    $payload = array(
        'data' => array($test_event),
        'access_token' => $access_token
    );
    
    $response = wp_remote_post(
        "https://graph.facebook.com/v17.0/{$pixel_id}/events",
        array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($payload),
            'timeout' => 30
        )
    );
    
    if (is_wp_error($response)) {
        wp_send_json_error(__('Connection failed.', 'alhadiya'));
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (isset($data['events_received']) && $data['events_received'] > 0) {
        wp_send_json_success(__('Connection successful!', 'alhadiya'));
    } else {
        wp_send_json_error(__('Connection failed.', 'alhadiya'));
    }
}
add_action('wp_ajax_test_facebook_capi', 'alhadiya_test_facebook_capi_connection');

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_facebook_capi_activate() instead
 */
function alhadiya_facebook_capi_activate() {
    alhadiya_create_facebook_capi_tables();
    
    if (!wp_next_scheduled('alhadiya_process_facebook_capi_batch')) {
        wp_schedule_event(time(), 'every_minute', 'alhadiya_process_facebook_capi_batch');
    }
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_facebook_capi_deactivate() instead
 */
function alhadiya_facebook_capi_deactivate() {
    wp_clear_scheduled_hook('alhadiya_process_facebook_capi_batch');
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_add_facebook_capi_admin_bar() instead
 */
function alhadiya_add_facebook_capi_admin_bar($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_menu(array(
        'id' => 'facebook-capi',
        'title' => __('Facebook CAPI', 'alhadiya'),
        'href' => admin_url('admin.php?page=facebook-capi')
    ));
}
add_action('admin_bar_menu', 'alhadiya_add_facebook_capi_admin_bar', 100);

/**
 * Legacy function for backward compatibility
 * @deprecated Use alhadiya_facebook_capi_admin_bar_styles() instead
 */
function alhadiya_facebook_capi_admin_bar_styles() {
    ?>
    <style>
    #wp-admin-bar-facebook-capi .ab-item {
        background-color: #1877f2 !important;
        color: white !important;
    }
    #wp-admin-bar-facebook-capi .ab-item:hover {
        background-color: #166fe5 !important;
    }
    </style>
    <?php
}
add_action('admin_head', 'alhadiya_facebook_capi_admin_bar_styles');
