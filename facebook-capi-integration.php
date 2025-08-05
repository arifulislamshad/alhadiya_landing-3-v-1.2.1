<?php
/**
 * Facebook Conversions API (CAPI) Integration for Alhadiya Project
 * 
 * This file implements comprehensive Facebook CAPI server-side tracking
 * with the exact JSON structure as specified in the requirements.
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// FACEBOOK CAPI CORE FUNCTIONS
// ========================================

/**
 * Initialize Facebook CAPI system
 */
function alhadiya_init_facebook_capi() {
    // Create Facebook CAPI tables
    alhadiya_create_facebook_capi_tables();
    
    // Clear any existing scheduled events first
    wp_clear_scheduled_hook('alhadiya_process_facebook_capi_batch');
    
    // Schedule batch processing only if Facebook CAPI is enabled and configured
    if (get_theme_mod('enable_facebook_capi', true)) {
        $pixel_id = get_theme_mod('facebook_pixel_id', '');
        $access_token = get_theme_mod('facebook_capi_access_token', '');
        
        if (!empty($pixel_id) && !empty($access_token)) {
            wp_schedule_event(time(), 'every_minute', 'alhadiya_process_facebook_capi_batch');
            alhadiya_log_facebook_capi('info', 'Facebook CAPI batch processing scheduled');
        } else {
            alhadiya_log_facebook_capi('warning', 'Facebook CAPI batch processing not scheduled - missing configuration');
        }
    }
}
add_action('init', 'alhadiya_init_facebook_capi');

/**
 * Create Facebook CAPI database tables
 */
function alhadiya_create_facebook_capi_tables() {
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
    dbDelta($sql1);
    dbDelta($sql2);
}

/**
 * Generate unique event ID for deduplication
 */
function alhadiya_generate_facebook_event_id($event_name, $session_id, $timestamp = null) {
    if (!$timestamp) {
        $timestamp = time();
    }
    
    $unique_string = $event_name . '_' . $timestamp . '_' . substr($session_id, -8);
    return $unique_string . '_' . substr(md5($unique_string . wp_salt()), 0, 8);
}

/**
 * Collect comprehensive user data for Facebook CAPI
 */
function alhadiya_collect_facebook_user_data() {
    $user_data = array();
    
    // Required fields
    $user_data['client_ip_address'] = alhadiya_get_client_ip();
    $user_data['client_user_agent'] = sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '');
    
    // Facebook cookies (FBC and FBP)
    if (isset($_COOKIE['_fbc'])) {
        $user_data['fbc'] = sanitize_text_field($_COOKIE['_fbc']);
    }
    if (isset($_COOKIE['_fbp'])) {
        $user_data['fbp'] = sanitize_text_field($_COOKIE['_fbp']);
    }
    
    // Hash personal information if available (from order data)
    global $wpdb;
    $session_id = alhadiya_init_server_session();
    
    $customer_data = $wpdb->get_row($wpdb->prepare(
        "SELECT customer_name, customer_phone FROM {$wpdb->prefix}device_tracking WHERE session_id = %s AND has_purchased = 1",
        $session_id
    ));
    
    if ($customer_data) {
        // Hash email if available (extract from customer data or form)
        if (!empty($customer_data->customer_name)) {
            $user_data['fn'] = hash('sha256', strtolower(trim($customer_data->customer_name)));
        }
        
        // Hash phone if available
        if (!empty($customer_data->customer_phone)) {
            $clean_phone = preg_replace('/[^0-9]/', '', $customer_data->customer_phone);
            $user_data['ph'] = hash('sha256', $clean_phone);
        }
    }
    
    // Location data from IP
    $location_data = alhadiya_get_location_from_ip($user_data['client_ip_address']);
    if ($location_data) {
        if (!empty($location_data['city'])) {
            $user_data['ct'] = hash('sha256', strtolower(trim($location_data['city'])));
        }
        if (!empty($location_data['country'])) {
            $user_data['country'] = hash('sha256', strtolower(trim($location_data['country'])));
        }
    }
    
    return array_filter($user_data); // Remove empty values
}

/**
 * Get location data from IP address
 */
function alhadiya_get_location_from_ip($ip) {
    // Use existing location function or implement IP geolocation
    $location_data = get_location_and_isp($ip);
    
    if ($location_data && $location_data['location'] !== 'Unknown') {
        $location_parts = explode(', ', $location_data['location']);
        return array(
            'city' => $location_parts[0] ?? '',
            'country' => $location_parts[1] ?? ''
        );
    }
    
    return null;
}

/**
 * Map alhadiya events to Facebook CAPI standard events
 */
function alhadiya_map_to_facebook_event($event_name) {
    $event_mapping = array(
        // Core events
        'page_view' => 'PageView',
        'scroll_depth' => 'scroll_depth', // Custom event
        'section_view' => 'ViewContent',
        'click_event' => 'click_event', // Custom event
        
        // Form events
        'form_start' => 'Lead',
        'form_submit' => 'CompleteRegistration',
        'input_focus' => 'input_focus', // Custom event
        'input_typing' => 'input_typing', // Custom event
        
        // E-commerce events
        'product_select' => 'ViewContent',
        'begin_checkout' => 'InitiateCheckout',
        'purchase_complete' => 'Purchase',
        'view_product' => 'ViewContent',
        
        // Video events
        'video_play' => 'ViewContent',
        
        // User behavior events
        'exit_intent' => 'exit_intent', // Custom event
        'idle_detected' => 'idle_detected', // Custom event
        
        // Device events
        'battery_low' => 'battery_low', // Custom event
        'memory_usage' => 'memory_usage', // Custom event
        'device_change' => 'device_change', // Custom event
        
        // Contact events
        'copy_text' => 'Contact',
        'error_detected' => 'error_detected', // Custom event
        'faq' => 'ViewContent',
        'phone_click' => 'Contact',
        'whatsapp_click' => 'Contact',
        
        // Order process events
        'delivery_option_click' => 'AddToCart',
        'payment_method_select' => 'AddPaymentInfo',
        'payment_number_copy' => 'Contact',
        'order_button_click' => 'InitiateCheckout',
        
        // Review events
        'customer_review' => 'ViewContent',
        'review_slider_view' => 'ViewContent'
    );
    
    return $event_mapping[$event_name] ?? $event_name;
}

/**
 * Format custom data based on event type
 */
function alhadiya_format_facebook_custom_data($event_name, $event_data) {
    $custom_data = array();
    
    switch ($event_name) {
        case 'page_view':
            $custom_data['page_title'] = $event_data['page_title'] ?? get_the_title();
            $custom_data['page_url'] = $event_data['page_url'] ?? get_permalink();
            break;
            
        case 'scroll_depth':
            $custom_data['scroll_percent'] = floatval($event_data['scroll_percentage'] ?? 0);
            $custom_data['page_url'] = $event_data['page_url'] ?? get_permalink();
            break;
            
        case 'section_view':
            $custom_data['section_name'] = $event_data['section_name'] ?? '';
            break;
            
        case 'click_event':
            $custom_data['element'] = $event_data['element_text'] ?? '';
            $custom_data['page_url'] = $event_data['page_url'] ?? get_permalink();
            break;
            
        case 'form_start':
            $custom_data['form_name'] = $event_data['form_id'] ?? 'Checkout';
            break;
            
        case 'form_submit':
            $custom_data['form_name'] = $event_data['form_id'] ?? 'Checkout';
            $custom_data['status'] = 'Success';
            break;
            
        case 'input_focus':
            $custom_data['input_name'] = $event_data['field_name'] ?? '';
            break;
            
        case 'input_typing':
            $custom_data['input_name'] = $event_data['field_name'] ?? '';
            break;
            
        case 'product_select':
            $custom_data['product_id'] = $event_data['product_id'] ?? '';
            $custom_data['product_name'] = $event_data['product_name'] ?? '';
            $custom_data['price'] = floatval($event_data['product_price'] ?? 0);
            $custom_data['content_ids'] = array($event_data['product_id'] ?? '');
            $custom_data['content_type'] = 'product';
            $custom_data['currency'] = 'BDT';
            break;
            
        case 'begin_checkout':
            $custom_data['cart_value'] = floatval($event_data['total_value'] ?? 0);
            $custom_data['product_count'] = intval($event_data['product_count'] ?? 1);
            $custom_data['value'] = floatval($event_data['total_value'] ?? 0);
            $custom_data['currency'] = 'BDT';
            $custom_data['content_ids'] = array($event_data['item_id'] ?? '');
            $custom_data['num_items'] = intval($event_data['product_count'] ?? 1);
            break;
            
        case 'purchase_complete':
            $custom_data['value'] = floatval($event_data['total_value'] ?? 0);
            $custom_data['currency'] = 'BDT';
            $custom_data['order_id'] = $event_data['order_id'] ?? '';
            $custom_data['content_ids'] = array($event_data['item_id'] ?? '');
            $custom_data['content_type'] = 'product';
            break;
            
        case 'view_product':
            $custom_data['product_id'] = $event_data['product_id'] ?? '';
            $custom_data['product_name'] = $event_data['product_name'] ?? '';
            $custom_data['content_ids'] = array($event_data['product_id'] ?? '');
            $custom_data['content_type'] = 'product';
            break;
            
        case 'video_play':
            $custom_data['video_id'] = $event_data['video_id'] ?? '';
            $custom_data['watched_seconds'] = intval($event_data['watched_seconds'] ?? 0);
            break;
            
        case 'exit_intent':
            $custom_data['page'] = $event_data['page'] ?? get_the_title();
            break;
            
        case 'idle_detected':
            $custom_data['idle_seconds'] = intval($event_data['idle_duration'] ?? 0);
            break;
            
        case 'battery_low':
            $custom_data['battery_percent'] = intval($event_data['battery_level'] ?? 0);
            break;
            
        case 'memory_usage':
            $custom_data['memory_used_mb'] = intval($event_data['memory_used_mb'] ?? 0);
            break;
            
        case 'device_change':
            $custom_data['from'] = $event_data['old_orientation'] ?? 'unknown';
            $custom_data['to'] = $event_data['new_orientation'] ?? 'unknown';
            break;
            
        case 'copy_text':
            $custom_data['text_preview'] = substr($event_data['copied_number'] ?? '', 0, 10) . '...';
            break;
            
        case 'error_detected':
            $custom_data['error_message'] = substr($event_data['error_message'] ?? '', 0, 100);
            break;
            
        case 'faq':
            $custom_data['question'] = $event_data['faq_title'] ?? '';
            break;
            
        case 'phone_click':
            $custom_data['phone_number'] = $event_data['phone_number'] ?? '';
            break;
            
        case 'whatsapp_click':
            $custom_data['wa_link'] = $event_data['whatsapp_url'] ?? '';
            break;
            
        case 'delivery_option_click':
            $custom_data['option'] = $event_data['delivery_type'] ?? '';
            break;
            
        case 'payment_method_select':
            $custom_data['method'] = $event_data['payment_name'] ?? '';
            break;
            
        case 'payment_number_copy':
            $custom_data['number'] = substr($event_data['copied_number'] ?? '', 0, 10) . '...';
            break;
            
        case 'order_button_click':
            $custom_data['label'] = $event_data['element_text'] ?? 'Confirm Order';
            break;
            
        case 'customer_review':
            $custom_data['rating'] = intval($event_data['rating'] ?? 5);
            $custom_data['review_excerpt'] = substr($event_data['review_text'] ?? '', 0, 50) . '...';
            break;
            
        case 'review_slider_view':
            $custom_data['section'] = 'Review Carousel';
            break;
            
        default:
            // For any unmapped events, include basic data
            $custom_data = array_merge($custom_data, $event_data);
            break;
    }
    
    return array_filter($custom_data); // Remove empty values
}

/**
 * Create Facebook CAPI event in standard format
 */
function alhadiya_create_facebook_capi_event($event_name, $event_data = array(), $session_id = null) {
    if (!$session_id) {
        $session_id = alhadiya_init_server_session();
    }
    
    $event_time = time();
    $event_id = alhadiya_generate_facebook_event_id($event_name, $session_id, $event_time);
    $facebook_event = alhadiya_map_to_facebook_event($event_name);
    
    $facebook_capi_event = array(
        'event_name' => $facebook_event,
        'event_time' => $event_time,
        'event_id' => $event_id,
        'action_source' => 'website',
        'user_data' => alhadiya_collect_facebook_user_data(),
        'custom_data' => alhadiya_format_facebook_custom_data($event_name, $event_data)
    );
    
    return $facebook_capi_event;
}

/**
 * Queue Facebook CAPI event for batch processing
 */
function alhadiya_queue_facebook_capi_event($event_name, $event_data = array(), $session_id = null) {
    // Check if Facebook CAPI is enabled (default to true for better UX)
    if (!get_theme_mod('enable_facebook_capi', true)) {
        return false;
    }
    
    // Check if we have minimum required configuration
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        // Log configuration issue
        alhadiya_log_facebook_capi('warning', 'Facebook CAPI not configured - missing Pixel ID or Access Token');
        return false;
    }
    
    global $wpdb;
    
    $facebook_event = alhadiya_create_facebook_capi_event($event_name, $event_data, $session_id);
    
    $result = $wpdb->insert(
        $wpdb->prefix . 'facebook_capi_events',
        array(
            'event_id' => $facebook_event['event_id'],
            'session_id' => $session_id ?: alhadiya_init_server_session(),
            'event_name' => $event_name,
            'facebook_event' => $facebook_event['event_name'],
            'event_time' => $facebook_event['event_time'],
            'user_data' => json_encode($facebook_event['user_data']),
            'custom_data' => json_encode($facebook_event['custom_data']),
            'action_source' => $facebook_event['action_source'],
            'status' => 'pending'
        )
    );
    
    if ($result) {
        alhadiya_log_facebook_capi('info', "Event queued: {$event_name}", $facebook_event['event_id']);
        
        // Try immediate sending if enabled
        if (get_theme_mod('facebook_capi_immediate_send', false)) {
            alhadiya_send_single_event_immediately($wpdb->insert_id);
        }
        
        return $facebook_event['event_id'];
    }
    
    return false;
}

/**
 * Send events to Facebook Conversions API
 */
function alhadiya_send_to_facebook_capi($events_batch) {
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    $test_event_code = get_theme_mod('facebook_test_event_code', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        alhadiya_log_facebook_capi('error', 'Facebook Pixel ID or Access Token not configured');
        return false;
    }
    
    $url = "https://graph.facebook.com/v18.0/{$pixel_id}/events";
    
    $payload = array(
        'data' => $events_batch,
        'access_token' => $access_token
    );
    
    // Add test event code if provided
    if (!empty($test_event_code)) {
        $payload['test_event_code'] = $test_event_code;
    }
    
    $response = wp_remote_post($url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'User-Agent' => 'Alhadiya-CAPI/1.0'
        ),
        'body' => json_encode($payload),
        'timeout' => 30,
        'sslverify' => true
    ));
    
    if (is_wp_error($response)) {
        alhadiya_log_facebook_capi('error', 'HTTP Error: ' . $response->get_error_message());
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $response_data = json_decode($response_body, true);
    
    if ($response_code === 200) {
        alhadiya_log_facebook_capi('success', 'Events sent successfully', null, $response_code, $response_data);
        return $response_data;
    } else {
        alhadiya_log_facebook_capi('error', 'API Error: ' . $response_body, null, $response_code, $response_data);
        return false;
    }
}

/**
 * Send single event immediately (bypass queue)
 */
function alhadiya_send_single_event_immediately($event_db_id) {
    global $wpdb;
    
    $event = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}facebook_capi_events WHERE id = %d",
        $event_db_id
    ));
    
    if (!$event) {
        return false;
    }
    
    $facebook_event = array(
        'event_name' => $event->facebook_event,
        'event_time' => $event->event_time,
        'event_id' => $event->event_id,
        'action_source' => $event->action_source,
        'user_data' => json_decode($event->user_data, true),
        'custom_data' => json_decode($event->custom_data, true)
    );
    
    $response = alhadiya_send_to_facebook_capi(array($facebook_event));
    
    if ($response !== false) {
        // Mark as sent
        $wpdb->update(
            $wpdb->prefix . 'facebook_capi_events',
            array(
                'status' => 'sent',
                'sent_at' => current_time('mysql'),
                'facebook_response' => json_encode($response)
            ),
            array('id' => $event_db_id)
        );
        
        alhadiya_log_facebook_capi('success', "Event sent immediately: {$event->event_name}", $event->event_id);
        return true;
    } else {
        // Mark for retry
        $wpdb->update(
            $wpdb->prefix . 'facebook_capi_events',
            array(
                'status' => 'retry',
                'retry_count' => $event->retry_count + 1,
                'error_message' => 'Failed to send immediately'
            ),
            array('id' => $event_db_id)
        );
        
        alhadiya_log_facebook_capi('error', "Failed to send event immediately: {$event->event_name}", $event->event_id);
        return false;
    }
}

/**
 * Process Facebook CAPI events batch
 */
function alhadiya_process_facebook_capi_batch() {
    if (!get_theme_mod('enable_facebook_capi', true)) {
        return;
    }
    
    // Check if we have minimum required configuration
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        alhadiya_log_facebook_capi('warning', 'Batch processing skipped - Facebook CAPI not configured');
        return;
    }
    
    global $wpdb;
    
    $batch_size = get_theme_mod('facebook_capi_batch_size', 100);
    $max_retries = get_theme_mod('facebook_capi_max_retries', 3);
    
    // Get pending events
    $pending_events = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}facebook_capi_events 
         WHERE status IN ('pending', 'retry') AND retry_count < %d 
         ORDER BY created_at ASC 
         LIMIT %d",
        $max_retries,
        $batch_size
    ));
    
    if (empty($pending_events)) {
        return;
    }
    
    // Prepare events for Facebook CAPI
    $facebook_events = array();
    $event_ids = array();
    
    foreach ($pending_events as $event) {
        $facebook_events[] = array(
            'event_name' => $event->facebook_event,
            'event_time' => $event->event_time,
            'event_id' => $event->event_id,
            'action_source' => $event->action_source,
            'user_data' => json_decode($event->user_data, true),
            'custom_data' => json_decode($event->custom_data, true)
        );
        $event_ids[] = $event->id;
    }
    
    // Send to Facebook CAPI
    $response = alhadiya_send_to_facebook_capi($facebook_events);
    
    if ($response !== false) {
        // Mark events as sent
        $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}facebook_capi_events 
             SET status = 'sent', sent_at = NOW(), facebook_response = %s 
             WHERE id IN (" . implode(',', array_fill(0, count($event_ids), '%d')) . ")",
            json_encode($response),
            ...$event_ids
        ));
        
        alhadiya_log_facebook_capi('success', 'Batch processed successfully: ' . count($facebook_events) . ' events');
    } else {
        // Mark events for retry or failed
        foreach ($pending_events as $event) {
            $new_retry_count = $event->retry_count + 1;
            $new_status = ($new_retry_count >= $max_retries) ? 'failed' : 'retry';
            
            $wpdb->update(
                $wpdb->prefix . 'facebook_capi_events',
                array(
                    'status' => $new_status,
                    'retry_count' => $new_retry_count,
                    'error_message' => 'Failed to send to Facebook CAPI'
                ),
                array('id' => $event->id)
            );
        }
        
        alhadiya_log_facebook_capi('error', 'Batch processing failed: ' . count($facebook_events) . ' events');
    }
}
add_action('alhadiya_process_facebook_capi_batch', 'alhadiya_process_facebook_capi_batch');

/**
 * Log Facebook CAPI activities
 */
function alhadiya_log_facebook_capi($type, $message, $event_id = null, $response_code = null, $response_data = null) {
    global $wpdb;
    
    $wpdb->insert(
        $wpdb->prefix . 'facebook_capi_logs',
        array(
            'event_id' => $event_id,
            'log_type' => $type,
            'message' => $message,
            'response_code' => $response_code,
            'response_data' => $response_data ? json_encode($response_data) : null
        )
    );
    
    // Also log to WordPress error log for debugging
    if ($type === 'error') {
        error_log("Facebook CAPI Error: {$message}");
    }
}

/**
 * Enhanced server event handler with Facebook CAPI integration
 */
function alhadiya_enhanced_server_event_handler() {
    // Verify nonce
    if (!isset($_POST['server_event_nonce']) || !wp_verify_nonce($_POST['server_event_nonce'], 'alhadiya_server_event_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    $event_name = sanitize_text_field($_POST['event_name'] ?? '');
    $event_data = $_POST['event_data'] ?? array();
    $event_value = sanitize_text_field($_POST['event_value'] ?? '');
    
    if (empty($event_name)) {
        wp_send_json_error('Event name is required');
        return;
    }
    
    $session_id = alhadiya_init_server_session();
    
    // Log to existing server events table
    $result = alhadiya_log_server_event($event_name, $event_data, $event_value);
    
    // Queue for Facebook CAPI
    $facebook_event_id = alhadiya_queue_facebook_capi_event($event_name, $event_data, $session_id);
    
    if ($result && $facebook_event_id) {
        wp_send_json_success(array(
            'message' => 'Event logged and queued for Facebook CAPI',
            'facebook_event_id' => $facebook_event_id,
            'session_id' => $session_id
        ));
    } else {
        wp_send_json_error('Failed to process event');
    }
}

// Replace the existing server event handler
remove_action('wp_ajax_alhadiya_server_event', 'alhadiya_handle_server_event');
remove_action('wp_ajax_nopriv_alhadiya_server_event', 'alhadiya_handle_server_event');
add_action('wp_ajax_alhadiya_server_event', 'alhadiya_enhanced_server_event_handler');
add_action('wp_ajax_nopriv_alhadiya_server_event', 'alhadiya_enhanced_server_event_handler');

/**
 * Get Facebook CAPI statistics
 */
function alhadiya_get_facebook_capi_stats() {
    global $wpdb;
    
    $stats = array();
    
    // Total events
    $stats['total_events'] = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}facebook_capi_events"
    );
    
    // Events by status
    $status_counts = $wpdb->get_results(
        "SELECT status, COUNT(*) as count FROM {$wpdb->prefix}facebook_capi_events GROUP BY status"
    );
    
    foreach ($status_counts as $status) {
        $stats[$status->status] = $status->count;
    }
    
    // Today's events
    $stats['today_events'] = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}facebook_capi_events WHERE DATE(created_at) = CURDATE()"
    );
    
    // Success rate
    $sent_events = $stats['sent'] ?? 0;
    $total_processed = ($stats['sent'] ?? 0) + ($stats['failed'] ?? 0);
    $stats['success_rate'] = $total_processed > 0 ? round(($sent_events / $total_processed) * 100, 2) : 0;
    
    return $stats;
}

// ========================================
// WORDPRESS CUSTOMIZER INTEGRATION
// ========================================

/**
 * Add Facebook CAPI settings to WordPress Customizer
 */
function alhadiya_add_facebook_capi_customizer_settings($wp_customize) {
    // Facebook CAPI Section
    $wp_customize->add_section('facebook_capi_settings', array(
        'title' => __('Facebook Conversions API', 'alhadiya'),
        'priority' => 41,
        'description' => __('Configure Facebook Conversions API for server-side tracking', 'alhadiya')
    ));
    
    // Enable Facebook CAPI
    $wp_customize->add_setting('enable_facebook_capi', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean'
    ));
    $wp_customize->add_control('enable_facebook_capi', array(
        'label' => __('Enable Facebook CAPI', 'alhadiya'),
        'section' => 'facebook_capi_settings',
        'type' => 'checkbox',
        'description' => __('Enable Facebook Conversions API server-side tracking', 'alhadiya')
    ));
    
    // Facebook Pixel ID (reuse existing or create new)
    if (!$wp_customize->get_setting('facebook_pixel_id')) {
        $wp_customize->add_setting('facebook_pixel_id', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        $wp_customize->add_control('facebook_pixel_id', array(
            'label' => __('Facebook Pixel ID', 'alhadiya'),
            'section' => 'facebook_capi_settings',
            'type' => 'text',
            'description' => __('Your Facebook Pixel ID (required for CAPI)', 'alhadiya')
        ));
    }
    
    // Facebook CAPI Access Token
    $wp_customize->add_setting('facebook_capi_access_token', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('facebook_capi_access_token', array(
        'label' => __('Facebook CAPI Access Token', 'alhadiya'),
        'section' => 'facebook_capi_settings',
        'type' => 'password',
        'description' => __('Your Facebook Conversions API Access Token', 'alhadiya')
    ));
    
    // Test Event Code
    $wp_customize->add_setting('facebook_test_event_code', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('facebook_test_event_code', array(
        'label' => __('Test Event Code', 'alhadiya'),
        'section' => 'facebook_capi_settings',
        'type' => 'text',
        'description' => __('Facebook Test Event Code for testing (optional)', 'alhadiya')
    ));
    
    // Batch Size
    $wp_customize->add_setting('facebook_capi_batch_size', array(
        'default' => 100,
        'sanitize_callback' => 'absint'
    ));
    $wp_customize->add_control('facebook_capi_batch_size', array(
        'label' => __('Batch Size', 'alhadiya'),
        'section' => 'facebook_capi_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 1000,
            'step' => 1
        ),
        'description' => __('Number of events to send in each batch (1-1000)', 'alhadiya')
    ));
    
    // Max Retries
    $wp_customize->add_setting('facebook_capi_max_retries', array(
        'default' => 3,
        'sanitize_callback' => 'absint'
    ));
    $wp_customize->add_control('facebook_capi_max_retries', array(
        'label' => __('Max Retries', 'alhadiya'),
        'section' => 'facebook_capi_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10,
            'step' => 1
        ),
        'description' => __('Maximum retry attempts for failed events', 'alhadiya')
    ));
    
    // Immediate Send Option
    $wp_customize->add_setting('facebook_capi_immediate_send', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean'
    ));
    $wp_customize->add_control('facebook_capi_immediate_send', array(
        'label' => __('Immediate Send', 'alhadiya'),
        'section' => 'facebook_capi_settings',
        'type' => 'checkbox',
        'description' => __('Send events immediately instead of queuing (bypass cron)', 'alhadiya')
    ));
}
add_action('customize_register', 'alhadiya_add_facebook_capi_customizer_settings');

// ========================================
// ADMIN DASHBOARD AND MONITORING
// ========================================

/**
 * Add Facebook CAPI admin menu
 */
function alhadiya_add_facebook_capi_admin_menu() {
    add_submenu_page(
        'themes.php',
        'Facebook CAPI Monitor',
        'Facebook CAPI',
        'manage_options',
        'facebook-capi-monitor',
        'alhadiya_facebook_capi_admin_page'
    );
}
add_action('admin_menu', 'alhadiya_add_facebook_capi_admin_menu');

/**
 * Facebook CAPI admin page
 */
function alhadiya_facebook_capi_admin_page() {
    $stats = alhadiya_get_facebook_capi_stats();
    $is_enabled = get_theme_mod('enable_facebook_capi', false);
    
    ?>
    <div class="wrap">
        <h1>Facebook Conversions API Monitor</h1>
        
        <div class="notice notice-info">
            <p><strong>Status:</strong> <?php echo $is_enabled ? '<span style="color: green;">Enabled</span>' : '<span style="color: red;">Disabled</span>'; ?></p>
            <?php if (!$is_enabled): ?>
                <p>Enable Facebook CAPI in <a href="<?php echo admin_url('customize.php?autofocus[section]=facebook_capi_settings'); ?>">Customizer → Facebook Conversions API</a></p>
            <?php endif; ?>
        </div>
        
        <?php if ($is_enabled): ?>
        <div class="dashboard-widgets-wrap">
            <div class="metabox-holder">
                <div class="postbox-container" style="width: 100%;">
                    
                    <!-- Statistics -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Statistics</span></h2>
                        <div class="inside">
                            <table class="widefat">
                                <tr>
                                    <td><strong>Total Events:</strong></td>
                                    <td><?php echo number_format($stats['total_events'] ?? 0); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Today's Events:</strong></td>
                                    <td><?php echo number_format($stats['today_events'] ?? 0); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Pending:</strong></td>
                                    <td><?php echo number_format($stats['pending'] ?? 0); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Sent:</strong></td>
                                    <td style="color: green;"><?php echo number_format($stats['sent'] ?? 0); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Failed:</strong></td>
                                    <td style="color: red;"><?php echo number_format($stats['failed'] ?? 0); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Success Rate:</strong></td>
                                    <td><?php echo $stats['success_rate']; ?>%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Test Event -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Test Facebook CAPI</span></h2>
                        <div class="inside">
                            <form method="post" action="">
                                <?php wp_nonce_field('facebook_capi_test', 'facebook_capi_test_nonce'); ?>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Event Type</th>
                                        <td>
                                            <select name="test_event_type">
                                                <option value="page_view">Page View</option>
                                                <option value="form_submit">Form Submit</option>
                                                <option value="purchase_complete">Purchase Complete</option>
                                                <option value="begin_checkout">Begin Checkout</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Test Data</th>
                                        <td>
                                            <textarea name="test_event_data" rows="5" cols="50" placeholder='{"page_title": "Test Page", "value": 100}'></textarea>
                                            <p class="description">JSON format event data (optional)</p>
                                        </td>
                                    </tr>
                                </table>
                                <p class="submit">
                                    <input type="submit" name="send_test_event" class="button-primary" value="Send Test Event" />
                                </p>
                            </form>
                            
                            <?php
                            if (isset($_POST['send_test_event']) && wp_verify_nonce($_POST['facebook_capi_test_nonce'], 'facebook_capi_test')) {
                                $test_event_type = sanitize_text_field($_POST['test_event_type']);
                                $test_event_data = json_decode(stripslashes($_POST['test_event_data']), true) ?: array();
                                
                                $test_result = alhadiya_queue_facebook_capi_event($test_event_type, $test_event_data);
                                
                                if ($test_result) {
                                    echo '<div class="notice notice-success"><p>Test event queued successfully! Event ID: ' . $test_result . '</p></div>';
                                } else {
                                    echo '<div class="notice notice-error"><p>Failed to queue test event. Check your configuration.</p></div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Admin Controls -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Admin Controls</span></h2>
                        <div class="inside">
                            <p><strong>Manual Actions:</strong></p>
                            <p>
                                <a href="<?php echo wp_nonce_url(add_query_arg('trigger_facebook_batch', '1'), 'trigger_facebook_batch'); ?>"
                                   class="button button-secondary">🔄 Trigger Batch Processing</a>
                                
                                <a href="<?php echo add_query_arg('force_process_facebook_events', '1'); ?>"
                                   class="button button-secondary"
                                   onclick="return confirm('This will process ALL pending events immediately. Continue?')">
                                   ⚡ Force Process All Pending Events
                                </a>
                            </p>
                            
                            <?php if (isset($_GET['batch_triggered'])): ?>
                                <div class="notice notice-success inline"><p>✅ Batch processing triggered successfully!</p></div>
                            <?php endif; ?>
                            
                            <?php if (isset($_GET['force_processed'])): ?>
                                <div class="notice notice-success inline"><p>✅ Force processed <?php echo intval($_GET['force_processed']); ?> events!</p></div>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <p><strong>Configuration Status:</strong></p>
                            <ul>
                                <li>Facebook Pixel ID: <?php echo !empty(get_theme_mod('facebook_pixel_id')) ? '✅ Configured' : '❌ Missing'; ?></li>
                                <li>Access Token: <?php echo !empty(get_theme_mod('facebook_capi_access_token')) ? '✅ Configured' : '❌ Missing'; ?></li>
                                <li>Immediate Send: <?php echo get_theme_mod('facebook_capi_immediate_send', false) ? '✅ Enabled' : '❌ Disabled'; ?></li>
                                <li>Cron Jobs: <?php echo wp_next_scheduled('alhadiya_process_facebook_capi_batch') ? '✅ Scheduled' : '❌ Not Scheduled'; ?></li>
                            </ul>
                            
                            <?php if (empty(get_theme_mod('facebook_pixel_id')) || empty(get_theme_mod('facebook_capi_access_token'))): ?>
                                <div class="notice notice-warning inline">
                                    <p><strong>⚠️ Configuration Required:</strong> Please configure your Facebook Pixel ID and Access Token in
                                    <a href="<?php echo admin_url('customize.php?autofocus[section]=facebook_capi_settings'); ?>">Customizer → Facebook Conversions API</a></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Events -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Recent Events</span></h2>
                        <div class="inside">
                            <?php
                            global $wpdb;
                            $recent_events = $wpdb->get_results(
                                "SELECT * FROM {$wpdb->prefix}facebook_capi_events
                                 ORDER BY created_at DESC
                                 LIMIT 10"
                            );
                            
                            if ($recent_events): ?>
                                <table class="widefat striped">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Facebook Event</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Retries</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_events as $event): ?>
                                        <tr>
                                            <td><?php echo esc_html($event->event_name); ?></td>
                                            <td><?php echo esc_html($event->facebook_event); ?></td>
                                            <td>
                                                <span class="status-<?php echo $event->status; ?>">
                                                    <?php echo ucfirst($event->status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo esc_html($event->created_at); ?></td>
                                            <td><?php echo $event->retry_count; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No events found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Logs -->
                    <div class="postbox">
                        <h2 class="hndle"><span>Recent Logs</span></h2>
                        <div class="inside">
                            <?php
                            $recent_logs = $wpdb->get_results(
                                "SELECT * FROM {$wpdb->prefix}facebook_capi_logs
                                 ORDER BY timestamp DESC
                                 LIMIT 20"
                            );
                            
                            if ($recent_logs): ?>
                                <table class="widefat striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Message</th>
                                            <th>Event ID</th>
                                            <th>Response Code</th>
                                            <th>Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_logs as $log): ?>
                                        <tr>
                                            <td>
                                                <span class="log-<?php echo $log->log_type; ?>">
                                                    <?php echo ucfirst($log->log_type); ?>
                                                </span>
                                            </td>
                                            <td><?php echo esc_html($log->message); ?></td>
                                            <td><?php echo esc_html($log->event_id ?: '-'); ?></td>
                                            <td><?php echo esc_html($log->response_code ?: '-'); ?></td>
                                            <td><?php echo esc_html($log->timestamp); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No logs found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <style>
        .status-pending { color: orange; }
        .status-sent { color: green; }
        .status-failed { color: red; }
        .status-retry { color: blue; }
        .log-success { color: green; font-weight: bold; }
        .log-error { color: red; font-weight: bold; }
        .log-warning { color: orange; font-weight: bold; }
        .log-info { color: blue; }
        </style>
    </div>
    <?php
}

// ========================================
// CRON AND SCHEDULING
// ========================================

/**
 * Add custom cron interval for Facebook CAPI processing
 */
function alhadiya_add_facebook_capi_cron_intervals($schedules) {
    $schedules['every_minute'] = array(
        'interval' => 60,
        'display' => __('Every Minute', 'alhadiya')
    );
    
    $schedules['every_five_minutes'] = array(
        'interval' => 300,
        'display' => __('Every 5 Minutes', 'alhadiya')
    );
    
    $schedules['every_thirty_seconds'] = array(
        'interval' => 30,
        'display' => __('Every 30 Seconds', 'alhadiya')
    );
    
    return $schedules;
}
add_filter('cron_schedules', 'alhadiya_add_facebook_capi_cron_intervals');

/**
 * Manual trigger for Facebook CAPI batch processing
 */
function alhadiya_manual_trigger_facebook_batch() {
    if (current_user_can('manage_options') && isset($_GET['trigger_facebook_batch'])) {
        if (wp_verify_nonce($_GET['_wpnonce'], 'trigger_facebook_batch')) {
            $processed = alhadiya_process_facebook_capi_batch();
            
            $redirect_url = remove_query_arg(array('trigger_facebook_batch', '_wpnonce'));
            $redirect_url = add_query_arg('batch_triggered', '1', $redirect_url);
            
            wp_redirect($redirect_url);
            exit;
        }
    }
}
add_action('admin_init', 'alhadiya_manual_trigger_facebook_batch');

/**
 * Force process all pending events (admin action)
 */
function alhadiya_force_process_all_pending_events() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    global $wpdb;
    
    $pending_events = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}facebook_capi_events
         WHERE status IN ('pending', 'retry')
         ORDER BY created_at ASC"
    );
    
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
        
        $response = alhadiya_send_to_facebook_capi(array($facebook_event));
        
        if ($response !== false) {
            $wpdb->update(
                $wpdb->prefix . 'facebook_capi_events',
                array(
                    'status' => 'sent',
                    'sent_at' => current_time('mysql'),
                    'facebook_response' => json_encode($response)
                ),
                array('id' => $event->id)
            );
            $processed++;
        } else {
            $wpdb->update(
                $wpdb->prefix . 'facebook_capi_events',
                array(
                    'status' => 'failed',
                    'retry_count' => $event->retry_count + 1,
                    'error_message' => 'Force processing failed'
                ),
                array('id' => $event->id)
            );
            $failed++;
        }
        
        // Add small delay to prevent rate limiting
        usleep(100000); // 0.1 second
    }
    
    alhadiya_log_facebook_capi('info', "Force processing completed: {$processed} sent, {$failed} failed");
    
    wp_redirect(add_query_arg('force_processed', $processed, wp_get_referer()));
    exit;
}

if (isset($_GET['force_process_facebook_events']) && current_user_can('manage_options')) {
    add_action('admin_init', 'alhadiya_force_process_all_pending_events');
}

/**
 * Clean up old Facebook CAPI events and logs
 */
function alhadiya_cleanup_facebook_capi_data() {
    global $wpdb;
    
    $retention_days = 30; // Keep data for 30 days
    
    // Clean up old events
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}facebook_capi_events
         WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
        $retention_days
    ));
    
    // Clean up old logs
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}facebook_capi_logs
         WHERE timestamp < DATE_SUB(NOW(), INTERVAL %d DAY)",
        $retention_days
    ));
}

// Schedule cleanup to run daily
if (!wp_next_scheduled('alhadiya_cleanup_facebook_capi_data')) {
    wp_schedule_event(time(), 'daily', 'alhadiya_cleanup_facebook_capi_data');
}
add_action('alhadiya_cleanup_facebook_capi_data', 'alhadiya_cleanup_facebook_capi_data');

// ========================================
// UTILITY FUNCTIONS
// ========================================

/**
 * Get client IP address
 */
function alhadiya_get_client_ip() {
    $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
    
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
    
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * Test Facebook CAPI connection
 */
function alhadiya_test_facebook_capi_connection() {
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        return array(
            'success' => false,
            'message' => 'Facebook Pixel ID or Access Token not configured'
        );
    }
    
    // Create a test event
    $test_event = array(
        'event_name' => 'PageView',
        'event_time' => time(),
        'event_id' => 'test_' . time(),
        'action_source' => 'website',
        'user_data' => array(
            'client_ip_address' => alhadiya_get_client_ip(),
            'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Test Agent'
        ),
        'custom_data' => array(
            'page_title' => 'Test Page',
            'source' => 'Facebook CAPI Test'
        )
    );
    
    $response = alhadiya_send_to_facebook_capi(array($test_event));
    
    if ($response !== false) {
        return array(
            'success' => true,
            'message' => 'Facebook CAPI connection successful',
            'response' => $response
        );
    } else {
        return array(
            'success' => false,
            'message' => 'Facebook CAPI connection failed'
        );
    }
}

/**
 * Manual trigger for batch processing (for testing)
 */
function alhadiya_manual_facebook_capi_batch_process() {
    if (current_user_can('manage_options') && isset($_GET['trigger_facebook_batch']) && wp_verify_nonce($_GET['_wpnonce'], 'trigger_facebook_batch')) {
        alhadiya_process_facebook_capi_batch();
        wp_redirect(add_query_arg('batch_processed', '1', remove_query_arg(array('trigger_facebook_batch', '_wpnonce'))));
        exit;
    }
}
add_action('admin_init', 'alhadiya_manual_facebook_capi_batch_process');

// ========================================
// INTEGRATION HOOKS
// ========================================

/**
 * Hook into existing WooCommerce events if WooCommerce is active
 */
if (class_exists('WooCommerce')) {
    
    // Purchase completed
    add_action('woocommerce_thankyou', function($order_id) {
        if (!$order_id) return;
        
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        $event_data = array(
            'order_id' => $order_id,
            'total_value' => $order->get_total(),
            'currency' => $order->get_currency(),
            'item_id' => 'henna_course',
            'product_count' => $order->get_item_count()
        );
        
        alhadiya_queue_facebook_capi_event('purchase_complete', $event_data);
    });
    
    // Checkout initiated
    add_action('woocommerce_checkout_process', function() {
        $event_data = array(
            'total_value' => WC()->cart->get_total(''),
            'currency' => get_woocommerce_currency(),
            'item_id' => 'henna_course',
            'product_count' => WC()->cart->get_cart_contents_count()
        );
        
        alhadiya_queue_facebook_capi_event('begin_checkout', $event_data);
    });
}

/**
 * Hook into Contact Form 7 submissions if CF7 is active
 */
if (function_exists('wpcf7_add_form_tag')) {
    add_action('wpcf7_mail_sent', function($contact_form) {
        $event_data = array(
            'form_id' => $contact_form->id(),
            'form_title' => $contact_form->title()
        );
        
        alhadiya_queue_facebook_capi_event('form_submit', $event_data);
    });
}

// ========================================
// ACTIVATION AND DEACTIVATION HOOKS
// ========================================

/**
 * Plugin activation
 */
function alhadiya_facebook_capi_activate() {
    alhadiya_create_facebook_capi_tables();
    
    // Schedule cron jobs
    if (!wp_next_scheduled('alhadiya_process_facebook_capi_batch')) {
        wp_schedule_event(time(), 'every_minute', 'alhadiya_process_facebook_capi_batch');
    }
    
    if (!wp_next_scheduled('alhadiya_cleanup_facebook_capi_data')) {
        wp_schedule_event(time(), 'daily', 'alhadiya_cleanup_facebook_capi_data');
    }
}

/**
 * Plugin deactivation
 */
function alhadiya_facebook_capi_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('alhadiya_process_facebook_capi_batch');
    wp_clear_scheduled_hook('alhadiya_cleanup_facebook_capi_data');
}

// Register activation/deactivation hooks if this is used as a plugin
// register_activation_hook(__FILE__, 'alhadiya_facebook_capi_activate');
// register_deactivation_hook(__FILE__, 'alhadiya_facebook_capi_deactivate');

// ========================================
// DEBUG AND DEVELOPMENT HELPERS
// ========================================

/**
 * Add Facebook CAPI debug information to admin bar
 */
function alhadiya_add_facebook_capi_admin_bar($wp_admin_bar) {
    if (!current_user_can('manage_options') || !get_theme_mod('enable_facebook_capi', false)) {
        return;
    }
    
    $stats = alhadiya_get_facebook_capi_stats();
    $pending_count = $stats['pending'] ?? 0;
    
    $wp_admin_bar->add_node(array(
        'id' => 'facebook-capi-status',
        'title' => 'FB CAPI: ' . $pending_count . ' pending',
        'href' => admin_url('themes.php?page=facebook-capi-monitor'),
        'meta' => array(
            'class' => $pending_count > 100 ? 'facebook-capi-warning' : 'facebook-capi-normal'
        )
    ));
}
add_action('admin_bar_menu', 'alhadiya_add_facebook_capi_admin_bar', 999);

/**
 * Add admin bar styles
 */
function alhadiya_facebook_capi_admin_bar_styles() {
    if (!is_admin_bar_showing() || !current_user_can('manage_options')) {
        return;
    }
    ?>
    <style>
    .facebook-capi-warning { background-color: #ff6b6b !important; }
    .facebook-capi-normal { background-color: #51cf66 !important; }
    </style>
    <?php
}
add_action('wp_head', 'alhadiya_facebook_capi_admin_bar_styles');
add_action('admin_head', 'alhadiya_facebook_capi_admin_bar_styles');

// ========================================
// END OF FACEBOOK CAPI INTEGRATION
// ========================================

?>