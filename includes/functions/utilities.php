<?php
/**
 * Utility Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get WooCommerce product data
 */
function get_wc_product_data() {
    if (!class_exists('WooCommerce')) {
        return array();
    }
    
    $products = wc_get_products(array(
        'status' => 'publish',
        'limit' => -1,
    ));
    
    $product_data = array();
    foreach ($products as $product) {
        $product_data[] = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'type' => $product->get_type(),
            'status' => $product->get_status(),
        );
    }
    
    return $product_data;
}

/**
 * Get YouTube embed URL
 */
function get_youtube_embed_url($url) {
    if (empty($url)) {
        return false;
    }
    
    // Handle different YouTube URL formats
    $patterns = array(
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
        '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
    );
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
    }
    
    return false;
}

/**
 * Sanitize checkbox
 */
function sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Get client IP address
 */
function get_client_ip() {
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
    
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
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Check if IP is blocked
 */
function check_ip_blocking() {
    $ip = get_client_ip();
    $blocked_ips = get_option('alhadiya_blocked_ips', array());
    
    if (isset($blocked_ips[$ip])) {
        $block_until = $blocked_ips[$ip];
        if (time() < $block_until) {
            return true;
        } else {
            unset($blocked_ips[$ip]);
            update_option('alhadiya_blocked_ips', $blocked_ips);
        }
    }
    
    return false;
}

/**
 * Block IP after order
 */
function block_ip_after_order($ip, $time = 5, $unit = 'minutes') {
    $blocked_ips = get_option('alhadiya_blocked_ips', array());
    
    switch ($unit) {
        case 'seconds':
            $block_until = time() + $time;
            break;
        case 'minutes':
            $block_until = time() + ($time * 60);
            break;
        case 'hours':
            $block_until = time() + ($time * 3600);
            break;
        case 'days':
            $block_until = time() + ($time * 86400);
            break;
        default:
            $block_until = time() + ($time * 60);
    }
    
    $blocked_ips[$ip] = $block_until;
    update_option('alhadiya_blocked_ips', $blocked_ips);
}

/**
 * Parse user agent
 */
function parse_user_agent($user_agent) {
    $browser = 'Unknown';
    $os = 'Unknown';
    $device = 'Unknown';
    
    // Browser detection
    if (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Edge';
    } elseif (preg_match('/MSIE|Trident/i', $user_agent)) {
        $browser = 'Internet Explorer';
    }
    
    // OS detection
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Mac/i', $user_agent)) {
        $os = 'macOS';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
    } elseif (preg_match('/iOS/i', $user_agent)) {
        $os = 'iOS';
    }
    
    // Device detection
    if (preg_match('/Mobile|Android|iPhone|iPad/i', $user_agent)) {
        $device = 'Mobile';
    } elseif (preg_match('/Tablet|iPad/i', $user_agent)) {
        $device = 'Tablet';
    } else {
        $device = 'Desktop';
    }
    
    return array(
        'browser' => $browser,
        'os' => $os,
        'device' => $device
    );
}

/**
 * Get location and ISP from IP
 */
function get_location_and_isp($ip) {
    if (empty($ip) || $ip === '0.0.0.0') {
        return array(
            'country' => 'Unknown',
            'city' => 'Unknown',
            'isp' => 'Unknown'
        );
    }
    
    $cache_key = 'alhadiya_ip_location_' . md5($ip);
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    $url = "http://ip-api.com/json/{$ip}";
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        return array(
            'country' => 'Unknown',
            'city' => 'Unknown',
            'isp' => 'Unknown'
        );
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if ($data && isset($data['status']) && $data['status'] === 'success') {
        $location_data = array(
            'country' => $data['country'] ?? 'Unknown',
            'city' => $data['city'] ?? 'Unknown',
            'isp' => $data['isp'] ?? 'Unknown'
        );
        
        set_transient($cache_key, $location_data, HOUR_IN_SECONDS);
        return $location_data;
    }
    
    return array(
        'country' => 'Unknown',
        'city' => 'Unknown',
        'isp' => 'Unknown'
    );
}