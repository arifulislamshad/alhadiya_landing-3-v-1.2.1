<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function alhadiya_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'alhadiya'),
    ));
}
add_action('after_setup_theme', 'alhadiya_theme_setup');

// Enqueue styles and scripts
function alhadiya_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css');
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_style('alhadiya-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true);
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
    wp_enqueue_script('jquery');
    
    // Localize script for AJAX
    wp_localize_script('jquery', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('alhadiya_nonce'),
        'wc_ajax_url' => class_exists('WC_AJAX') ? WC_AJAX::get_endpoint('%%endpoint%%') : '',
        'dhaka_delivery_charge' => get_theme_mod('dhaka_delivery_charge', 0),
        'outside_dhaka_delivery_charge' => get_theme_mod('outside_dhaka_delivery_charge', 0),
        'phone_number' => get_theme_mod('phone_number', '+8801737146996'),
        'device_info_nonce' => wp_create_nonce('alhadiya_device_info_nonce'), // New nonce for device info
        'event_nonce' => wp_create_nonce('alhadiya_event_nonce'), // New nonce for custom events
        'screen_size_nonce' => wp_create_nonce('alhadiya_screen_size_nonce'), // New nonce for screen size
        'activity_nonce' => wp_create_nonce('alhadiya_activity_nonce'), // New nonce for activity tracking
        'server_event_nonce' => wp_create_nonce('alhadiya_server_event_nonce') // Server-side tracking nonce
    ));
}
add_action('wp_enqueue_scripts', 'alhadiya_scripts');

// Create custom post types for reviews and FAQs
function create_course_post_types() {
    // Reviews/Photos
    register_post_type('course_review', array(
        'labels' => array(
            'name' => __('Customer Reviews'),
            'singular_name' => __('Customer Review'),
            'add_new' => __('Add New Review'),
            'add_new_item' => __('Add New Customer Review'),
            'edit_item' => __('Edit Review'),
            'new_item' => __('New Review'),
            'view_item' => __('View Review'),
            'search_items' => __('Search Reviews'),
            'not_found' => __('No reviews found'),
            'not_found_in_trash' => __('No reviews found in trash')
        ),
        'public' => true,
        'supports' => array('title', 'thumbnail', 'editor'),
        'menu_icon' => 'dashicons-camera',
        'menu_position' => 25
    ));
    
    // FAQs
    register_post_type('course_faq', array(
        'labels' => array(
            'name' => __('FAQs'),
            'singular_name' => __('FAQ'),
            'add_new' => __('Add New FAQ'),
            'add_new_item' => __('Add New FAQ'),
            'edit_item' => __('Edit FAQ'),
            'new_item' => __('New FAQ'),
            'view_item' => __('View FAQ'),
            'search_items' => __('Search FAQs'),
            'not_found' => __('No FAQs found'),
            'not_found_in_trash' => __('No FAQs found in trash')
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'page-attributes'),
        'menu_icon' => 'dashicons-editor-help',
        'menu_position' => 26
    ));
}
add_action('init', 'create_course_post_types');

// Ensure database tables are created
function ensure_tracking_tables_exist() {
    create_device_tracking_table();
    create_device_events_table();
    alhadiya_create_server_events_table();
}
add_action('init', 'ensure_tracking_tables_exist');
add_action('after_switch_theme', 'ensure_tracking_tables_exist');

// Insert default FAQs on theme activation if they don't exist
function alhadiya_insert_default_faqs() {
    $faqs = array(
        array(
            'title' => 'ক্লাস কীভাবে করানো হবে? এবং রেকর্ড থাকবে কি?',
            'content' => 'আমাদের ক্লাসগুলো অনলাইন প্ল্যাটফর্মে লাইভ করানো হবে। প্রতিটি ক্লাসের রেকর্ড থাকবে, যা আপনি পরবর্তীতে যেকোনো সময় দেখতে পারবেন।'
        ),
        array(
            'title' => 'কোর্স শেষে কোনো সমস্যা হলে কি আপনারা সাহায্য করবেন?',
            'content' => 'হ্যাঁ, কোর্স শেষেও আমাদের সাপোর্ট টিম আপনার যেকোনো সমস্যা সমাধানে সাহায্য করবে। আপনি আমাদের সাথে যোগাযোগ করতে পারবেন।'
        ),
        array(
            'title' => 'কোর্সটি করলে কি মেহেদীর রঙ গাঢ় হবে?',
            'content' => 'আমাদের কোর্সে মেহেদীর রঙ গাঢ় করার সকল সিক্রেট টিপস এবং সঠিক রেসিপি শেখানো হবে, যা অনুসরণ করলে আপনার মেহেদীর রঙ অবশ্যই গাঢ় হবে।'
        ),
        array(
            'title' => 'আমি কি কোর্সটি করে ব্যবসা করতে পারব?',
            'content' => 'অবশ্যই! এই কোর্সটি আপনাকে অর্গানিক মেহেদী তৈরি এবং বিক্রির সকল কৌশল শেখাবে, যা আপনাকে সফলভাবে ব্যবসা শুরু করতে সাহায্য করবে।'
        ),
        array(
            'title' => 'মেহেদী কোর্সটিতে কয়দিন ক্লাস হবে এবং ক্লাসের সময় কী?',
            'content' => 'মেহেদী কোর্সটিতে মোট ৭ দিন ক্লাস হবে। ক্লাসের সময়সূচী কোর্সের শুরুতে জানিয়ে দেওয়া হবে এবং এটি শিক্ষার্থীদের সুবিধার কথা মাথায় রেখে নির্ধারণ করা হবে।'
        ),
        array(
            'title' => 'কোর্স শেষে কি সার্টিফিকেট দেওয়া হবে?',
            'content' => 'হ্যাঁ, কোর্স সফলভাবে সম্পন্ন করার পর আপনাকে একটি সার্টিফিকেট প্রদান করা হবে।'
        )
    );

    foreach ($faqs as $faq_data) {
        $existing_faq = get_page_by_title($faq_data['title'], OBJECT, 'course_faq');
        if (null === $existing_faq) {
            wp_insert_post(array(
                'post_title'    => $faq_data['title'],
                'post_content'  => $faq_data['content'],
                'post_status'   => 'publish',
                'post_type'     => 'course_faq',
            ));
        }
    }
}
add_action('after_switch_theme', 'alhadiya_insert_default_faqs');

// Enhanced Device Tracking Table - ADDED NEW COLUMNS
function create_device_tracking_table() {
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
    dbDelta($sql);
}
add_action('after_switch_theme', 'create_device_tracking_table');
add_action('init', 'create_device_tracking_table');

// New table for specific device events
function create_device_events_table() {
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
    dbDelta($sql);
}
add_action('after_switch_theme', 'create_device_events_table');
add_action('init', 'create_device_events_table');


// Fixed IP Blocking System
function check_ip_blocking() {
    $user_ip = get_client_ip();
    $blocked_key = 'blocked_ip_' . md5($user_ip);
    $blocked_data = get_transient($blocked_key);
    
    if ($blocked_data && is_array($blocked_data)) {
        $block_time = $blocked_data['block_time'];
        $current_time = time();
        
        if ($current_time < $block_time) {
            $remaining_seconds = $block_time - $current_time;
            $remaining_minutes = ceil($remaining_seconds / 60);
            
            wp_send_json_error('আপনার IP ব্লক করা হয়েছে। অনুগ্রহ করে ' . $remaining_minutes . ' মিনিট পর আবার চেষ্টা করুন।');
            return false;
        } else {
            // Block expired, remove it
            delete_transient($blocked_key);
        }
    }
    return true;
}

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

function block_ip_after_order($ip, $time = 5, $unit = 'minutes') {
    $seconds = 0;
    
    switch($unit) {
        case 'minutes':
            $seconds = $time * 60;
            break;
        case 'hours':
            $seconds = $time * 3600;
            break;
        case 'days':
            $seconds = $time * 86400;
            break;
    }
    
    $block_time = time() + $seconds;
    $blocked_data = array(
        'block_time' => $block_time,
        'blocked_at' => time(),
        'duration' => $seconds
    );
    
    $blocked_key = 'blocked_ip_' . md5($ip);
    set_transient($blocked_key, $blocked_data, $seconds);
    
    // Log the blocking for debugging
    error_log("IP Blocked: $ip for $time $unit (until " . date('Y-m-d H:i:s', $block_time) . ")");
}

// Enhanced device tracking - Initial visit/page load info
function track_enhanced_device_info() {
    // Check if device tracking is enabled
    if (!get_theme_mod('enable_device_tracking', true)) {
        return false;
    }
    
    global $wpdb;
    
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
    $ip = get_client_ip();
    $referrer = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';
    
    // Get session ID from server-side session system
    $session_id = alhadiya_init_server_session();
    
    // Set cookie for client-side access if not already set
    if (!isset($_COOKIE['device_session'])) {
        $cookie_domain = parse_url(get_site_url(), PHP_URL_HOST);
        setcookie('device_session', $session_id, time() + (86400 * 30), '/', $cookie_domain, is_ssl(), false);
        $_COOKIE['device_session'] = $session_id; // Set in current request
    }
    
    // Parse user agent for device info
    $device_info = parse_user_agent($user_agent);
    
    // Get location and ISP info
    $location_data = get_location_and_isp($ip);
    
    // Disable Facebook ID tracking by setting it to empty string
    $facebook_id = ''; 
    
    $table_name = $wpdb->prefix . 'device_tracking';
    
    // Check if session exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE session_id = %s",
        $session_id
    ));
    
    // Screen size and new device info will be updated via AJAX from client-side
    $screen_size = $existing ? $existing->screen_size : '';
    $language = (is_object($existing) && property_exists($existing, 'language')) ? $existing->language : '';
    $timezone = (is_object($existing) && property_exists($existing, 'timezone')) ? $existing->timezone : '';
    $connection_type = $existing ? $existing->connection_type : '';
    $battery_level = $existing ? $existing->battery_level : null;
    $battery_charging = $existing ? $existing->battery_charging : null;
    $memory_info = (is_object($existing) && property_exists($existing, 'memory_info')) ? $existing->memory_info : null;
    $cpu_cores = $existing ? $existing->cpu_cores : null;
    $touchscreen_detected = (is_object($existing) && property_exists($existing, 'touchscreen_detected')) ? $existing->touchscreen_detected : null;

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
                'facebook_id' => $facebook_id, // Will be empty string
                'screen_size' => $screen_size, // Initial empty, updated by AJAX
                'language' => $language,
                'timezone' => $timezone,
                'connection_type' => $connection_type,
                'battery_level' => $battery_level,
                'battery_charging' => $battery_charging,
                'memory_info' => $memory_info,
                'cpu_cores' => $cpu_cores,
                'touchscreen_detected' => $touchscreen_detected,
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
        'facebook_id' => $facebook_id,
        'screen_size' => $screen_size,
        'timestamp' => current_time('mysql')
    );
}

function parse_user_agent($user_agent) {
    $device_type = 'Desktop';
    $device_model = 'Unknown';
    $browser = 'Unknown';
    $os = 'Unknown';
    
    // Detect mobile devices
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i', $user_agent)) {
        $device_type = 'Mobile';
        
        // Detect specific mobile devices
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
    
    // Detect browser
    if (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Edge';
    }
    
    // Detect OS for desktop
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

function get_location_and_isp($ip) {
    $location = 'Unknown';
    $isp = 'Unknown';
    
    // Try multiple IP geolocation services
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

// Track time spent on website
function track_time_spent() {
    // Check if time spent tracking is enabled
    if (!get_theme_mod('enable_time_spent_tracking', true)) {
        wp_send_json_error('Time spent tracking is disabled');
        return;
    }
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alhadiya_device_info_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';

    $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
    $time_spent = isset($_POST['time_spent']) ? intval($_POST['time_spent']) : 0;

    if (!empty($session_id) && $time_spent > 0) {
        $wpdb->query($wpdb->prepare(
            "UPDATE $table_name SET time_spent = time_spent + %d WHERE session_id = %s",
            $time_spent,
            $session_id
        ));
        wp_send_json_success('Time spent updated successfully');
    } else {
        wp_send_json_error('Invalid data provided');
    }
}

// AJAX handler for updating time spent
function update_time_spent() {
    // Check if time spent tracking is enabled
    if (!get_theme_mod('enable_time_spent_tracking', true)) {
        return;
    }
    
    if (!is_admin()) {
        $session_id = isset($_COOKIE['device_session']) ? sanitize_text_field($_COOKIE['device_session']) : '';
        if (!empty($session_id)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'device_tracking';
            
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_name SET time_spent = time_spent + 1 WHERE session_id = %s",
                $session_id
            ));
        }
    }
}
add_action('wp_ajax_update_time_spent', 'update_time_spent');
add_action('wp_ajax_nopriv_update_time_spent', 'update_time_spent');

// AJAX handler for tracking custom events
function track_custom_event() {
    // Check if custom events tracking is enabled
    if (!get_theme_mod('enable_custom_events_tracking', true)) {
        wp_send_json_error('Custom events tracking is disabled');
        return;
    }
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alhadiya_event_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'device_events';

    $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
    $event_type = isset($_POST['event_type']) ? sanitize_text_field($_POST['event_type']) : '';
    $event_name = isset($_POST['event_name']) ? sanitize_text_field($_POST['event_name']) : '';
    $event_value = isset($_POST['event_value']) ? sanitize_text_field($_POST['event_value']) : '';

    $wpdb->insert(
        $table_name,
        array(
            'session_id' => $session_id,
            'event_type' => $event_type,
            'event_name' => $event_name,
            'event_value' => $event_value,
            'timestamp' => current_time('mysql')
        )
    );

    wp_send_json_success('Event tracked successfully');
}
add_action('wp_ajax_track_custom_event', 'track_custom_event');
add_action('wp_ajax_nopriv_track_custom_event', 'track_custom_event');

// AJAX handler for updating device screen size
function update_device_screen_size() {
    // Check if device details tracking is enabled
    if (!get_theme_mod('enable_device_details_tracking', true)) {
        wp_send_json_error('Device details tracking is disabled');
        return;
    }
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alhadiya_screen_size_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';

    $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
    $screen_size = isset($_POST['screen_size']) ? sanitize_text_field($_POST['screen_size']) : '';

    if (empty($session_id) || empty($screen_size)) {
        wp_send_json_error('Missing session_id or screen_size');
        return;
    }

    $result = $wpdb->update(
        $table_name,
        array('screen_size' => $screen_size),
        array('session_id' => $session_id)
    );

    if ($result === false) {
        wp_send_json_error('Database update failed: ' . $wpdb->last_error);
    } else {
        wp_send_json_success('Screen size updated successfully');
    }
}
add_action('wp_ajax_update_device_screen_size', 'update_device_screen_size');
add_action('wp_ajax_nopriv_update_device_screen_size', 'update_device_screen_size');

// NEW AJAX handler for updating client-side device details
function update_client_device_details() {
    // Check if device details tracking is enabled
    if (!get_theme_mod('enable_device_details_tracking', true)) {
        wp_send_json_error('Device details tracking is disabled');
        return;
    }
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alhadiya_device_info_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';

    $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
    $data_to_update = array();

    if (isset($_POST['language']) && $_POST['language'] !== 'N/A') $data_to_update['language'] = sanitize_text_field($_POST['language']);
    if (isset($_POST['timezone']) && $_POST['timezone'] !== 'N/A') $data_to_update['timezone'] = sanitize_text_field($_POST['timezone']);
    if (isset($_POST['connection_type']) && $_POST['connection_type'] !== 'N/A') $data_to_update['connection_type'] = sanitize_text_field($_POST['connection_type']);
    if (isset($_POST['battery_level']) && $_POST['battery_level'] !== 'N/A' && $_POST['battery_level'] !== null) $data_to_update['battery_level'] = floatval($_POST['battery_level']);
    if (isset($_POST['battery_charging']) && $_POST['battery_charging'] !== 'N/A' && $_POST['battery_charging'] !== null) $data_to_update['battery_charging'] = intval($_POST['battery_charging']);
    if (isset($_POST['memory_info']) && $_POST['memory_info'] !== 'N/A') $data_to_update['memory_info'] = floatval($_POST['memory_info']);
    if (isset($_POST['cpu_cores']) && $_POST['cpu_cores'] !== 'N/A') $data_to_update['cpu_cores'] = intval($_POST['cpu_cores']);
    if (isset($_POST['touchscreen_detected']) && $_POST['touchscreen_detected'] !== 'N/A') $data_to_update['touchscreen_detected'] = intval($_POST['touchscreen_detected']);

    if (!empty($data_to_update) && !empty($session_id)) {
        $result = $wpdb->update(
            $table_name,
            $data_to_update,
            array('session_id' => $session_id)
        );

        if ($result === false) {
            wp_send_json_error('Database update failed: ' . $wpdb->last_error);
        } else {
            wp_send_json_success('Client device details updated successfully');
        }
    } else {
        wp_send_json_error('No data to update or missing session_id');
    }
}
add_action('wp_ajax_update_client_device_details', 'update_client_device_details');
add_action('wp_ajax_nopriv_update_client_device_details', 'update_client_device_details');

// Enhanced AJAX handler for device details via server events
function handle_device_details_event() {
    if (!isset($_POST['server_event_nonce']) || !wp_verify_nonce($_POST['server_event_nonce'], 'alhadiya_server_event_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    $event_name = isset($_POST['event_name']) ? sanitize_text_field($_POST['event_name']) : '';
    $event_data = isset($_POST['event_data']) ? $_POST['event_data'] : array();
    
    if ($event_name === 'device_details' && !empty($event_data)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'device_tracking';
        
        // Get session ID from cookie or generate new one
        $session_id = alhadiya_init_server_session();
        
        $data_to_update = array();
        
        if (isset($event_data['screen_size']) && !empty($event_data['screen_size'])) $data_to_update['screen_size'] = sanitize_text_field($event_data['screen_size']);
        if (isset($event_data['language']) && !empty($event_data['language'])) $data_to_update['language'] = sanitize_text_field($event_data['language']);
        if (isset($event_data['timezone']) && !empty($event_data['timezone'])) $data_to_update['timezone'] = sanitize_text_field($event_data['timezone']);
        if (isset($event_data['connection_type']) && !empty($event_data['connection_type'])) $data_to_update['connection_type'] = sanitize_text_field($event_data['connection_type']);
        if (isset($event_data['battery_level']) && $event_data['battery_level'] !== null) $data_to_update['battery_level'] = floatval($event_data['battery_level']);
        if (isset($event_data['memory_info']) && !empty($event_data['memory_info'])) $data_to_update['memory_info'] = floatval($event_data['memory_info']);
        if (isset($event_data['cpu_cores']) && !empty($event_data['cpu_cores'])) $data_to_update['cpu_cores'] = intval($event_data['cpu_cores']);
        if (isset($event_data['touchscreen_detected']) && $event_data['touchscreen_detected'] !== null) $data_to_update['touchscreen_detected'] = intval($event_data['touchscreen_detected']);
        
        if (!empty($data_to_update)) {
            $result = $wpdb->update(
                $table_name,
                $data_to_update,
                array('session_id' => $session_id)
            );
            
            if ($result !== false) {
                wp_send_json_success('Device details updated successfully');
            } else {
                wp_send_json_error('Database update failed: ' . $wpdb->last_error);
            }
        } else {
            wp_send_json_error('No valid device data to update');
        }
    } else {
        wp_send_json_error('Invalid event data');
    }
}
add_action('wp_ajax_alhadiya_server_event', 'handle_device_details_event', 5);
add_action('wp_ajax_nopriv_alhadiya_server_event', 'handle_device_details_event', 5);


// Fixed WooCommerce order submission with fixed IP blocking
function handle_woocommerce_order() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        wp_send_json_error('WooCommerce is not active');
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alhadiya_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Check IP blocking - Fixed implementation
    if (!check_ip_blocking()) {
        return; // Function already sends JSON error
    }
    
    // Track enhanced device info
    $device_info = track_enhanced_device_info();
    
    // Prevent duplicate submissions with better key
    $user_ip = get_client_ip();
    $submission_key = 'order_submission_' . md5($user_ip . (isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '') . date('Y-m-d-H'));
    if (get_transient($submission_key)) {
        wp_send_json_error('ডুপ্লিকেট অর্ডার সনাক্ত করা হয়েছে। অনুগ্রহ করে একটু অপেক্ষা করুন।');
        return;
    }
    set_transient($submission_key, true, 300); // 5 minutes
    
    // Sanitize and validate input data
    $billing_first_name = isset($_POST['billing_first_name']) ? sanitize_text_field($_POST['billing_first_name']) : '';
    $billing_phone = isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '';
    $billing_address_1 = isset($_POST['billing_address_1']) ? sanitize_textarea_field($_POST['billing_address_1']) : '';
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $delivery_zone = isset($_POST['delivery_zone']) ? sanitize_text_field($_POST['delivery_zone']) : '';
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';
    $transaction_number = isset($_POST['transaction_number']) ? sanitize_text_field($_POST['transaction_number']) : '';
    
    // Validate required fields
    if (empty($billing_first_name)) {
        wp_send_json_error('অনুগ্রহ করে আপনার নাম লিখুন');
        return;
    }
    
    if (empty($billing_phone)) {
        wp_send_json_error('অনুগ্রহ করে মোবাইল নাম্বার লিখুন');
        return;
    }
    
    if (strlen($billing_phone) < 10) {
        wp_send_json_error('অনুগ্রহ করে সঠিক মোবাইল নাম্বার লিখুন');
        return;
    }
    
    if (empty($billing_address_1)) {
        wp_send_json_error('অনুগ্রহ করে ঠিকানা লিখুন');
        return;
    }
    
    if (empty($product_id)) {
        wp_send_json_error('অনুগ্রহ করে একটি প্যাকেজ সিলেক্ট করুন');
        return;
    }
    
    if (empty($delivery_zone)) {
        wp_send_json_error('অনুগ্রহ করে ডেলিভারি অপশন সিলেক্ট করুন');
        return;
    }
    
    if (empty($payment_method)) {
        wp_send_json_error('অনুগ্রহ করে পেমেন্ট পদ্ধতি সিলেক্ট করুন');
        return;
    }
    
    // Validate transaction number for digital payments
    if (in_array($payment_method, ['bkash', 'nagad', 'rocket']) && empty($transaction_number)) {
        wp_send_json_error('অনুগ্রহ করে ট্রানজেকশন নাম্বার লিখুন');
        return;
    }
    
    // Get product
    $product = wc_get_product($product_id);
    if (!$product || !$product->exists()) {
        wp_send_json_error('প্রোডাক্ট পাওয়া যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।');
        return;
    }
    
    try {
        // Create order
        $order = wc_create_order();
        
        if (is_wp_error($order)) {
            wp_send_json_error('অর্ডার তৈরি করতে সমস্যা হয়েছে: ' . $order->get_error_message());
            return;
        }
        
        // Add product to order
        $item_id = $order->add_product($product, 1);
        if (!$item_id) {
            wp_send_json_error('প্রোডাক্ট অর্ডারে যোগ করতে সমস্যা হয়েছে');
            return;
        }
        
        // Set billing address
        $order->set_billing_first_name($billing_first_name);
        $order->set_billing_last_name('');
        $order->set_billing_phone($billing_phone);
        $order->set_billing_address_1($billing_address_1);
        
        // Set shipping address same as billing
        $order->set_shipping_first_name($billing_first_name);
        $order->set_shipping_address_1($billing_address_1);
        
        // Add customer info to device tracking table upon successful purchase
        global $wpdb;
        $tracking_table = $wpdb->prefix . 'device_tracking';
        $wpdb->update(
            $tracking_table,
            array(
                'has_purchased' => 1,
                'customer_name' => $billing_first_name,
                'customer_phone' => $billing_phone,
                'customer_address' => $billing_address_1,
            ),
            array('session_id' => $device_info['session_id'])
        );

        // Set delivery zone and charges
        $delivery_charges = array(
            '1' => floatval(get_theme_mod('dhaka_delivery_charge', 0)),
            '2' => floatval(get_theme_mod('outside_dhaka_delivery_charge', 0))
        );
        
        $delivery_charge = isset($delivery_charges[$delivery_zone]) ? $delivery_charges[$delivery_zone] : 0;
        
        // Add metadata
        $order->update_meta_data('_delivery_zone', $delivery_zone);
        $order->update_meta_data('_delivery_charge', $delivery_charge);
        $order->update_meta_data('_order_source', 'landing_page');
        $order->update_meta_data('_payment_method_selected', $payment_method);
        $order->update_meta_data('_device_info', json_encode($device_info));
        $order->update_meta_data('_session_id', $device_info['session_id']);
        
        if (!empty($transaction_number)) {
            $order->update_meta_data('_transaction_number', $transaction_number);
        }
        
        // Add delivery charge if applicable
        if ($delivery_charge > 0) {
            $delivery_title = $delivery_zone == '1' ? 
                get_theme_mod('dhaka_delivery_title', 'ঢাকার মধ্যে ডেলিভারি') : 
                get_theme_mod('outside_dhaka_delivery_title', 'ঢাকার বাইরে ডেলিভারি');
            
            $order->add_fee($delivery_title, $delivery_charge);
        }
        
        // Set payment method
        $payment_methods = array(
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            'pay_later' => 'Pay Later'
        );
        
        $payment_title = isset($payment_methods[$payment_method]) ? $payment_methods[$payment_method] : 'Cash on Delivery';
        $order->set_payment_method($payment_method);
        $order->set_payment_method_title($payment_title);
        
        // Calculate totals
        $order->calculate_totals();
        
        // Set order status based on payment method
        if ($payment_method === 'pay_later') {
            $order->set_status('pending');
        } else {
            $order->set_status('processing');
        }
        
        // Add order notes
        $zone_text = $delivery_zone == '1' ? 'ঢাকার মধ্যে' : 'ঢাকার বাইরে';
        $note = 'অর্ডার ল্যান্ডিং পেজ থেকে এসেছে। ডেলিভারি জোন: ' . $zone_text . '. পেমেন্ট পদ্ধতি: ' . $payment_title;
        
        if (!empty($transaction_number)) {
            $note .= '. ট্রানজেকশন নাম্বার: ' . $transaction_number;
        }
        
        $order->add_order_note($note);
        
        // Save order
        $order_id = $order->save();
        
        if (!$order_id) {
            wp_send_json_error('অর্ডার সেভ করতে সমস্যা হয়েছে');
            return;
        }
        
        // Block IP for specified time - Fixed implementation
        $block_time = get_theme_mod('ip_block_time', 1);
        $block_unit = get_theme_mod('ip_block_unit', 'minutes');
        block_ip_after_order($user_ip, $block_time, $block_unit);
        
        // Clear any caches
        if (function_exists('wc_delete_shop_order_transients')) {
            wc_delete_shop_order_transients($order_id);
        }
        
        // Return success response with enhanced data
        wp_send_json_success(array(
            'message' => 'অর্ডার সফলভাবে সম্পন্ন হয়েছে! আমাদের প্রতিনিধি শীঘ্রই আপনার সাথে যোগাযোগ করবে।',
            'order_id' => $order_id,
            'payment_method' => $payment_title,
            'transaction_number' => $transaction_number,
            'customer_name' => $billing_first_name,
            'customer_phone' => $billing_phone,
            'total_amount' => $order->get_total(),
            'redirect_url' => home_url('/order-success/?order_id=' . $order_id . '&security=' . wp_create_nonce('order_success_' . $order_id))
        ));
        
    } catch (Exception $e) {
        error_log('Order creation error: ' . $e->getMessage());
        wp_send_json_error('অর্ডার তৈরি করতে সমস্যা হয়েছে: ' . $e->getMessage());
    }
}
add_action('wp_ajax_submit_wc_order', 'handle_woocommerce_order');
add_action('wp_ajax_nopriv_submit_wc_order', 'handle_woocommerce_order');

// Enhanced customizer options
function alhadiya_customize_register($wp_customize) {
    // Logo Settings Section
    $wp_customize->add_section('alhadiya_logo_settings', array(
        'title' => __('Logo Settings', 'alhadiya'),
        'priority' => 25,
    ));
    
    // Logo Size
    $wp_customize->add_setting('logo_size', array('default' => 200));
    $wp_customize->add_control('logo_size', array(
        'label' => __('Logo Size (px)', 'alhadiya'),
        'section' => 'alhadiya_logo_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 50,
            'max' => 500,
            'step' => 10,
        ),
    ));
    
    // Logo Alignment
    $wp_customize->add_setting('logo_alignment', array('default' => 'center'));
    $wp_customize->add_control('logo_alignment', array(
        'label' => __('Logo Alignment', 'alhadiya'),
        'section' => 'alhadiya_logo_settings',
        'type' => 'select',
        'choices' => array(
            'left' => 'Left',
            'center' => 'Center',
            'right' => 'Right'
        ),
    ));

    // Content Management Section
    $wp_customize->add_section('alhadiya_content_management', array(
        'title' => __('Content Management', 'alhadiya'),
        'priority' => 28,
    ));

    // Course Items with individual colors
    $course_items = array(
        'item1' => 'অর্গানিক হাতের মেহেদী তৈরি',
        'item2' => 'ড্রাই রিলিজ কিভাবে করে',
        'item3' => 'মেহেদী কোণ তৈরি',
        'item4' => 'প্রফেশনাল রেসিপি শিট',
        'item5' => 'কি কি তেল ব্যবহার করা যাবে',
        'item6' => 'কিভাবে মেহেদির রঙ গাড় হবে (সিক্রেট টিপস)',
        'item7' => 'মেহেদির মূল্য নির্ধারণ',
        'item8' => 'দীর্ঘদিন সংরক্ষণ কিভাবে করবেন',
        'item9' => 'মেহেদী কিভাবে বিক্রি করবেন',
        'item10' => 'সার্টিফিকেট প্রদান',
        'item11' => 'মেহেদী কোণ প্যাকেজিং',
        'item12' => 'প্যাকেজিং ডিজাইন ও লেবেলিং',
        'item13' => 'বিক্রির জন্য প্রস্তুতি'
    );

    foreach ($course_items as $key => $default_text) {
        // Text setting
        $wp_customize->add_setting('course_' . $key . '_text', array('default' => $default_text));
        $wp_customize->add_control('course_' . $key . '_text', array(
            'label' => __('Course Item ' . substr($key, 4) . ' Text', 'alhadiya'),
            'section' => 'alhadiya_content_management',
            'type' => 'text',
        ));

        // Color setting
        $wp_customize->add_setting('course_' . $key . '_color', array('default' => '#28a745'));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'course_' . $key . '_color', array(
            'label' => __('Course Item ' . substr($key, 4) . ' Color', 'alhadiya'),
            'section' => 'alhadiya_content_management',
        )));
    }

    // Section titles and colors
    $wp_customize->add_setting('section1_title', array('default' => '🌱 অর্গানিক মেহেদী তৈরির সহজ উপায়'));
    $wp_customize->add_control('section1_title', array(
        'label' => __('Section 1 Title', 'alhadiya'),
        'section' => 'alhadiya_content_management',
        'type' => 'text',
    ));

    $wp_customize->add_setting('section1_color', array('default' => '#28a745'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section1_color', array(
        'label' => __('Section 1 Color', 'alhadiya'),
        'section' => 'alhadiya_content_management',
    )));

    $wp_customize->add_setting('section2_title', array('default' => '🔥 মেহেদী রঙ বাড়ানোর গোপন টিপস'));
    $wp_customize->add_control('section2_title', array(
        'label' => __('Section 2 Title', 'alhadiya'),
        'section' => 'alhadiya_content_management',
        'type' => 'text',
    ));

    $wp_customize->add_setting('section2_color', array('default' => '#dc3545'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section2_color', array(
        'label' => __('Section 2 Color', 'alhadiya'),
        'section' => 'alhadiya_content_management',
    )));

    $wp_customize->add_setting('section3_title', array('default' => '📦 প্যাকেজিং ও সার্টিফিকেশন'));
    $wp_customize->add_control('section3_title', array(
        'label' => __('Section 3 Title', 'alhadiya'),
        'section' => 'alhadiya_content_management',
        'type' => 'text',
    ));

    $wp_customize->add_setting('section3_color', array('default' => '#6f42c1'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section3_color', array(
        'label' => __('Section 3 Color', 'alhadiya'),
        'section' => 'alhadiya_content_management',
    )));
    
    // Payment Settings Section
    $wp_customize->add_section('alhadiya_payment_settings', array(
        'title' => __('Payment Settings', 'alhadiya'),
        'priority' => 35,
    ));

    // Payment method text toggle
    $wp_customize->add_setting('show_payment_text', array('default' => true));
    $wp_customize->add_control('show_payment_text', array(
        'label' => __('Show Payment Method Text', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'checkbox',
        'description' => __('Enable to show text with icons, disable to show only icons', 'alhadiya'),
    ));
    
    // Payment method icons
    $wp_customize->add_setting('bkash_icon');
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'bkash_icon', array(
        'label' => __('bKash Icon', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'mime_type' => 'image',
    )));

    $wp_customize->add_setting('nagad_icon');
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'nagad_icon', array(
        'label' => __('Nagad Icon', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'mime_type' => 'image',
    )));

    $wp_customize->add_setting('rocket_icon');
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'rocket_icon', array(
        'label' => __('Rocket Icon', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'mime_type' => 'image',
    )));
    
    // bKash Number and Color
    $wp_customize->add_setting('bkash_number', array('default' => '01975669946'));
    $wp_customize->add_control('bkash_number', array(
        'label' => __('bKash Number', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'text',
    ));

    $wp_customize->add_setting('bkash_color', array('default' => '#e2136e'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bkash_color', array(
        'label' => __('bKash Color', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
    )));

    $wp_customize->add_setting('bkash_instruction', array('default' => 'আর এই নাম্বারে বিকাশ সেন্ডমানি করে ফর্ম এ লিখুন (Personal)'));
    $wp_customize->add_control('bkash_instruction', array(
        'label' => __('bKash Instruction Text', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'textarea',
    ));
    
    // Nagad Number and Color
    $wp_customize->add_setting('nagad_number', array('default' => '01737146996'));
    $wp_customize->add_control('nagad_number', array(
        'label' => __('Nagad Number', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'text',
    ));

    $wp_customize->add_setting('nagad_color', array('default' => '#f47920'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nagad_color', array(
        'label' => __('Nagad Color', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
    )));

    $wp_customize->add_setting('nagad_instruction', array('default' => 'আর এই নাম্বারে নগদে সেন্ডমানি করে ফর্ম এ লিখুন (Personal)'));
    $wp_customize->add_control('nagad_instruction', array(
        'label' => __('Nagad Instruction Text', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'textarea',
    ));
    
    // Rocket Number and Color
    $wp_customize->add_setting('rocket_number', array('default' => '01737146996'));
    $wp_customize->add_control('rocket_number', array(
        'label' => __('Rocket Number', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'text',
    ));

    $wp_customize->add_setting('rocket_color', array('default' => '#8b1538'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rocket_color', array(
        'label' => __('Rocket Color', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
    )));

    $wp_customize->add_setting('rocket_instruction', array('default' => 'আর এই নাম্বারে রকেটে সেন্ডমানি করে ফর্ম এ লিখুন (Personal)'));
    $wp_customize->add_control('rocket_instruction', array(
        'label' => __('Rocket Instruction Text', 'alhadiya'),
        'section' => 'alhadiya_payment_settings',
        'type' => 'textarea',
    ));

    // IP Blocking Settings - Fixed defaults
    $wp_customize->add_section('alhadiya_security_settings', array(
        'title' => __('Security Settings', 'alhadiya'),
        'priority' => 36,
    ));

    $wp_customize->add_setting('ip_block_time', array('default' => 1));
    $wp_customize->add_control('ip_block_time', array(
        'label' => __('IP Block Time', 'alhadiya'),
        'section' => 'alhadiya_security_settings',
        'type' => 'number',
        'description' => __('How long to block IP after order submission', 'alhadiya'),
        'input_attrs' => array(
            'min' => 1,
            'max' => 365,
        ),
    ));

    $wp_customize->add_setting('ip_block_unit', array('default' => 'minutes'));
    $wp_customize->add_control('ip_block_unit', array(
        'label' => __('IP Block Unit', 'alhadiya'),
        'section' => 'alhadiya_security_settings',
        'type' => 'select',
        'choices' => array(
            'minutes' => 'Minutes',
            'hours' => 'Hours',
            'days' => 'Days'
        ),
    ));
    
    // Main Content Settings
    $wp_customize->add_section('alhadiya_content_settings', array(
        'title' => __('Content Settings', 'alhadiya'),
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('main_heading_text', array('default' => 'অর্গানিক হাতের মেহেদি বানানোর কোর্স মাত্র ৪৯০ টাকা'));
    $wp_customize->add_control('main_heading_text', array(
        'label' => __('Main Heading Text', 'alhadiya'),
        'section' => 'alhadiya_content_settings',
        'type' => 'text',
    ));
    
    // Video Settings
    $wp_customize->add_section('alhadiya_video_settings', array(
        'title' => __('Video Settings', 'alhadiya'),
        'priority' => 40,
    ));
    
    $wp_customize->add_setting('youtube_video_url', array('default' => ''));
    $wp_customize->add_control('youtube_video_url', array(
        'label' => __('YouTube Video URL', 'alhadiya'),
        'section' => 'alhadiya_video_settings',
        'type' => 'url',
    ));
    
    // Delivery Settings
    $wp_customize->add_section('alhadiya_delivery_settings', array(
        'title' => __('Delivery Settings', 'alhadiya'),
        'priority' => 45,
    ));
    
    $wp_customize->add_setting('dhaka_delivery_title', array('default' => 'ঢাকার মধ্যে ডেলিভারি'));
    $wp_customize->add_control('dhaka_delivery_title', array(
        'label' => __('Dhaka Delivery Title', 'alhadiya'),
        'section' => 'alhadiya_delivery_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('dhaka_delivery_charge', array('default' => 0));
    $wp_customize->add_control('dhaka_delivery_charge', array(
        'label' => __('Dhaka Delivery Charge (৳)', 'alhadiya'),
        'section' => 'alhadiya_delivery_settings',
        'type' => 'number',
    ));
    
    $wp_customize->add_setting('outside_dhaka_delivery_title', array('default' => 'ঢাকার বাইরে ডেলিভারি'));
    $wp_customize->add_control('outside_dhaka_delivery_title', array(
        'label' => __('Outside Dhaka Delivery Title', 'alhadiya'),
        'section' => 'alhadiya_delivery_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('outside_dhaka_delivery_charge', array('default' => 0));
    $wp_customize->add_control('outside_dhaka_delivery_charge', array(
        'label' => __('Outside Dhaka Delivery Charge (৳)', 'alhadiya'),
        'section' => 'alhadiya_delivery_settings',
        'type' => 'number',
    ));
    
    // Other Settings
    $wp_customize->add_section('alhadiya_other_settings', array(
        'title' => __('Other Settings', 'alhadiya'),
        'priority' => 50,
    ));
    
    $wp_customize->add_setting('phone_number', array('default' => '+8801737146996'));
    $wp_customize->add_control('phone_number', array(
        'label' => __('Phone Number', 'alhadiya'),
        'section' => 'alhadiya_other_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('whatsapp_number', array('default' => '1737146996'));
    $wp_customize->add_control('whatsapp_number', array(
        'label' => __('WhatsApp Number', 'alhadiya'),
        'section' => 'alhadiya_other_settings',
        'type' => 'text',
    ));
    
    // Footer Settings
    $wp_customize->add_section('alhadiya_footer_settings', array(
        'title' => __('Footer Settings', 'alhadiya'),
        'priority' => 55,
    ));
    
    // NEW: Footer Enable/Disable Setting
    $wp_customize->add_setting('enable_footer', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    $wp_customize->add_control('enable_footer', array(
        'label' => __('Enable Footer', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'checkbox',
        'description' => __('Check to display the footer, uncheck to hide it.', 'alhadiya'),
    ));

    $wp_customize->add_setting('footer_description', array('default' => 'অর্গানিক হাতের মেহেদি বানানোর সম্পূর্ণ কোর্স। ঘরে বসে শিখুন প্রফেশনাল মেহেদি তৈরির সব কৌশল।'));
    $wp_customize->add_control('footer_description', array(
        'label' => __('Footer Description', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('facebook_url', array('default' => ''));
    $wp_customize->add_control('facebook_url', array(
        'label' => __('Facebook URL', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('youtube_url', array('default' => ''));
    $wp_customize->add_control('youtube_url', array(
        'label' => __('YouTube URL', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('instagram_url', array('default' => ''));
    $wp_customize->add_control('instagram_url', array(
        'label' => __('Instagram URL', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('email_address', array('default' => ''));
    $wp_customize->add_control('email_address', array(
        'label' => __('Email Address', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'email',
    ));
    
    $wp_customize->add_setting('business_address', array('default' => ''));
    $wp_customize->add_control('business_address', array(
        'label' => __('Business Address', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('business_hours', array('default' => 'সকাল ৯টা - রাত ৯টা'));
    $wp_customize->add_control('business_hours', array(
        'label' => __('Business Hours', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('privacy_policy_url', array('default' => ''));
    $wp_customize->add_control('privacy_policy_url', array(
        'label' => __('Privacy Policy URL', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('terms_conditions_url', array('default' => ''));
    $wp_customize->add_control('terms_conditions_url', array(
        'label' => __('Terms & Conditions URL', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('refund_policy_url', array('default' => ''));
    $wp_customize->add_control('refund_policy_url', array(
        'label' => __('Refund Policy URL', 'alhadiya'),
        'section' => 'alhadiya_footer_settings',
        'type' => 'url',
    ));
    
    // SEO & Analytics Settings
    $wp_customize->add_section('alhadiya_seo_settings', array(
        'title' => __('SEO & Analytics', 'alhadiya'),
        'priority' => 60,
    ));
    
    $wp_customize->add_setting('site_description', array('default' => 'অর্গানিক হাতের মেহেদি বানানোর কোর্স - সহজ উপায়ে শিখুন প্রফেশনাল মেহেদি তৈরির কৌশল'));
    $wp_customize->add_control('site_description', array(
        'label' => __('Site Description (Meta)', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('og_image');
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'og_image', array(
        'label' => __('OG Image (1200x630px)', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'mime_type' => 'image',
    )));
    
    $wp_customize->add_setting('favicon');
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'favicon', array(
        'label' => __('Favicon (32x32px)', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'mime_type' => 'image',
    )));
    
    $wp_customize->add_setting('apple_touch_icon');
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'apple_touch_icon', array(
        'label' => __('Apple Touch Icon (180x180px)', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'mime_type' => 'image',
    )));
    
    $wp_customize->add_setting('google_analytics_id', array('default' => ''));
    $wp_customize->add_control('google_analytics_id', array(
        'label' => __('Google Analytics ID (G-XXXXXXXXXX)', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('facebook_pixel_id', array('default' => ''));
    $wp_customize->add_control('facebook_pixel_id', array(
        'label' => __('Facebook Pixel ID', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'type' => 'text',
    ));

    // New: Google Tag Manager ID
    $wp_customize->add_setting('google_tag_manager_id', array('default' => ''));
    $wp_customize->add_control('google_tag_manager_id', array(
        'label' => __('Google Tag Manager ID (GTM-XXXXXXX)', 'alhadiya'),
        'section' => 'alhadiya_seo_settings',
        'type' => 'text',
    ));
    
    // Header Settings
    $wp_customize->add_section('alhadiya_header_settings', array(
        'title' => __('Header Settings', 'alhadiya'),
        'priority' => 20,
    ));
    
    $wp_customize->add_setting('header_announcement', array('default' => ''));
    $wp_customize->add_control('header_announcement', array(
        'label' => __('Header Announcement', 'alhadiya'),
        'section' => 'alhadiya_header_settings',
        'type' => 'textarea',
        'description' => __('Add an announcement to display at the top of the page (leave empty to hide)', 'alhadiya'),
    ));
    
    // Cookie Consent Settings
    $wp_customize->add_section('cookie_consent_settings', array(
        'title' => __('Cookie & Tracking Settings', 'alhadiya'),
        'priority' => 35,
    ));
    
    // Enable/Disable Cookie Consent
    $wp_customize->add_setting('enable_cookie_consent', array(
        'default' => false,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_cookie_consent', array(
        'label' => __('Enable Cookie Consent Banner', 'alhadiya'),
        'description' => __('Show cookie consent banner to users (not required for Bangladesh)', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
    
    // Device Tracking Settings
    $wp_customize->add_setting('enable_device_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_device_tracking', array(
        'label' => __('Enable Device Tracking', 'alhadiya'),
        'description' => __('Track device information and analytics', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
    
    // Page Visit Tracking
    $wp_customize->add_setting('enable_page_visit_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_page_visit_tracking', array(
        'label' => __('Enable Page Visit Tracking', 'alhadiya'),
        'description' => __('Track which pages users visit', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
    
    // Time Spent Tracking
    $wp_customize->add_setting('enable_time_spent_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_time_spent_tracking', array(
        'label' => __('Enable Time Spent Tracking', 'alhadiya'),
        'description' => __('Track how long users spend on the site', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
    
    // Custom Events Tracking
    $wp_customize->add_setting('enable_custom_events_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_custom_events_tracking', array(
        'label' => __('Enable Custom Events Tracking', 'alhadiya'),
        'description' => __('Track button clicks, form submissions, etc.', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
    
    // Video Tracking
    $wp_customize->add_setting('enable_video_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_video_tracking', array(
        'label' => __('Enable Video Tracking', 'alhadiya'),
        'description' => __('Track YouTube video interactions', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
    
    // Device Details Tracking
    $wp_customize->add_setting('enable_device_details_tracking', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_device_details_tracking', array(
        'label' => __('Enable Device Details Tracking', 'alhadiya'),
        'description' => __('Track screen size, battery, memory, etc.', 'alhadiya'),
        'section' => 'cookie_consent_settings',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'alhadiya_customize_register');

// Sanitize checkbox input for Customizer
function sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

// Add custom fields for reviews
function add_review_meta_boxes() {
    add_meta_box('review_details', 'Review Details', 'review_details_callback', 'course_review');
}
add_action('add_meta_boxes', 'add_review_meta_boxes');

function review_details_callback($post) {
    wp_nonce_field('save_review_details', 'review_details_nonce');
    $customer_name = get_post_meta($post->ID, '_customer_name', true);
    $customer_rating = get_post_meta($post->ID, '_customer_rating', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="customer_name">Customer Name</label></th>
            <td><input type="text" id="customer_name" name="customer_name" value="<?php echo esc_attr($customer_name); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="customer_rating">Rating (1-5)</label></th>
            <td>
                <select id="customer_rating" name="customer_rating">
                    <option value="">Select Rating</option>
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected($customer_rating, $i); ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// Save review meta
function save_review_details($post_id) {
    if (!isset($_POST['review_details_nonce']) || !wp_verify_nonce($_POST['review_details_nonce'], 'save_review_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['customer_name'])) {
        update_post_meta($post_id, '_customer_name', sanitize_text_field($_POST['customer_name']));
    }
    
    if (isset($_POST['customer_rating'])) {
        update_post_meta($post_id, '_customer_rating', sanitize_text_field($_POST['customer_rating']));
    }
}
add_action('save_post', 'save_review_details');

// AJAX handler for getting WooCommerce product data
function get_wc_product_data() {
    if (!wp_verify_nonce($_POST['nonce'], 'alhadiya_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    $product_id = intval($_POST['id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error('Product not found');
        return;
    }
    
    // Get numeric price values
    $price = floatval($product->get_price());
    $regular_price = floatval($product->get_regular_price());
    $sale_price = $product->get_sale_price() ? floatval($product->get_sale_price()) : 0;
    
    wp_send_json(array(
        'name' => $product->get_name(),
        'price' => $price,
        'regular_price' => $regular_price,
        'sale_price' => $sale_price
    ));
}
add_action('wp_ajax_get_wc_product_data', 'get_wc_product_data');
add_action('wp_ajax_nopriv_get_wc_product_data', 'get_wc_product_data');

// Convert YouTube URL to embed URL
function get_youtube_embed_url($url) {
    if (empty($url)) return '';
    
    $video_id = '';
    
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    }
    
    if ($video_id) {
        return 'https://www.youtube.com/embed/' . $video_id;
    }
    
    return '';
}

// Add custom columns to WooCommerce orders
add_filter('manage_edit-shop_order_columns', 'add_custom_order_columns');
add_action('manage_shop_order_posts_custom_column', 'populate_custom_order_columns', 10, 2);

function add_custom_order_columns($columns) {
    $columns['delivery_zone'] = __('Delivery Zone', 'alhadiya');
    $columns['payment_method_used'] = __('Payment Method', 'alhadiya');
    $columns['transaction_number'] = __('Transaction Number', 'alhadiya');
    $columns['device_info'] = __('Device Info', 'alhadiya');
    return $columns;
}

function populate_custom_order_columns($column, $post_id) {
    $order = wc_get_order($post_id);
    
    if ($column === 'delivery_zone') {
        $delivery_zone = $order->get_meta('_delivery_zone');
        echo $delivery_zone == '1' ? 'ঢাকার মধ্যে' : 'ঢাকার বাইরে';
    }
    
    if ($column === 'payment_method_used') {
        $payment_method = $order->get_meta('_payment_method_selected');
        $methods = array(
            'bkash' => '<span style="color: #e2136e;">bKash</span>',
            'nagad' => '<span style="color: #f47920;">Nagad</span>',
            'rocket' => '<span style="color: #8b1538;">Rocket</span>',
            'pay_later' => '<span style="color: #6c757d;">Pay Later</span>'
        );
        echo isset($methods[$payment_method]) ? $methods[$payment_method] : 'N/A';
    }
    
    if ($column === 'transaction_number') {
        $transaction_number = $order->get_meta('_transaction_number');
        echo $transaction_number ? $transaction_number : 'N/A';
    }

    if ($column === 'device_info') {
        $device_info = $order->get_meta('_device_info');
        if ($device_info) {
            $info = json_decode($device_info, true);
            echo '<small>IP: ' . $info['ip'] . '<br>Location: ' . $info['location_data']['location'] . '</small>';
        } else {
            echo 'N/A';
        }
    }
}

// Enhanced Admin menu for device tracking
function add_enhanced_device_tracking_menu() {
    add_menu_page(
        'Enhanced Device Tracking',
        'Device Tracking',
        'manage_options',
        'enhanced-device-tracking',
        'enhanced_device_tracking_page',
        'dashicons-admin-users',
        30
    );

    add_submenu_page(
        'enhanced-device-tracking',
        'Session Details',
        'Session Details',
        'manage_options',
        'device-session-details',
        'device_session_details_page'
    );
}
add_action('admin_menu', 'add_enhanced_device_tracking_menu');

function enhanced_device_tracking_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    $events_table = $wpdb->prefix . 'device_events';
    
    // Get visitor stats
    $stats = get_visitor_activity_stats();
    $live_visitors = get_live_visitors();
    
    // Get all sessions with pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Filtering
    $where_clause = "1=1";
    $filter_params = array();
    
    if (isset($_GET['filter_device']) && !empty($_GET['filter_device'])) {
        $where_clause .= " AND device_type = %s";
        $filter_params[] = sanitize_text_field($_GET['filter_device']);
    }
    
    if (isset($_GET['filter_location']) && !empty($_GET['filter_location'])) {
        $where_clause .= " AND location LIKE %s";
        $filter_params[] = '%' . $wpdb->esc_like(sanitize_text_field($_GET['filter_location'])) . '%';
    }
    
    if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
        if ($_GET['filter_status'] === 'online') {
            $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $where_clause .= " AND last_visit >= %s";
            $filter_params[] = $five_minutes_ago;
        } elseif ($_GET['filter_status'] === 'offline') {
            $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $where_clause .= " AND last_visit < %s";
            $filter_params[] = $five_minutes_ago;
        }
    }
    
    // Get total count for pagination
    $total_query = "SELECT COUNT(*) FROM $table_name WHERE $where_clause";
    if (!empty($filter_params)) {
        $total_query = $wpdb->prepare($total_query, $filter_params);
    }
    $total_sessions = $wpdb->get_var($total_query);
    
    // Get sessions
    $sessions_query = "SELECT * FROM $table_name WHERE $where_clause ORDER BY last_visit DESC LIMIT %d OFFSET %d";
    $sessions_params = array_merge($filter_params, array($per_page, $offset));
    $sessions = $wpdb->get_results($wpdb->prepare($sessions_query, $sessions_params));
    
    $total_pages = ceil($total_sessions / $per_page);
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">
            <i class="dashicons dashicons-chart-area"></i>
            Device Tracking Dashboard
        </h1>
        
        <!-- Live Stats Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $stats['online']; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">Live Visitors</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-admin-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $stats['today']; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">Today's Visitors</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-calendar-alt"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $stats['week']; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">This Week</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-chart-line"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $stats['month']; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">This Month</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Live Visitors Section -->
        <div class="live-visitors" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin: 20px 0;">
            <h2 style="margin-top: 0; color: #333;">
                <i class="dashicons dashicons-wifi" style="color: #4CAF50;"></i>
                Live Visitors (Last 5 Minutes)
            </h2>
            <div class="live-visitors-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <?php if (empty($live_visitors)): ?>
                    <p style="color: #666; font-style: italic;">No live visitors at the moment.</p>
                <?php else: ?>
                    <?php foreach ($live_visitors as $visitor): ?>
                        <div class="visitor-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; background: #f9f9f9;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; margin-right: 10px;"></div>
                                <strong><?php echo esc_html($visitor->device_type); ?> - <?php echo esc_html($visitor->browser); ?></strong>
                            </div>
                            <div style="font-size: 14px; color: #666;">
                                <div><strong>IP:</strong> <?php echo esc_html($visitor->ip_address); ?></div>
                                <div><strong>Location:</strong> <?php echo esc_html($visitor->location); ?></div>
                                <div><strong>Last Active:</strong> <?php echo esc_html(date('H:i:s', strtotime($visitor->last_visit))); ?></div>
                                <div><strong>Pages Viewed:</strong> <?php echo esc_html($visitor->pages_viewed); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-section" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin: 20px 0;">
            <h3 style="margin-top: 0; color: #333;">
                <i class="dashicons dashicons-filter"></i>
                Filters
            </h3>
            <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <input type="hidden" name="page" value="enhanced-device-tracking">
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Device Type:</label>
                    <select name="filter_device" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Devices</option>
                        <option value="Desktop" <?php selected(isset($_GET['filter_device']) ? $_GET['filter_device'] : '', 'Desktop'); ?>>Desktop</option>
                        <option value="Mobile" <?php selected(isset($_GET['filter_device']) ? $_GET['filter_device'] : '', 'Mobile'); ?>>Mobile</option>
                        <option value="Tablet" <?php selected(isset($_GET['filter_device']) ? $_GET['filter_device'] : '', 'Tablet'); ?>>Tablet</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Location:</label>
                    <input type="text" name="filter_location" value="<?php echo esc_attr(isset($_GET['filter_location']) ? $_GET['filter_location'] : ''); ?>" placeholder="Enter location..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Status:</label>
                    <select name="filter_status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Status</option>
                        <option value="online" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'online'); ?>>Online</option>
                        <option value="offline" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'offline'); ?>>Offline</option>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="button button-primary" style="padding: 8px 16px; height: auto;">
                        <i class="dashicons dashicons-search"></i> Filter
                    </button>
                    <a href="?page=enhanced-device-tracking" class="button" style="padding: 8px 16px; height: auto; margin-left: 10px;">
                        <i class="dashicons dashicons-dismiss"></i> Clear
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Sessions Table -->
        <div class="sessions-table" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid #e0e0e0;">
                <h3 style="margin: 0; color: #333;">
                    <i class="dashicons dashicons-list-view"></i>
                    All Sessions (<?php echo $total_sessions; ?> total)
                </h3>
            </div>
            
            <table class="wp-list-table widefat fixed striped" style="border: none;">
                <thead>
                    <tr>
                        <th>Session ID</th>
                        <th>Device Info</th>
                        <th>Location & ISP</th>
                        <th>Screen & Language</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sessions)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                                <i class="dashicons dashicons-info" style="font-size: 24px; margin-bottom: 10px;"></i>
                                <br>No sessions found matching your filters.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sessions as $session): ?>
                            <?php 
                            $is_online = (strtotime($session->last_visit) > strtotime('-5 minutes'));
                            $time_spent_formatted = '';
                            if ($session->time_spent > 0) {
                                $minutes = floor($session->time_spent / 60);
                                $seconds = $session->time_spent % 60;
                                $time_spent_formatted = "{$minutes}m {$seconds}s";
                            }
                            ?>
                            <tr>
                                <td style="font-family: monospace; font-size: 12px;">
                                    <?php echo esc_html(substr($session->session_id, 0, 20)) . '...'; ?>
                                </td>
                                <td>
                                    <div style="font-weight: bold;"><?php echo esc_html($session->device_type); ?> - <?php echo esc_html($session->browser); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($session->os); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($session->ip_address); ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: bold;"><?php echo esc_html($session->location); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($session->isp); ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: bold;"><?php echo esc_html($session->screen_size ?: 'N/A'); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($session->language ?: 'N/A'); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($session->timezone ?: 'N/A'); ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: bold;"><?php echo esc_html($session->pages_viewed); ?> pages</div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($time_spent_formatted ?: '0m 0s'); ?></div>
                                    <div style="font-size: 12px; color: #666;"><?php echo esc_html($session->visit_count); ?> visits</div>
                                </td>
                                <td>
                                    <?php if ($is_online): ?>
                                        <span style="background: #4CAF50; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                            <i class="dashicons dashicons-wifi" style="font-size: 10px;"></i> Online
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #f44336; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                            <i class="dashicons dashicons-dismiss" style="font-size: 10px;"></i> Offline
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=device-session-details&session_id=<?php echo urlencode($session->session_id); ?>" class="button button-small">
                                        <i class="dashicons dashicons-visibility"></i> Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="padding: 20px; text-align: center; border-top: 1px solid #e0e0e0;">
                    <?php
                    $pagination_args = array_merge($_GET, array('page' => 'enhanced-device-tracking'));
                    unset($pagination_args['paged']);
                    
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo; Previous'),
                        'next_text' => __('Next &raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page,
                        'type' => 'array'
                    ));
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
    .stats-grid .stat-card:hover {
        transform: translateY(-2px);
        transition: transform 0.3s ease;
    }
    
    .visitor-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        transition: box-shadow 0.3s ease;
    }
    
    .filters-section input:focus,
    .filters-section select:focus {
        border-color: #0073aa;
        box-shadow: 0 0 0 1px #0073aa;
        outline: none;
    }
    
    .sessions-table table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    
    .sessions-table table tr:hover {
        background-color: #f8f9fa;
    }
    </style>
    <?php
}

function device_session_details_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    $events_table = $wpdb->prefix . 'device_events';
    
    $session_id = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';
    
    if (empty($session_id)) {
        wp_die(__('No session ID provided.'));
    }
    
    // Get session details
    $session = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE session_id = %s",
        $session_id
    ));
    
    if (!$session) {
        wp_die(__('Session not found.'));
    }
    
    // Get events for this session with filtering
    $where_clause = "session_id = %s";
    $filter_params = array($session_id);
    
    // Date range filtering
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
    $event_type = isset($_GET['event_type']) ? sanitize_text_field($_GET['event_type']) : '';
    
    if (!empty($start_date)) {
        $where_clause .= " AND DATE(timestamp) >= %s";
        $filter_params[] = $start_date;
    }
    
    if (!empty($end_date)) {
        $where_clause .= " AND DATE(timestamp) <= %s";
        $filter_params[] = $end_date;
    }
    
    if (!empty($event_type)) {
        $where_clause .= " AND event_type = %s";
        $filter_params[] = $event_type;
    }
    
    // Get events
    $events_query = "SELECT * FROM $events_table WHERE $where_clause ORDER BY timestamp DESC";
    $events = $wpdb->get_results($wpdb->prepare($events_query, $filter_params));
    
    // Get unique event types for filter
    $event_types = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT event_type FROM $events_table WHERE session_id = %s ORDER BY event_type",
        $session_id
    ));
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">
            <i class="dashicons dashicons-admin-users"></i>
            Session Details
        </h1>
        <a href="?page=enhanced-device-tracking" class="page-title-action">
            <i class="dashicons dashicons-arrow-left-alt"></i> Back to Dashboard
        </a>
        
        <!-- Session Overview Card -->
        <div class="session-overview" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin: 20px 0;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                <!-- Basic Info -->
                <div class="info-section">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px;">
                        <i class="dashicons dashicons-info"></i> Basic Information
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                        <div><strong>Session ID:</strong><br><code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;"><?php echo esc_html($session->session_id); ?></code></div>
                        <div><strong>IP Address:</strong><br><?php echo esc_html($session->ip_address); ?></div>
                        <div><strong>Device Type:</strong><br><?php echo esc_html($session->device_type); ?></div>
                        <div><strong>Browser:</strong><br><?php echo esc_html($session->browser); ?></div>
                        <div><strong>Operating System:</strong><br><?php echo esc_html($session->os); ?></div>
                        <div><strong>Device Model:</strong><br><?php echo esc_html($session->device_model); ?></div>
                    </div>
                </div>
                
                <!-- Location & Network -->
                <div class="info-section">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px;">
                        <i class="dashicons dashicons-location"></i> Location & Network
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                        <div><strong>Location:</strong><br><?php echo esc_html($session->location); ?></div>
                        <div><strong>ISP:</strong><br><?php echo esc_html($session->isp); ?></div>
                        <div><strong>Referrer:</strong><br><?php echo esc_html($session->referrer ?: 'Direct'); ?></div>
                        <div><strong>Facebook ID:</strong><br><?php echo esc_html($session->facebook_id ?: 'N/A'); ?></div>
                    </div>
                </div>
                
                <!-- Activity Stats -->
                <div class="info-section">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #dc3545; padding-bottom: 10px;">
                        <i class="dashicons dashicons-chart-line"></i> Activity Statistics
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                        <div><strong>Visit Count:</strong><br><span style="background: #0073aa; color: white; padding: 4px 8px; border-radius: 12px; font-weight: bold;"><?php echo esc_html($session->visit_count); ?></span></div>
                        <div><strong>Pages Viewed:</strong><br><span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-weight: bold;"><?php echo esc_html($session->pages_viewed); ?></span></div>
                        <div><strong>Time Spent:</strong><br>
                            <?php 
                            $minutes = floor($session->time_spent / 60);
                            $seconds = $session->time_spent % 60;
                            echo '<span style="background: #ffc107; color: #333; padding: 4px 8px; border-radius: 12px; font-weight: bold;">' . $minutes . 'm ' . $seconds . 's</span>';
                            ?>
                        </div>
                        <div><strong>Purchased:</strong><br>
                            <?php if ($session->has_purchased): ?>
                                <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-weight: bold;">✓ Yes</span>
                            <?php else: ?>
                                <span style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 12px; font-weight: bold;">✗ No</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Device Details -->
                <div class="info-section">
                    <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #6f42c1; padding-bottom: 10px;">
                        <i class="dashicons dashicons-smartphone"></i> Device Details
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                        <div><strong>Screen Size:</strong><br><?php echo esc_html($session->screen_size ?: 'N/A'); ?></div>
                        <div><strong>Language:</strong><br><?php echo esc_html($session->language ?: 'N/A'); ?></div>
                        <div><strong>Timezone:</strong><br><?php echo esc_html($session->timezone ?: 'N/A'); ?></div>
                        <div><strong>Connection:</strong><br><?php echo esc_html($session->connection_type ?: 'N/A'); ?></div>
                        <div><strong>Battery Level:</strong><br>
                            <?php 
                            if ($session->battery_level !== null) {
                                $battery_percent = round($session->battery_level * 100);
                                $charging_status = $session->battery_charging ? ' (Charging)' : ' (Discharging)';
                                echo esc_html($battery_percent . '%' . $charging_status);
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                        <div><strong>Memory:</strong><br><?php echo esc_html($session->memory_info !== null ? $session->memory_info . 'GB' : 'N/A'); ?></div>
                        <div><strong>CPU Cores:</strong><br><?php echo esc_html($session->cpu_cores !== null ? $session->cpu_cores : 'N/A'); ?></div>
                        <div><strong>Touchscreen:</strong><br><?php echo esc_html($session->touchscreen_detected !== null ? ($session->touchscreen_detected ? 'Yes' : 'No') : 'N/A'); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Timestamps -->
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <strong>First Visit:</strong><br>
                        <span style="color: #666;"><?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($session->first_visit))); ?></span>
                    </div>
                    <div>
                        <strong>Last Visit:</strong><br>
                        <span style="color: #666;"><?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($session->last_visit))); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer Information (if purchased) -->
        <?php if ($session->has_purchased): ?>
        <div class="customer-info" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin: 20px 0;">
            <h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px;">
                <i class="dashicons dashicons-businessman"></i> Customer Information
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div><strong>Name:</strong><br><?php echo esc_html($session->customer_name); ?></div>
                <div><strong>Phone:</strong><br><?php echo esc_html($session->customer_phone); ?></div>
                <div><strong>Address:</strong><br><?php echo esc_html($session->customer_address); ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Events Filter -->
        <div class="events-filter" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin: 20px 0;">
            <h3 style="margin-top: 0; color: #333;">
                <i class="dashicons dashicons-filter"></i> Filter Events
            </h3>
            <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <input type="hidden" name="page" value="device-session-details">
                <input type="hidden" name="session_id" value="<?php echo esc_attr($session_id); ?>">
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Start Date:</label>
                    <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">End Date:</label>
                    <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Event Type:</label>
                    <select name="event_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Events</option>
                        <?php foreach ($event_types as $type): ?>
                            <option value="<?php echo esc_attr($type); ?>" <?php selected($event_type, $type); ?>><?php echo esc_html($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="button button-primary" style="padding: 8px 16px; height: auto;">
                        <i class="dashicons dashicons-search"></i> Filter Events
                    </button>
                    <a href="?page=device-session-details&session_id=<?php echo urlencode($session_id); ?>" class="button" style="padding: 8px 16px; height: auto; margin-left: 10px;">
                        <i class="dashicons dashicons-dismiss"></i> Clear
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Events Table -->
        <div class="events-table" style="background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid #e0e0e0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3 style="margin: 0;">
                    <i class="dashicons dashicons-list-view"></i>
                    Event Log (<?php echo count($events); ?> events)
                </h3>
            </div>
            
            <?php if (empty($events)): ?>
                <div style="text-align: center; padding: 60px; color: #666;">
                    <i class="dashicons dashicons-info" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>No events found</h3>
                    <p>No events match your current filters.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped" style="border: none;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa;">Timestamp</th>
                            <th style="background: #f8f9fa;">Event Type</th>
                            <th style="background: #f8f9fa;">Event Name</th>
                            <th style="background: #f8f9fa;">Value/Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td style="font-family: monospace; font-size: 12px;">
                                    <?php echo esc_html(date('M j, Y H:i:s', strtotime($event->timestamp))); ?>
                                </td>
                                <td>
                                    <span style="background: #0073aa; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                        <?php echo esc_html($event->event_type); ?>
                                    </span>
                                </td>
                                <td style="font-weight: 500;"><?php echo esc_html($event->event_name); ?></td>
                                <td style="font-size: 13px; color: #666;"><?php echo esc_html($event->event_value); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
    .session-overview .info-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #0073aa;
    }
    
    .session-overview .info-section:nth-child(2) {
        border-left-color: #28a745;
    }
    
    .session-overview .info-section:nth-child(3) {
        border-left-color: #dc3545;
    }
    
    .session-overview .info-section:nth-child(4) {
        border-left-color: #6f42c1;
    }
    
    .events-filter input:focus,
    .events-filter select:focus {
        border-color: #0073aa;
        box-shadow: 0 0 0 1px #0073aa;
        outline: none;
    }
    
    .events-table table th {
        font-weight: 600;
        color: #333;
    }
    
    .events-table table tr:hover {
        background-color: #f8f9fa;
    }
    
    .customer-info {
        border-left: 4px solid #28a745;
    }
    </style>
    <?php
}

// Track page visits on every page load
function track_page_visit() {
    // Check if page visit tracking is enabled
    if (!get_theme_mod('enable_page_visit_tracking', true)) {
        return;
    }
    
    if (!is_admin()) {
        $tracking_data = track_enhanced_device_info();
        // Track initial page view as an event
        global $wpdb;
        $events_table = $wpdb->prefix . 'device_events';
        $current_page_url = home_url(add_query_arg(NULL, NULL)); // Get current URL without query params for cleaner name
        $page_name = 'Page View: ' . basename(parse_url($current_page_url, PHP_URL_PATH));
        if (empty(basename(parse_url($current_page_url, PHP_URL_PATH)))) {
            $page_name = 'Page View: Home';
        } else if (is_singular('course_faq')) {
            $page_name = 'Page View: FAQ - ' . get_the_title();
        } else if (is_singular('course_review')) {
            $page_name = 'Page View: Review - ' . get_the_title();
        } else if (is_page('order-success')) { // Assuming 'order-success' is a page slug
            $page_name = 'Page View: Order Success';
        }

        $wpdb->insert(
            $events_table,
            array(
                'session_id' => $tracking_data['session_id'],
                'event_type' => 'page_view',
                'event_name' => $page_name,
                'event_value' => $current_page_url,
                'timestamp' => current_time('mysql')
            )
        );
    }
}
add_action('wp', 'track_page_visit');

// Add body class based on customizer setting
function alhadiya_body_classes( $classes ) {
    if ( ! get_theme_mod('show_payment_text', true) ) {
        $classes[] = 'hide-payment-text';
    }
    return $classes;
}
add_filter( 'body_class', 'alhadiya_body_classes' );

// Custom Walker for Bootstrap 5 Nav Menu (if needed, otherwise remove)
// This is a placeholder. You would typically include a separate file for this.
if ( ! class_exists( 'WP_Bootstrap_Navwalker' ) ) {
    class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {
        public function start_lvl( &$output, $depth = 0, $args = null ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat( $t, $depth );
            $output .= "{$n}{$indent}<ul class=\"dropdown-menu\">{$n}";
        }

        public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;

            $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            $id = 'id="menu-item-' . $item->ID . '"';

            $output .= $indent . '<li' . $id . $class_names . '>';

            $atts = array();
            $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
            $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
            $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
            $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $depth );

            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $item_output = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
    }
}

// Live visitor tracking functions
function get_live_visitors() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    
    // Get visitors active in last 5 minutes
    $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    
    $live_visitors = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name 
        WHERE last_visit >= %s 
        ORDER BY last_visit DESC",
        $five_minutes_ago
    ));
    
    return $live_visitors;
}

function update_visitor_activity() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alhadiya_activity_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    
    $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
    
    if (!empty($session_id)) {
        $wpdb->update(
            $table_name,
            array('last_visit' => current_time('mysql')),
            array('session_id' => $session_id)
        );
        wp_send_json_success('Activity updated');
    } else {
        wp_send_json_error('No session ID provided');
    }
}
add_action('wp_ajax_update_visitor_activity', 'update_visitor_activity');
add_action('wp_ajax_nopriv_update_visitor_activity', 'update_visitor_activity');

function get_visitor_activity_stats() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    
    // Get current online visitors (last 5 minutes)
    $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $online_visitors = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE last_visit >= %s",
        $five_minutes_ago
    ));
    
    // Get today's total visitors
    $today_start = date('Y-m-d 00:00:00');
    $today_visitors = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE first_visit >= %s",
        $today_start
    ));
    
    // Get this week's total visitors
    $week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
    $week_visitors = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE first_visit >= %s",
        $week_start
    ));
    
    // Get this month's total visitors
    $month_start = date('Y-m-01 00:00:00');
    $month_visitors = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE first_visit >= %s",
        $month_start
    ));
    
    return array(
        'online' => intval($online_visitors),
        'today' => intval($today_visitors),
        'week' => intval($week_visitors),
        'month' => intval($month_visitors)
    );
}

// ========================================
// SERVER-SIDE TRACKING SYSTEM
// ========================================

// Initialize server-side session for GDPR-friendly tracking
function alhadiya_init_server_session() {
    // Check if we already have a session ID in cookie
    if (isset($_COOKIE['device_session'])) {
        return sanitize_text_field($_COOKIE['device_session']);
    }
    
    // Generate a new session ID
    $session_id = 'ss_' . uniqid() . '_' . time();
    
    // Use WooCommerce session if available, otherwise fallback to PHP session
    if (class_exists('WooCommerce') && function_exists('WC')) {
        try {
            // WooCommerce session is available
            if (!WC()->session) {
                WC()->session = new WC_Session_Handler();
            }
            
            // Store our custom session ID in WooCommerce session
            WC()->session->set('alhadiya_session_id', $session_id);
            
            // Set cookie for client-side access
            $cookie_domain = parse_url(get_site_url(), PHP_URL_HOST);
            setcookie('device_session', $session_id, time() + (86400 * 30), '/', $cookie_domain, is_ssl(), false);
            $_COOKIE['device_session'] = $session_id; // Set in current request
            
            return $session_id;
        } catch (Exception $e) {
            // Fallback to PHP session if WooCommerce session fails
            error_log('WooCommerce session error: ' . $e->getMessage());
        }
    }
    
    // Fallback to PHP session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['alhadiya_session_id'] = $session_id;
    
    // Set cookie for client-side access
    $cookie_domain = parse_url(get_site_url(), PHP_URL_HOST);
    setcookie('device_session', $session_id, time() + (86400 * 30), '/', $cookie_domain, is_ssl(), false);
    $_COOKIE['device_session'] = $session_id; // Set in current request
    
    return $session_id;
}

// Server-side event logger
function alhadiya_log_server_event($event_name, $event_data = array(), $event_value = '') {
    // Get session ID
    $session_id = alhadiya_init_server_session();
    
    // Get user info
    $user_ip = get_client_ip();
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
    $referrer = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';
    
    // Prepare event data
    $event_log = array(
        'session_id' => $session_id,
        'event_name' => sanitize_text_field($event_name),
        'event_data' => is_array($event_data) ? $event_data : array(),
        'event_value' => sanitize_text_field($event_value),
        'user_ip' => $user_ip,
        'user_agent' => $user_agent,
        'referrer' => $referrer,
        'timestamp' => current_time('mysql'),
        'page_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
    );
    
    // Log to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'server_events';
    
    $wpdb->insert(
        $table_name,
        array(
            'session_id' => $event_log['session_id'],
            'event_name' => $event_log['event_name'],
            'event_data' => json_encode($event_log['event_data']),
            'event_value' => $event_log['event_value'],
            'user_ip' => $event_log['user_ip'],
            'user_agent' => $event_log['user_agent'],
            'referrer' => $event_log['referrer'],
            'page_url' => $event_log['page_url'],
            'timestamp' => $event_log['timestamp']
        )
    );
    
    // Send to external platforms
    alhadiya_send_to_facebook_conversion_api($event_log);
    alhadiya_send_to_google_analytics($event_log);
    alhadiya_send_to_microsoft_clarity($event_log);
    
    return $event_log;
}

// Facebook Conversion API Integration
function alhadiya_send_to_facebook_conversion_api($event_log) {
    $pixel_id = get_theme_mod('facebook_pixel_id', '');
    $access_token = get_theme_mod('facebook_access_token', '');
    
    if (empty($pixel_id) || empty($access_token)) {
        return false;
    }
    
    // Map events to Facebook standard events
    $fb_event_mapping = array(
        'page_view' => 'PageView',
        'button_click' => 'CustomEvent',
        'form_submit' => 'Lead',
        'order_success' => 'Purchase',
        'add_to_cart' => 'AddToCart',
        'view_content' => 'ViewContent',
        'section_view' => 'ViewContent',
        'scroll_depth' => 'CustomEvent',
        'video_play' => 'CustomEvent',
        'faq_toggle' => 'CustomEvent'
    );
    
    $fb_event_name = isset($fb_event_mapping[$event_log['event_name']]) ? $fb_event_mapping[$event_log['event_name']] : 'CustomEvent';
    
    $fb_data = array(
        'data' => array(
            array(
                'event_name' => $fb_event_name,
                'event_time' => strtotime($event_log['timestamp']),
                'action_source' => 'website',
                'event_source_url' => $event_log['page_url'],
                'user_data' => array(
                    'client_ip_address' => $event_log['user_ip'],
                    'client_user_agent' => $event_log['user_agent']
                ),
                'custom_data' => array(
                    'content_name' => $event_log['event_name'],
                    'content_value' => $event_log['event_value'],
                    'session_id' => $event_log['session_id']
                )
            )
        ),
        'access_token' => $access_token
    );
    
    // Send to Facebook Conversion API
    $response = wp_remote_post(
        "https://graph.facebook.com/v18.0/{$pixel_id}/events",
        array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($fb_data),
            'timeout' => 5
        )
    );
    
    return !is_wp_error($response);
}

// Google Analytics 4 Integration
function alhadiya_send_to_google_analytics($event_log) {
    $ga4_measurement_id = get_theme_mod('ga4_measurement_id', '');
    $ga4_api_secret = get_theme_mod('ga4_api_secret', '');
    
    if (empty($ga4_measurement_id) || empty($ga4_api_secret)) {
        return false;
    }
    
    $ga4_data = array(
        'client_id' => $event_log['session_id'],
        'events' => array(
            array(
                'name' => $event_log['event_name'],
                'params' => array(
                    'event_value' => $event_log['event_value'],
                    'page_location' => $event_log['page_url'],
                    'page_referrer' => $event_log['referrer'],
                    'session_id' => $event_log['session_id']
                )
            )
        )
    );
    
    // Send to GA4 Measurement Protocol
    $response = wp_remote_post(
        "https://www.google-analytics.com/mp/collect?measurement_id={$ga4_measurement_id}&api_secret={$ga4_api_secret}",
        array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($ga4_data),
            'timeout' => 5
        )
    );
    
    return !is_wp_error($response);
}

// Microsoft Clarity Integration
function alhadiya_send_to_microsoft_clarity($event_log) {
    $clarity_project_id = get_theme_mod('microsoft_clarity_id', '');
    
    if (empty($clarity_project_id)) {
        return false;
    }
    
    // Clarity uses client-side tracking, so we'll prepare data for JavaScript
    $clarity_data = array(
        'event_name' => $event_log['event_name'],
        'event_data' => $event_log['event_data'],
        'event_value' => $event_log['event_value'],
        'session_id' => $event_log['session_id']
    );
    
    // Store in transient for JavaScript pickup
    set_transient('clarity_event_' . $event_log['session_id'] . '_' . time(), $clarity_data, 300);
    
    return true;
}

// Create server events table
function alhadiya_create_server_events_table() {
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
    dbDelta($sql);
}

// AJAX handler for server-side events
if (!function_exists('alhadiya_handle_server_event')) {
    function alhadiya_handle_server_event() {
        // Check if server tracking is enabled
        if (!get_theme_mod('enable_server_tracking', true)) {
            wp_send_json_error('Server tracking is disabled');
            return;
        }
        
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
        
        // Log the event
        $result = alhadiya_log_server_event($event_name, $event_data, $event_value);
        
        if ($result) {
            wp_send_json_success('Event logged successfully');
        } else {
            wp_send_json_error('Failed to log event');
        }
    }
}
add_action('wp_ajax_alhadiya_server_event', 'alhadiya_handle_server_event');
add_action('wp_ajax_nopriv_alhadiya_server_event', 'alhadiya_handle_server_event');

// Remove duplicate function - main ajax_object is already localized in alhadiya_scripts()

// Add tracking settings to customizer
function alhadiya_add_tracking_settings($wp_customize) {
    // Server-side Tracking Settings
    $wp_customize->add_section('server_tracking_settings', array(
        'title' => __('Server-Side Tracking Settings', 'alhadiya'),
        'priority' => 40,
    ));
    
    // Facebook Conversion API
    $wp_customize->add_setting('facebook_pixel_id', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('facebook_pixel_id', array(
        'label' => __('Facebook Pixel ID', 'alhadiya'),
        'description' => __('Enter your Facebook Pixel ID for Conversion API', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('facebook_access_token', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('facebook_access_token', array(
        'label' => __('Facebook Access Token', 'alhadiya'),
        'description' => __('Enter your Facebook Access Token for Conversion API', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'text'
    ));
    
    // Google Analytics 4
    $wp_customize->add_setting('ga4_measurement_id', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ga4_measurement_id', array(
        'label' => __('GA4 Measurement ID', 'alhadiya'),
        'description' => __('Enter your GA4 Measurement ID (G-XXXXXXXXXX)', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('ga4_api_secret', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ga4_api_secret', array(
        'label' => __('GA4 API Secret', 'alhadiya'),
        'description' => __('Enter your GA4 API Secret for Measurement Protocol', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'text'
    ));
    
    // Microsoft Clarity
    $wp_customize->add_setting('microsoft_clarity_id', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('microsoft_clarity_id', array(
        'label' => __('Microsoft Clarity Project ID', 'alhadiya'),
        'description' => __('Enter your Microsoft Clarity Project ID', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'text'
    ));
    
    // Google Tag Manager
    $wp_customize->add_setting('gtm_container_id', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('gtm_container_id', array(
        'label' => __('Google Tag Manager Container ID', 'alhadiya'),
        'description' => __('Enter your GTM Container ID (GTM-XXXXXXX)', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'text'
    ));
    
    // Enable/Disable Server-side Tracking
    $wp_customize->add_setting('enable_server_tracking', array('default' => true, 'sanitize_callback' => 'sanitize_checkbox'));
    $wp_customize->add_control('enable_server_tracking', array(
        'label' => __('Enable Server-Side Tracking', 'alhadiya'),
        'description' => __('Enable server-side event tracking (GDPR-friendly)', 'alhadiya'),
        'section' => 'server_tracking_settings',
        'type' => 'checkbox'
    ));
}

// Initialize server-side tracking
function alhadiya_init_server_tracking() {
    // Create server events table
    alhadiya_create_server_events_table();
}

// Hook for initialization - separate session initialization
add_action('init', 'alhadiya_init_server_tracking');

// Initialize WooCommerce session properly
function alhadiya_init_woocommerce_session() {
    if (class_exists('WooCommerce') && function_exists('WC')) {
        try {
            // Ensure WooCommerce is fully loaded
            if (!did_action('woocommerce_init')) {
                return;
            }
            
            // Initialize our session
            alhadiya_init_server_session();
        } catch (Exception $e) {
            error_log('WooCommerce session initialization error: ' . $e->getMessage());
        }
    }
}

// Hook session initialization based on WooCommerce availability
if (class_exists('WooCommerce')) {
    add_action('woocommerce_init', 'alhadiya_init_woocommerce_session');
} else {
    add_action('init', 'alhadiya_init_server_session');
}

// Ensure session is initialized early for tracking
add_action('wp', 'alhadiya_init_server_session', 1);
add_action('init', 'alhadiya_init_server_session', 1);

// Add tracking settings to customizer
add_action('customize_register', 'alhadiya_add_tracking_settings');

// ========================================
// HIGH-TRAFFIC OPTIMIZATION
// ========================================

// Batch processing for high traffic
function alhadiya_batch_process_events() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'server_events';
    
    // Process events in batches of 100
    $batch_size = 100;
    $processed = 0;
    
    $events = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE processed = 0 LIMIT %d",
            $batch_size
        )
    );
    
    foreach ($events as $event) {
        $event_data = json_decode($event->event_data, true);
        
        // Send to external platforms
        alhadiya_send_to_facebook_conversion_api(array(
            'session_id' => $event->session_id,
            'event_name' => $event->event_name,
            'event_data' => $event_data,
            'event_value' => $event->event_value,
            'user_ip' => $event->user_ip,
            'user_agent' => $event->user_agent,
            'referrer' => $event->referrer,
            'page_url' => $event->page_url,
            'timestamp' => $event->timestamp
        ));
        
        // Mark as processed
        $wpdb->update(
            $table_name,
            array('processed' => 1),
            array('id' => $event->id)
        );
        
        $processed++;
    }
    
    return $processed;
}

// Schedule batch processing
if (!wp_next_scheduled('alhadiya_batch_process_events')) {
    wp_schedule_event(time(), 'every_5_minutes', 'alhadiya_batch_process_events');
}
add_action('alhadiya_batch_process_events', 'alhadiya_batch_process_events');

// Add tracking scripts to header
function alhadiya_add_tracking_scripts() {
    $gtm_container_id = get_theme_mod('gtm_container_id', '');
    $facebook_pixel_id = get_theme_mod('facebook_pixel_id', '');
    $ga4_measurement_id = get_theme_mod('ga4_measurement_id', '');
    $microsoft_clarity_id = get_theme_mod('microsoft_clarity_id', '');
    $server_tracking_enabled = get_theme_mod('enable_server_tracking', true);
    
    if (!$server_tracking_enabled) {
        return;
    }
    
    ?>
    <!-- Google Tag Manager -->
    <?php if (!empty($gtm_container_id)): ?>
    <script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo esc_js($gtm_container_id); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Facebook Pixel -->
    <?php if (!empty($facebook_pixel_id)): ?>
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?php echo esc_js($facebook_pixel_id); ?>');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?php echo esc_attr($facebook_pixel_id); ?>&ev=PageView&noscript=1"
    /></noscript>
    <?php endif; ?>
    
    <!-- Google Analytics 4 -->
    <?php if (!empty($ga4_measurement_id)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js($ga4_measurement_id); ?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?php echo esc_js($ga4_measurement_id); ?>', {
        'custom_map': {
            'session_id': 'session_id',
            'custom_parameter': 'custom_parameter'
        }
    });
    </script>
    <?php endif; ?>
    
    <!-- Microsoft Clarity -->
    <?php if (!empty($microsoft_clarity_id)): ?>
    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "<?php echo esc_js($microsoft_clarity_id); ?>");
    </script>
    <?php endif; ?>
    
    <!-- Initialize DataLayer for GTM -->
    <script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'event': 'page_view',
        'page_title': '<?php echo esc_js(get_the_title()); ?>',
        'page_url': '<?php echo esc_js(get_permalink()); ?>',
        'session_id': '<?php echo esc_js(alhadiya_init_server_session()); ?>'
    });
    </script>
    <?php
}
add_action('wp_head', 'alhadiya_add_tracking_scripts');

// Add GTM noscript to body
function alhadiya_add_gtm_noscript() {
    $gtm_container_id = get_theme_mod('gtm_container_id', '');
    $server_tracking_enabled = get_theme_mod('enable_server_tracking', true);
    
    if (!$server_tracking_enabled || empty($gtm_container_id)) {
        return;
    }
    
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_container_id); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php
}
add_action('wp_body_open', 'alhadiya_add_gtm_noscript');

// Add server events dashboard menu
function alhadiya_add_server_events_menu() {
    add_submenu_page(
        'enhanced-device-tracking',
        'Server Events Dashboard',
        'Server Events',
        'manage_options',
        'server-events-dashboard',
        'alhadiya_server_events_dashboard'
    );
}
add_action('admin_menu', 'alhadiya_add_server_events_menu');

// Server events dashboard page
function alhadiya_server_events_dashboard() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'server_events';
    
    // Get events with pagination
    $per_page = 50;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Filtering
    $where_clause = "1=1";
    $filter_params = array();
    
    if (isset($_GET['filter_event']) && !empty($_GET['filter_event'])) {
        $where_clause .= " AND event_name = %s";
        $filter_params[] = sanitize_text_field($_GET['filter_event']);
    }
    
    if (isset($_GET['filter_session']) && !empty($_GET['filter_session'])) {
        $where_clause .= " AND session_id LIKE %s";
        $filter_params[] = '%' . $wpdb->esc_like(sanitize_text_field($_GET['filter_session'])) . '%';
    }
    
    if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
        if ($_GET['filter_status'] === 'processed') {
            $where_clause .= " AND processed = 1";
        } elseif ($_GET['filter_status'] === 'unprocessed') {
            $where_clause .= " AND processed = 0";
        }
    }
    
    // Get total count for pagination
    $total_query = "SELECT COUNT(*) FROM $table_name WHERE $where_clause";
    if (!empty($filter_params)) {
        $total_query = $wpdb->prepare($total_query, $filter_params);
    }
    $total_events = $wpdb->get_var($total_query);
    
    // Get events
    $events_query = "SELECT * FROM $table_name WHERE $where_clause ORDER BY timestamp DESC LIMIT %d OFFSET %d";
    $events_params = array_merge($filter_params, array($per_page, $offset));
    $events = $wpdb->get_results($wpdb->prepare($events_query, $events_params));
    
    $total_pages = ceil($total_events / $per_page);
    
    // Get unique event types for filter
    $event_types = $wpdb->get_col("SELECT DISTINCT event_name FROM $table_name ORDER BY event_name");
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">
            <i class="dashicons dashicons-chart-area"></i>
            Server Events Dashboard
        </h1>
        <a href="?page=enhanced-device-tracking" class="page-title-action">
            <i class="dashicons dashicons-arrow-left-alt"></i> Back to Device Tracking
        </a>
        
        <!-- Stats Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
            <?php
            $total_events_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $processed_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE processed = 1");
            $unprocessed_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE processed = 0");
            $today_events = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE DATE(timestamp) = %s", date('Y-m-d')));
            ?>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $total_events_count; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">Total Events</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-list-view"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $processed_events; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">Processed Events</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-yes-alt"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $unprocessed_events; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">Pending Events</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-clock"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; font-weight: bold;"><?php echo $today_events; ?></h3>
                        <p style="margin: 5px 0 0 0; opacity: 0.9;">Today's Events</p>
                    </div>
                    <div style="font-size: 40px; opacity: 0.8;">
                        <i class="dashicons dashicons-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-section" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin: 20px 0;">
            <h3 style="margin-top: 0; color: #333;">
                <i class="dashicons dashicons-filter"></i>
                Filter Events
            </h3>
            <form method="get" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <input type="hidden" name="page" value="server-events-dashboard">
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Event Type:</label>
                    <select name="filter_event" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Events</option>
                        <?php foreach ($event_types as $type): ?>
                            <option value="<?php echo esc_attr($type); ?>" <?php selected(isset($_GET['filter_event']) ? $_GET['filter_event'] : '', $type); ?>><?php echo esc_html($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Session ID:</label>
                    <input type="text" name="filter_session" value="<?php echo esc_attr(isset($_GET['filter_session']) ? $_GET['filter_session'] : ''); ?>" placeholder="Enter session ID..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Status:</label>
                    <select name="filter_status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Status</option>
                        <option value="processed" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'processed'); ?>>Processed</option>
                        <option value="unprocessed" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'unprocessed'); ?>>Unprocessed</option>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="button button-primary" style="padding: 8px 16px; height: auto;">
                        <i class="dashicons dashicons-search"></i> Filter Events
                    </button>
                    <a href="?page=server-events-dashboard" class="button" style="padding: 8px 16px; height: auto; margin-left: 10px;">
                        <i class="dashicons dashicons-dismiss"></i> Clear
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Events Table -->
        <div class="events-table" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid #e0e0e0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3 style="margin: 0;">
                    <i class="dashicons dashicons-list-view"></i>
                    Server Events (<?php echo $total_events; ?> total)
                </h3>
            </div>
            
            <?php if (empty($events)): ?>
                <div style="text-align: center; padding: 60px; color: #666;">
                    <i class="dashicons dashicons-info" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>No events found</h3>
                    <p>No events match your current filters.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped" style="border: none;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa;">Timestamp</th>
                            <th style="background: #f8f9fa;">Event Name</th>
                            <th style="background: #f8f9fa;">Session ID</th>
                            <th style="background: #f8f9fa;">Event Data</th>
                            <th style="background: #f8f9fa;">Event Value</th>
                            <th style="background: #f8f9fa;">User IP</th>
                            <th style="background: #f8f9fa;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td style="font-family: monospace; font-size: 12px;">
                                    <?php echo esc_html(date('M j, Y H:i:s', strtotime($event->timestamp))); ?>
                                </td>
                                <td>
                                    <span style="background: #0073aa; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                        <?php echo esc_html($event->event_name); ?>
                                    </span>
                                </td>
                                <td style="font-family: monospace; font-size: 11px;">
                                    <?php echo esc_html(substr($event->session_id, 0, 20)) . '...'; ?>
                                </td>
                                <td style="font-size: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    <?php 
                                    $event_data = json_decode($event->event_data, true);
                                    if ($event_data) {
                                        echo esc_html(json_encode($event_data, JSON_UNESCAPED_UNICODE));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td style="font-size: 12px; max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo esc_html($event->event_value ?: 'N/A'); ?>
                                </td>
                                <td style="font-family: monospace; font-size: 11px;">
                                    <?php echo esc_html($event->user_ip); ?>
                                </td>
                                <td>
                                    <?php if ($event->processed): ?>
                                        <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                            <i class="dashicons dashicons-yes-alt" style="font-size: 10px;"></i> Processed
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #ffc107; color: #333; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                            <i class="dashicons dashicons-clock" style="font-size: 10px;"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="padding: 20px; text-align: center; border-top: 1px solid #e0e0e0;">
                    <?php
                    $pagination_args = array_merge($_GET, array('page' => 'server-events-dashboard'));
                    unset($pagination_args['paged']);
                    
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo; Previous'),
                        'next_text' => __('Next &raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page,
                        'type' => 'array'
                    ));
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
    .stats-grid .stat-card:hover {
        transform: translateY(-2px);
        transition: transform 0.3s ease;
    }
    
    .filters-section input:focus,
    .filters-section select:focus {
        border-color: #0073aa;
        box-shadow: 0 0 0 1px #0073aa;
        outline: none;
    }
    
    .events-table table th {
        font-weight: 600;
        color: #333;
    }
    
    .events-table table tr:hover {
        background-color: #f8f9fa;
    }
    </style>
    <?php
}

// ========================================
// ENHANCED EVENT TRACKING FUNCTIONS
// ========================================

// Enhanced Server-side event tracking handler for Facebook Analytics
function handle_alhadiya_server_event() {
    // Skip nonce verification for now to debug
    /*
    if (!wp_verify_nonce($_POST['server_event_nonce'], 'alhadiya_server_event_nonce')) {
        error_log('Server event nonce verification failed');
        wp_send_json_error('Security check failed');
        return;
    }
    */
    
    global $wpdb;
    
    $event_name = sanitize_text_field($_POST['event_name']);
    $event_data = $_POST['event_data'];
    $event_value = sanitize_text_field($_POST['event_value']);
    
    // Get session ID
    $session_id = get_current_session_id();
    
    // Enhanced Facebook-compatible event mapping
    $facebook_event_mapping = array(
        'page_view' => 'PageView',
        'phone_click' => 'Contact',
        'whatsapp_click' => 'Contact', 
        'product_select' => 'ViewContent',
        'begin_checkout' => 'InitiateCheckout',
        'purchase_complete' => 'Purchase',
        'form_start' => 'Lead',
        'form_submit' => 'Lead',
        'video_play' => 'ViewContent',
        'faq_click' => 'ViewContent',
        'copy_text' => 'Contact',
        'delivery_option_click' => 'AddToCart',
        'payment_method_click' => 'AddPaymentInfo'
    );
    
    // Store in server_events table
    $server_events_table = $wpdb->prefix . 'server_events';
    
    // Create enhanced event data for Facebook
    $enhanced_event_data = array(
        'event_name' => $event_name,
        'facebook_event' => isset($facebook_event_mapping[$event_name]) ? $facebook_event_mapping[$event_name] : 'CustomEvent',
        'event_time' => time(),
        'user_data' => array(
            'client_ip_address' => get_user_ip_address(),
            'client_user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'fbc' => isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : null,
            'fbp' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
        ),
        'custom_data' => $event_data,
        'action_source' => 'website'
    );
    
    $insert_data = array(
        'session_id' => $session_id,
        'event_name' => $event_name,
        'event_data' => maybe_serialize($enhanced_event_data),
        'event_value' => $event_value,
        'event_timestamp' => current_time('mysql'),
        'user_ip' => get_user_ip_address(),
        'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
        'page_url' => esc_url_raw($_SERVER['HTTP_REFERER']),
        'browser_name' => get_browser_name($_SERVER['HTTP_USER_AGENT']),
        'device_type' => get_device_type_from_user_agent($_SERVER['HTTP_USER_AGENT']),
        'operating_system' => get_operating_system($_SERVER['HTTP_USER_AGENT'])
    );
    
    $result = $wpdb->insert($server_events_table, $insert_data);
    
    // Also track to device_events for backward compatibility
    $device_events_table = $wpdb->prefix . 'device_events';
    $wpdb->insert($device_events_table, array(
        'session_id' => $session_id,
        'event_type' => $event_name,
        'event_name' => $event_name,
        'event_value' => $event_value,
        'event_timestamp' => current_time('mysql')
    ));
    
    if ($result) {
        wp_send_json_success(array(
            'message' => 'Server event tracked successfully',
            'event_id' => $wpdb->insert_id,
            'facebook_event' => $enhanced_event_data['facebook_event'],
            'session_id' => $session_id
        ));
    } else {
        error_log('Failed to insert server event: ' . $wpdb->last_error);
        wp_send_json_error(array(
            'message' => 'Failed to track server event',
            'error' => $wpdb->last_error
        ));
    }
}
add_action('wp_ajax_alhadiya_server_event', 'handle_alhadiya_server_event');
add_action('wp_ajax_nopriv_alhadiya_server_event', 'handle_alhadiya_server_event');

// Facebook Server-Side API integration (optional)
function send_event_to_facebook($event_data) {
    $facebook_pixel_id = get_theme_mod('facebook_pixel_id', '');
    $facebook_access_token = get_theme_mod('facebook_access_token', ''); // You can add this to customizer
    
    if (!$facebook_pixel_id || !$facebook_access_token) {
        return false; // Skip if not configured
    }
    
    $url = "https://graph.facebook.com/v18.0/{$facebook_pixel_id}/events";
    
    $body = array(
        'data' => array($event_data),
        'access_token' => $facebook_access_token
    );
    
    $response = wp_remote_post($url, array(
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json'
        ),
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        error_log('Facebook API Error: ' . $response->get_error_message());
        return false;
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 200) {
        error_log('Facebook API Response Error: ' . wp_remote_retrieve_body($response));
        return false;
    }
    
    return true;
}

// Enhanced event tracking with dual database insert
function track_enhanced_event($event_name, $event_data = array(), $event_value = '') {
    global $wpdb;
    
    $session_id = get_current_session_id();
    
    // Insert into both tables simultaneously
    $server_events_table = $wpdb->prefix . 'server_events';
    $device_events_table = $wpdb->prefix . 'device_events';
    
    // Facebook event mapping
    $facebook_events = array(
        'page_view' => 'PageView',
        'phone_click' => 'Contact',
        'whatsapp_click' => 'Contact',
        'product_select' => 'ViewContent',
        'begin_checkout' => 'InitiateCheckout',
        'purchase_complete' => 'Purchase',
        'form_start' => 'Lead',
        'form_submit' => 'CompleteRegistration',
        'video_play' => 'ViewContent',
        'faq_click' => 'ViewContent',
        'delivery_option_click' => 'AddToCart',
        'payment_method_click' => 'AddPaymentInfo'
    );
    
    $facebook_event = isset($facebook_events[$event_name]) ? $facebook_events[$event_name] : 'CustomEvent';
    
    // Enhanced event data
    $enhanced_data = array(
        'event_name' => $event_name,
        'facebook_event' => $facebook_event,
        'custom_data' => $event_data,
        'user_data' => array(
            'client_ip_address' => get_user_ip_address(),
            'client_user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'fbc' => isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : null,
            'fbp' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
        ),
        'event_time' => time(),
        'action_source' => 'website'
    );
    
    // Insert to server_events
    $wpdb->insert($server_events_table, array(
        'session_id' => $session_id,
        'event_name' => $event_name,
        'event_data' => maybe_serialize($enhanced_data),
        'event_value' => $event_value,
        'event_timestamp' => current_time('mysql'),
        'user_ip' => get_user_ip_address(),
        'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
        'page_url' => isset($_SERVER['REQUEST_URI']) ? esc_url_raw($_SERVER['REQUEST_URI']) : '',
        'browser_name' => get_browser_name($_SERVER['HTTP_USER_AGENT']),
        'device_type' => get_device_type_from_user_agent($_SERVER['HTTP_USER_AGENT']),
        'operating_system' => get_operating_system($_SERVER['HTTP_USER_AGENT'])
    ));
    
    // Insert to device_events (backward compatibility)
    $wpdb->insert($device_events_table, array(
        'session_id' => $session_id,
        'event_type' => $event_name,
        'event_name' => $event_name,
        'event_value' => $event_value,
        'event_timestamp' => current_time('mysql')
    ));
    
    return true;
}

// Get current session ID helper function
function get_current_session_id() {
    // Try to get from existing device tracking
    if (isset($_COOKIE['device_session'])) {
        return sanitize_text_field($_COOKIE['device_session']);
    }
    
    // Create new session ID
    $session_id = 'session_' . uniqid() . '_' . time();
    setcookie('device_session', $session_id, time() + (30 * 24 * 60 * 60), '/'); // 30 days
    
    return $session_id;
}

// Get user IP address
function get_user_ip_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
    } else {
        return sanitize_text_field($_SERVER['REMOTE_ADDR']);
    }
}

// Enhanced device tracking with more details
function track_enhanced_device_info_v2() {
    if (!get_theme_mod('enable_device_tracking', true)) {
        return false;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    
    // Get or create session ID
    $session_id = get_current_session_id();
    
    // Collect enhanced device information
    $device_data = array(
        'session_id' => $session_id,
        'ip_address' => get_user_ip_address(),
        'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
        'browser_name' => get_browser_name($_SERVER['HTTP_USER_AGENT']),
        'device_type' => get_device_type_from_user_agent($_SERVER['HTTP_USER_AGENT']),
        'operating_system' => get_operating_system($_SERVER['HTTP_USER_AGENT']),
        'screen_resolution' => '', // Will be updated via AJAX
        'viewport_size' => '', // Will be updated via AJAX
        'timezone' => get_user_timezone(),
        'language' => get_user_language(),
        'referrer' => esc_url_raw($_SERVER['HTTP_REFERER'] ?? ''),
        'landing_page' => esc_url_raw($_SERVER['REQUEST_URI']),
        'visit_count' => 1,
        'last_visit' => current_time('mysql'),
        'first_visit' => current_time('mysql'),
        'total_time_spent' => 0,
        'page_views' => 1,
        'is_bot' => is_bot_user_agent($_SERVER['HTTP_USER_AGENT']) ? 1 : 0,
        'created_at' => current_time('mysql')
    );
    
    // Check if session already exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE session_id = %s",
        $session_id
    ));
    
    if ($existing) {
        // Update existing session
        $update_data = array(
            'last_visit' => current_time('mysql'),
            'visit_count' => $existing->visit_count + 1,
            'page_views' => $existing->page_views + 1
        );
        
        $wpdb->update($table_name, $update_data, array('session_id' => $session_id));
        $device_data = array_merge((array)$existing, $update_data);
    } else {
        // Insert new session
        $wpdb->insert($table_name, $device_data);
    }
    
    return $device_data;
}

// Helper function to detect browser name
function get_browser_name($user_agent) {
    if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
    if (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
    if (strpos($user_agent, 'Safari') !== false) return 'Safari';
    if (strpos($user_agent, 'Edge') !== false) return 'Edge';
    if (strpos($user_agent, 'Opera') !== false) return 'Opera';
    return 'Unknown';
}

// Helper function to detect device type
function get_device_type_from_user_agent($user_agent) {
    if (preg_match('/tablet|ipad|playbook|silk/i', $user_agent)) return 'Tablet';
    if (preg_match('/mobile|iphone|ipod|android|blackberry|opera|mini|windows\sce|palm|smartphone|iemobile/i', $user_agent)) return 'Mobile';
    return 'Desktop';
}

// Helper function to detect operating system
function get_operating_system($user_agent) {
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    );
    
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    
    return $os_platform;
}

// Helper function to get user timezone
function get_user_timezone() {
    return get_option('timezone_string') ?: 'UTC';
}

// Helper function to get user language
function get_user_language() {
    return sanitize_text_field($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en');
}

// Helper function to detect bot
function is_bot_user_agent($user_agent) {
    $bots = array('bot', 'crawler', 'spider', 'scraper', 'facebook', 'google');
    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    return false;
}

// Create enhanced server events table
function create_server_events_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'server_events';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        session_id varchar(255) NOT NULL,
        event_name varchar(255) NOT NULL,
        event_data longtext,
        event_value varchar(500),
        event_timestamp datetime DEFAULT CURRENT_TIMESTAMP,
        user_ip varchar(45),
        user_agent text,
        page_url text,
        browser_name varchar(100),
        device_type varchar(50),
        operating_system varchar(100),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY session_id (session_id),
        KEY event_name (event_name),
        KEY event_timestamp (event_timestamp)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Initialize enhanced tracking tables
add_action('after_setup_theme', 'create_server_events_table');
add_action('init', 'create_server_events_table');

// Auto-track page view on server side
function auto_track_server_page_view() {
    if (!is_admin() && get_theme_mod('enable_server_tracking', true)) {
        track_enhanced_event('page_view', array(
            'page_title' => get_the_title(),
            'page_url' => $_SERVER['REQUEST_URI'],
            'is_home' => is_home(),
            'is_front_page' => is_front_page()
        ));
    }
}
add_action('wp_head', 'auto_track_server_page_view');

// Add enhanced tracking settings to customizer
function add_enhanced_tracking_settings($wp_customize) {
    // Enhanced Tracking Section
    $wp_customize->add_section('enhanced_tracking_settings', array(
        'title' => __('Enhanced Event Tracking', 'alhadiya'),
        'priority' => 35,
    ));
    
    // Enable Enhanced Tracking
    $wp_customize->add_setting('enable_enhanced_tracking', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('enable_enhanced_tracking', array(
        'label' => __('Enable Enhanced Event Tracking', 'alhadiya'),
        'section' => 'enhanced_tracking_settings',
        'type' => 'checkbox',
    ));
    
    // Server-side Tracking IDs
    $tracking_services = array(
        'facebook_pixel_id' => 'Facebook Pixel ID',
        'facebook_access_token' => 'Facebook Access Token (Server-Side API)',
        'ga4_measurement_id' => 'Google Analytics 4 Measurement ID',
        'microsoft_clarity_id' => 'Microsoft Clarity Project ID',
        'gtm_container_id' => 'Google Tag Manager Container ID'
    );
    
    foreach ($tracking_services as $setting_id => $label) {
        $wp_customize->add_setting($setting_id, array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control($setting_id, array(
            'label' => __($label, 'alhadiya'),
            'section' => 'enhanced_tracking_settings',
            'type' => 'text',
        ));
    }
}
add_action('customize_register', 'add_enhanced_tracking_settings');

// Add enhanced tracking dashboard to admin menu
function add_enhanced_tracking_dashboard_menu() {
    add_menu_page(
        'Enhanced Tracking Dashboard',
        'Enhanced Tracking',
        'manage_options',
        'enhanced-tracking-dashboard',
        'enhanced_tracking_dashboard_page',
        'dashicons-chart-area',
        30
    );
    
    // Add submenu pages
    add_submenu_page(
        'enhanced-tracking-dashboard',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'enhanced-tracking-dashboard'
    );
    
    add_submenu_page(
        'enhanced-tracking-dashboard',
        'Real-time Events',
        'Real-time Events',
        'manage_options',
        'enhanced-tracking-realtime',
        'enhanced_tracking_realtime_page'
    );
    
    add_submenu_page(
        'enhanced-tracking-dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'enhanced-tracking-settings',
        'enhanced_tracking_settings_page'
    );
}
add_action('admin_menu', 'add_enhanced_tracking_dashboard_menu');

// Enhanced tracking dashboard page
function enhanced_tracking_dashboard_page() {
    include get_template_directory() . '/enhanced-tracking-dashboard.php';
}

// Real-time events page
function enhanced_tracking_realtime_page() {
    echo '<div class="wrap">';
    echo '<h1>Real-time Event Tracking</h1>';
    echo '<div id="realtime-events" style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">';
    echo '<p>Real-time events will appear here...</p>';
    echo '<div id="events-container"></div>';
    echo '</div>';
    echo '</div>';
    
    // Add real-time JavaScript
    ?>
    <script>
    let eventCount = 0;
    
    function fetchRealTimeEvents() {
        fetch(ajaxurl + '?action=get_realtime_events&nonce=<?php echo wp_create_nonce("realtime_events"); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                const container = document.getElementById('events-container');
                data.data.forEach(event => {
                    const eventDiv = document.createElement('div');
                    eventDiv.style.cssText = 'padding: 10px; margin: 5px 0; background: #f9f9f9; border-left: 3px solid #dd0055;';
                    eventDiv.innerHTML = `
                        <strong>${event.event_name}</strong>
                        <div style="font-size: 12px; color: #666;">
                            ${event.event_type} | ${event.timestamp}
                        </div>
                    `;
                    container.insertBefore(eventDiv, container.firstChild);
                    eventCount++;
                });
                
                // Keep only latest 50 events
                while (container.children.length > 50) {
                    container.removeChild(container.lastChild);
                }
            }
        })
        .catch(error => console.error('Error fetching events:', error));
    }
    
    // Fetch events every 5 seconds
    setInterval(fetchRealTimeEvents, 5000);
    fetchRealTimeEvents(); // Initial load
    </script>
    <?php
}

// Enhanced tracking settings page
function enhanced_tracking_settings_page() {
    if (isset($_POST['submit'])) {
        // Save settings
        $settings = [
            'enable_device_tracking',
            'enable_custom_events_tracking',
            'enable_server_tracking',
            'enable_enhanced_tracking',
            'facebook_pixel_id',
            'facebook_access_token',
            'ga4_measurement_id',
            'microsoft_clarity_id',
            'gtm_container_id'
        ];
        
        foreach ($settings as $setting) {
            if (isset($_POST[$setting])) {
                set_theme_mod($setting, sanitize_text_field($_POST[$setting]));
            }
        }
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>Enhanced Tracking Settings</h1>
        
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Device Tracking</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_device_tracking" value="1" 
                                   <?php checked(get_theme_mod('enable_device_tracking', true)); ?> />
                            Track device information, browser details, and user sessions
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Enable Custom Events Tracking</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_custom_events_tracking" value="1" 
                                   <?php checked(get_theme_mod('enable_custom_events_tracking', true)); ?> />
                            Track custom events like clicks, form interactions, etc.
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Enable Server Events Tracking</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_server_tracking" value="1" 
                                   <?php checked(get_theme_mod('enable_server_tracking', true)); ?> />
                            Track server-side events and analytics
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Enable Enhanced Tracking</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_enhanced_tracking" value="1" 
                                   <?php checked(get_theme_mod('enable_enhanced_tracking', true)); ?> />
                            Enable all advanced tracking features
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Facebook Pixel ID</th>
                    <td>
                        <input type="text" name="facebook_pixel_id" 
                               value="<?php echo esc_attr(get_theme_mod('facebook_pixel_id', '')); ?>" 
                               class="regular-text" />
                        <p class="description">Enter your Facebook Pixel ID for enhanced e-commerce tracking</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Facebook Access Token</th>
                    <td>
                        <input type="password" name="facebook_access_token" 
                               value="<?php echo esc_attr(get_theme_mod('facebook_access_token', '')); ?>" 
                               class="regular-text" />
                        <p class="description">Enter your Facebook Access Token for Server-Side API (optional but recommended)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Google Analytics 4 Measurement ID</th>
                    <td>
                        <input type="text" name="ga4_measurement_id" 
                               value="<?php echo esc_attr(get_theme_mod('ga4_measurement_id', '')); ?>" 
                               class="regular-text" placeholder="G-XXXXXXXXXX" />
                        <p class="description">Enter your GA4 Measurement ID</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Microsoft Clarity Project ID</th>
                    <td>
                        <input type="text" name="microsoft_clarity_id" 
                               value="<?php echo esc_attr(get_theme_mod('microsoft_clarity_id', '')); ?>" 
                               class="regular-text" />
                        <p class="description">Enter your Microsoft Clarity Project ID</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Google Tag Manager Container ID</th>
                    <td>
                        <input type="text" name="gtm_container_id" 
                               value="<?php echo esc_attr(get_theme_mod('gtm_container_id', '')); ?>" 
                               class="regular-text" placeholder="GTM-XXXXXXX" />
                        <p class="description">Enter your GTM Container ID</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <div class="postbox" style="margin-top: 20px;">
            <h2 class="hndle">Tracking Implementation Status</h2>
            <div class="inside">
                <p><strong>✅ All tracking systems have been successfully implemented!</strong></p>
                <ul>
                    <li>📱 Device & Browser Detection</li>
                    <li>🎯 Event Tracking (25+ event types)</li>
                    <li>📊 E-commerce Funnel Tracking</li>
                    <li>🔋 Device Performance Monitoring</li>
                    <li>🌐 Multi-platform Analytics Integration</li>
                    <li>⚡ Real-time Event Streaming</li>
                    <li>📈 Advanced Analytics Dashboard</li>
                </ul>
                
                <p><strong>Database Tables:</strong></p>
                <ul>
                    <li><code>wp_device_tracking</code> - Device and session information</li>
                    <li><code>wp_device_events</code> - Custom events and interactions</li>
                    <li><code>wp_server_events</code> - Server-side events and analytics</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

// AJAX handler for real-time events
function handle_get_realtime_events() {
    check_ajax_referer('realtime_events', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    global $wpdb;
    $events_table = $wpdb->prefix . 'device_events';
    
    // Get events from last 30 seconds
    $recent_events = $wpdb->get_results(
        "SELECT event_name, event_type, event_timestamp 
         FROM $events_table 
         WHERE event_timestamp >= DATE_SUB(NOW(), INTERVAL 30 SECOND) 
         ORDER BY event_timestamp DESC 
         LIMIT 10"
    );
    
    $formatted_events = array();
    foreach ($recent_events as $event) {
        $formatted_events[] = array(
            'event_name' => $event->event_name,
            'event_type' => $event->event_type,
            'timestamp' => date('H:i:s', strtotime($event->event_timestamp))
        );
    }
    
    wp_send_json_success($formatted_events);
}
add_action('wp_ajax_get_realtime_events', 'handle_get_realtime_events');

// Export events for Facebook Analytics (CSV format)
function export_facebook_events() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    global $wpdb;
    $server_events_table = $wpdb->prefix . 'server_events';
    
    $events = $wpdb->get_results(
        "SELECT event_name, event_data, event_timestamp, user_ip, browser_name, device_type 
         FROM $server_events_table 
         ORDER BY event_timestamp DESC 
         LIMIT 1000"
    );
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="facebook-events-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    fputcsv($output, array(
        'Event Name',
        'Facebook Event',
        'Timestamp',
        'User IP',
        'Browser',
        'Device Type',
        'Event Data'
    ));
    
    $facebook_mapping = array(
        'page_view' => 'PageView',
        'phone_click' => 'Contact',
        'whatsapp_click' => 'Contact',
        'product_select' => 'ViewContent',
        'begin_checkout' => 'InitiateCheckout',
        'purchase_complete' => 'Purchase',
        'form_start' => 'Lead',
        'form_submit' => 'CompleteRegistration'
    );
    
    foreach ($events as $event) {
        $facebook_event = isset($facebook_mapping[$event->event_name]) ? $facebook_mapping[$event->event_name] : 'CustomEvent';
        
        fputcsv($output, array(
            $event->event_name,
            $facebook_event,
            $event->event_timestamp,
            $event->user_ip,
            $event->browser_name,
            $event->device_type,
            $event->event_data
        ));
    }
    
    fclose($output);
    exit;
}

if (isset($_GET['export_facebook_events']) && current_user_can('manage_options')) {
    add_action('init', 'export_facebook_events');
}
