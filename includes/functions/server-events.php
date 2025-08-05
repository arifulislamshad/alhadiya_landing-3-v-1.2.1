<?php
/**
 * Server Events and Tracking Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize server session
 */
function alhadiya_init_server_session() {
    if (!session_id()) {
        session_start();
    }
    
    if (!isset($_SESSION['alhadiya_server_session_id'])) {
        $_SESSION['alhadiya_server_session_id'] = uniqid('server_session_', true);
    }
    
    if (!isset($_SESSION['alhadiya_server_session_start'])) {
        $_SESSION['alhadiya_server_session_start'] = time();
    }
}
add_action('init', 'alhadiya_init_server_session');

/**
 * Log server event
 */
function alhadiya_log_server_event($event_name, $event_data = array(), $event_value = '') {
    global $wpdb;
    
    $session_id = $_SESSION['alhadiya_server_session_id'] ?? get_current_session_id();
    $event_id = uniqid('event_', true);
    $event_time = time();
    
    $table_name = $wpdb->prefix . 'server_events';
    
    $wpdb->insert(
        $table_name,
        array(
            'event_id' => $event_id,
            'session_id' => $session_id,
            'event_name' => $event_name,
            'event_data' => json_encode($event_data),
            'event_value' => $event_value,
            'user_data' => json_encode(alhadiya_collect_facebook_user_data()),
            'custom_data' => json_encode($event_data),
            'event_time' => $event_time,
            'created_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
    );
    
    return $event_id;
}

/**
 * Send to Facebook conversion API
 */
function alhadiya_send_to_facebook_conversion_api($event_log) {
    if (!get_theme_mod('enable_facebook_capi', true)) {
        return false;
    }
    
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_capi_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        return false;
    }
    
    $event_data = json_decode($event_log->event_data, true);
    $user_data = json_decode($event_log->user_data, true);
    
    $facebook_event = alhadiya_map_to_facebook_event($event_log->event_name);
    
    if (!$facebook_event) {
        return false;
    }
    
    $payload = array(
        'data' => array(
            array(
                'event_name' => $facebook_event,
                'event_time' => $event_log->event_time,
                'action_source' => 'website',
                'user_data' => $user_data,
                'custom_data' => $event_data
            )
        ),
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
 * Send to Google Analytics
 */
function alhadiya_send_to_google_analytics($event_log) {
    if (!get_theme_mod('enable_google_analytics', true)) {
        return false;
    }
    
    $ga_id = get_theme_mod('google_analytics_id', '');
    
    if (empty($ga_id)) {
        return false;
    }
    
    // Google Analytics 4 Measurement Protocol
    $payload = array(
        'client_id' => uniqid(),
        'events' => array(
            array(
                'name' => $event_log->event_name,
                'params' => json_decode($event_log->event_data, true)
            )
        )
    );
    
    $response = wp_remote_post(
        "https://www.google-analytics.com/mp/collect?measurement_id={$ga_id}&api_secret=YOUR_API_SECRET",
        array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($payload),
            'timeout' => 30
        )
    );
    
    return !is_wp_error($response);
}

/**
 * Send to Microsoft Clarity
 */
function alhadiya_send_to_microsoft_clarity($event_log) {
    if (!get_theme_mod('enable_microsoft_clarity', true)) {
        return false;
    }
    
    $clarity_id = get_theme_mod('microsoft_clarity_id', '');
    
    if (empty($clarity_id)) {
        return false;
    }
    
    // Microsoft Clarity custom events
    $event_data = json_decode($event_log->event_data, true);
    
    // Clarity events are typically sent via JavaScript
    // This is a server-side fallback
    return true;
}

/**
 * Handle server event
 */
function handle_alhadiya_server_event() {
    if (!isset($_POST['event_name']) || !isset($_POST['nonce'])) {
        wp_die();
    }
    
    if (!wp_verify_nonce($_POST['nonce'], 'alhadiya_server_event_nonce')) {
        wp_die();
    }
    
    $event_name = sanitize_text_field($_POST['event_name']);
    $event_data = isset($_POST['event_data']) ? $_POST['event_data'] : array();
    $event_value = isset($_POST['event_value']) ? sanitize_text_field($_POST['event_value']) : '';
    
    $event_id = alhadiya_log_server_event($event_name, $event_data, $event_value);
    
    wp_send_json_success(array('event_id' => $event_id));
}
add_action('wp_ajax_alhadiya_server_event', 'handle_alhadiya_server_event');
add_action('wp_ajax_nopriv_alhadiya_server_event', 'handle_alhadiya_server_event');

/**
 * Track enhanced event
 */
function track_enhanced_event($event_name, $event_data = array(), $event_value = '') {
    $event_id = alhadiya_log_server_event($event_name, $event_data, $event_value);
    
    // Send to external services
    global $wpdb;
    $table_name = $wpdb->prefix . 'server_events';
    $event_log = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE event_id = %s",
        $event_id
    ));
    
    if ($event_log) {
        // Send to Facebook CAPI
        if (get_theme_mod('enable_facebook_capi', true)) {
            alhadiya_send_to_facebook_conversion_api($event_log);
        }
        
        // Send to Google Analytics
        if (get_theme_mod('enable_google_analytics', true)) {
            alhadiya_send_to_google_analytics($event_log);
        }
        
        // Send to Microsoft Clarity
        if (get_theme_mod('enable_microsoft_clarity', true)) {
            alhadiya_send_to_microsoft_clarity($event_log);
        }
    }
    
    return $event_id;
}

/**
 * Initialize server tracking
 */
function alhadiya_init_server_tracking() {
    // Auto-track page view
    if (!is_admin() && !wp_doing_ajax()) {
        auto_track_server_page_view();
    }
}
add_action('init', 'alhadiya_init_server_tracking');

/**
 * Auto track server page view
 */
function auto_track_server_page_view() {
    $event_data = array(
        'page_url' => $_SERVER['REQUEST_URI'] ?? '',
        'page_title' => wp_get_document_title(),
        'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    );
    
    track_enhanced_event('page_view', $event_data);
}

/**
 * Batch process events
 */
function alhadiya_batch_process_events() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'server_events';
    $pending_events = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE status = 'pending' ORDER BY created_at ASC LIMIT 50"
    );
    
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
    }
}

/**
 * Force process all pending events
 */
function alhadiya_force_process_all_pending_events() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'server_events';
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
 * Add tracking scripts
 */
function alhadiya_add_tracking_scripts() {
    if (is_admin()) {
        return;
    }
    
    ?>
    <script>
    (function() {
        'use strict';
        
        // Track page view
        if (typeof ajax_object !== 'undefined') {
            jQuery.post(ajax_object.ajax_url, {
                action: 'alhadiya_server_event',
                event_name: 'page_view',
                event_data: {
                    page_url: window.location.href,
                    page_title: document.title,
                    referrer: document.referrer,
                    screen_width: screen.width,
                    screen_height: screen.height
                },
                nonce: ajax_object.server_event_nonce
            });
        }
        
        // Track time spent
        let startTime = Date.now();
        let timeSpent = 0;
        
        function updateTimeSpent() {
            timeSpent = Math.floor((Date.now() - startTime) / 1000);
            
            if (typeof ajax_object !== 'undefined' && ajax_object.session_id) {
                jQuery.post(ajax_object.ajax_url, {
                    action: 'track_time_spent',
                    session_id: ajax_object.session_id,
                    time_spent: timeSpent
                });
            }
        }
        
        // Update time spent every 30 seconds
        setInterval(updateTimeSpent, 30000);
        
        // Update on page unload
        window.addEventListener('beforeunload', updateTimeSpent);
        
        // Track custom events
        window.trackEvent = function(eventName, eventData, eventValue) {
            if (typeof ajax_object !== 'undefined') {
                jQuery.post(ajax_object.ajax_url, {
                    action: 'track_custom_event',
                    event_name: eventName,
                    event_data: JSON.stringify(eventData),
                    event_value: eventValue,
                    session_id: ajax_object.session_id,
                    nonce: ajax_object.event_nonce
                });
            }
        };
        
        // Track screen size
        function updateScreenSize() {
            if (typeof ajax_object !== 'undefined' && ajax_object.session_id) {
                jQuery.post(ajax_object.ajax_url, {
                    action: 'update_screen_size',
                    screen_width: screen.width,
                    screen_height: screen.height,
                    session_id: ajax_object.session_id,
                    nonce: ajax_object.screen_size_nonce
                });
            }
        }
        
        // Update screen size on resize
        window.addEventListener('resize', updateScreenSize);
        updateScreenSize();
        
    })();
    </script>
    <?php
}
add_action('wp_footer', 'alhadiya_add_tracking_scripts');

/**
 * Add GTM noscript
 */
function alhadiya_add_gtm_noscript() {
    $gtm_id = get_theme_mod('google_tag_manager_id', '');
    
    if (!empty($gtm_id)) {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}
add_action('wp_body_open', 'alhadiya_add_gtm_noscript');