<?php
/**
 * WooCommerce Integration Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle WooCommerce order
 */
function handle_woocommerce_order($order_id) {
    if (!$order_id) {
        return;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    // Block IP after order to prevent spam
    $ip = get_client_ip();
    block_ip_after_order($ip, 5, 'minutes');
    
    // Track order event
    $session_id = get_current_session_id();
    $event_data = array(
        'order_id' => $order_id,
        'order_total' => $order->get_total(),
        'order_currency' => $order->get_currency(),
        'payment_method' => $order->get_payment_method(),
        'shipping_method' => $order->get_shipping_method(),
        'customer_id' => $order->get_customer_id(),
        'billing_email' => $order->get_billing_email(),
        'billing_phone' => $order->get_billing_phone(),
        'billing_country' => $order->get_billing_country(),
        'billing_city' => $order->get_billing_city()
    );
    
    // Track server event
    track_enhanced_event('purchase', $event_data, $order->get_total());
    
    // Send to Facebook CAPI
    if (get_theme_mod('enable_facebook_capi', true)) {
        alhadiya_queue_facebook_capi_event('Purchase', $event_data, $session_id);
    }
    
    // Send to Google Analytics
    if (get_theme_mod('enable_google_analytics', true)) {
        alhadiya_send_to_google_analytics(array(
            'event_name' => 'purchase',
            'event_data' => $event_data,
            'session_id' => $session_id
        ));
    }
    
    // Send to Microsoft Clarity
    if (get_theme_mod('enable_microsoft_clarity', true)) {
        alhadiya_send_to_microsoft_clarity(array(
            'event_name' => 'purchase',
            'event_data' => $event_data,
            'session_id' => $session_id
        ));
    }
}
add_action('woocommerce_thankyou', 'handle_woocommerce_order');

/**
 * Add custom order columns
 */
function add_custom_order_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $column) {
        $new_columns[$key] = $column;
        if ($key === 'order_status') {
            $new_columns['tracking_info'] = __('Tracking Info', 'alhadiya');
        }
    }
    
    return $new_columns;
}
add_filter('manage_edit-shop_order_columns', 'add_custom_order_columns');

/**
 * Populate custom order columns
 */
function populate_custom_order_columns($column, $post_id) {
    if ($column === 'tracking_info') {
        $order = wc_get_order($post_id);
        if ($order) {
            $session_id = get_post_meta($post_id, '_session_id', true);
            $ip_address = get_post_meta($post_id, '_ip_address', true);
            
            if ($session_id) {
                echo '<strong>Session:</strong> ' . esc_html(substr($session_id, 0, 10)) . '...<br>';
            }
            if ($ip_address) {
                echo '<strong>IP:</strong> ' . esc_html($ip_address);
            }
        }
    }
}
add_action('manage_shop_order_posts_custom_column', 'populate_custom_order_columns', 10, 2);

/**
 * Save order tracking info
 */
function save_order_tracking_info($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    $session_id = get_current_session_id();
    $ip_address = get_client_ip();
    
    update_post_meta($order_id, '_session_id', $session_id);
    update_post_meta($order_id, '_ip_address', $ip_address);
}
add_action('woocommerce_checkout_order_processed', 'save_order_tracking_info');

/**
 * Initialize WooCommerce session
 */
function alhadiya_init_woocommerce_session() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Ensure WooCommerce session is started
    if (!WC()->session) {
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
    }
}
add_action('init', 'alhadiya_init_woocommerce_session', 5);