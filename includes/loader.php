<?php
/**
 * Main Loader File
 * 
 * This file loads all the modular components of the Alhadiya theme
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('ALHADIYA_VERSION', '1.0.0');
define('ALHADIYA_THEME_DIR', get_template_directory());
define('ALHADIYA_THEME_URI', get_template_directory_uri());

// Load core functions
require_once ALHADIYA_THEME_DIR . '/includes/functions/theme-setup.php';
require_once ALHADIYA_THEME_DIR . '/includes/functions/custom-post-types.php';
require_once ALHADIYA_THEME_DIR . '/includes/functions/utilities.php';
require_once ALHADIYA_THEME_DIR . '/includes/functions/tracking.php';
require_once ALHADIYA_THEME_DIR . '/includes/functions/woocommerce.php';
require_once ALHADIYA_THEME_DIR . '/includes/functions/customizer.php';
require_once ALHADIYA_THEME_DIR . '/includes/functions/server-events.php';

// Load admin functions
require_once ALHADIYA_THEME_DIR . '/includes/admin/admin-functions.php';

// Load classes
require_once ALHADIYA_THEME_DIR . '/includes/classes/navigation-walker.php';

// Load Facebook CAPI integration
require_once ALHADIYA_THEME_DIR . '/facebook-capi-integration.php';

/**
 * Initialize theme
 */
function alhadiya_init() {
    // Initialize tracking if enabled
    if (get_theme_mod('enable_device_tracking', true)) {
        track_enhanced_device_info();
    }
    
    // Initialize Facebook CAPI
    if (get_theme_mod('enable_facebook_capi', true)) {
        alhadiya_init_facebook_capi();
    }
}
add_action('init', 'alhadiya_init');

/**
 * Add default FAQs
 */
function alhadiya_insert_default_faqs() {
    $faqs = array(
        array(
            'title' => 'কোর্সটি কতদিনের?',
            'content' => 'এই কোর্সটি সম্পূর্ণ করতে ৩০ দিন সময় লাগবে। আপনি আপনার সুবিধামত সময়ে শিখতে পারবেন।'
        ),
        array(
            'title' => 'কোর্সটি অনলাইনে নাকি অফলাইনে?',
            'content' => 'এই কোর্সটি সম্পূর্ণ অনলাইন। আপনি যেকোনো সময়, যেকোনো জায়গা থেকে শিখতে পারবেন।'
        ),
        array(
            'title' => 'কোর্স শেষে সার্টিফিকেট পাবো?',
            'content' => 'হ্যাঁ, কোর্স সম্পূর্ণ করার পর আপনি একটি সার্টিফিকেট পাবেন যা আপনার দক্ষতা প্রমাণ করবে।'
        ),
        array(
            'title' => 'কোর্সের ফি কত?',
            'content' => 'কোর্সের ফি মাত্র ৪৯০ টাকা। এটি একবারের জন্য, কোনো অতিরিক্ত ফি নেই।'
        ),
        array(
            'title' => 'কোর্সে কী কী শেখানো হবে?',
            'content' => 'কোর্সে অর্গানিক মেহেদী তৈরি, রঙ বাড়ানো, ব্যবসা করা, প্যাকেজিং সহ সবকিছু শেখানো হবে।'
        )
    );
    
    foreach ($faqs as $faq) {
        $existing = get_posts(array(
            'post_type' => 'course_faq',
            'post_status' => 'publish',
            'title' => $faq['title'],
            'posts_per_page' => 1
        ));
        
        if (empty($existing)) {
            wp_insert_post(array(
                'post_title' => $faq['title'],
                'post_content' => $faq['content'],
                'post_type' => 'course_faq',
                'post_status' => 'publish'
            ));
        }
    }
}
add_action('after_switch_theme', 'alhadiya_insert_default_faqs');

/**
 * Add enhanced tracking dashboard menu
 */
function add_enhanced_tracking_dashboard_menu() {
    add_submenu_page(
        'enhanced-device-tracking',
        __('Enhanced Tracking', 'alhadiya'),
        __('Enhanced Tracking', 'alhadiya'),
        'manage_options',
        'enhanced-tracking',
        'enhanced_tracking_dashboard_page'
    );
    
    add_submenu_page(
        'enhanced-device-tracking',
        __('Real-time Events', 'alhadiya'),
        __('Real-time Events', 'alhadiya'),
        'manage_options',
        'realtime-events',
        'enhanced_tracking_realtime_page'
    );
    
    add_submenu_page(
        'enhanced-device-tracking',
        __('Tracking Settings', 'alhadiya'),
        __('Tracking Settings', 'alhadiya'),
        'manage_options',
        'tracking-settings',
        'enhanced_tracking_settings_page'
    );
}
add_action('admin_menu', 'add_enhanced_tracking_dashboard_menu');

/**
 * Enhanced tracking dashboard page
 */
function enhanced_tracking_dashboard_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'device_tracking';
    $events_table = $wpdb->prefix . 'device_events';
    
    // Get statistics
    $total_sessions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_events = $wpdb->get_var("SELECT COUNT(*) FROM $events_table");
    $today_sessions = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = %s",
        current_time('Y-m-d')
    ));
    $today_events = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $events_table WHERE DATE(created_at) = %s",
        current_time('Y-m-d')
    ));
    
    ?>
    <div class="wrap">
        <h1><?php _e('Enhanced Tracking Dashboard', 'alhadiya'); ?></h1>
        
        <div class="stats-overview">
            <div class="stat-box">
                <h3><?php _e('Total Sessions', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($total_sessions); ?></p>
            </div>
            <div class="stat-box">
                <h3><?php _e('Total Events', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($total_events); ?></p>
            </div>
            <div class="stat-box">
                <h3><?php _e('Today Sessions', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($today_sessions); ?></p>
            </div>
            <div class="stat-box">
                <h3><?php _e('Today Events', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($today_events); ?></p>
            </div>
        </div>
        
        <h2><?php _e('Recent Activity', 'alhadiya'); ?></h2>
        <div id="recent-activity">
            <p><?php _e('Loading recent activity...', 'alhadiya'); ?></p>
        </div>
    </div>
    
    <style>
    .stats-overview {
        display: flex;
        gap: 20px;
        margin: 20px 0;
    }
    .stat-box {
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
        flex: 1;
    }
    .stat-box h3 {
        margin: 0 0 10px 0;
        color: #666;
    }
    .stat-box p {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
        color: #0073aa;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        function updateRecentActivity() {
            $.post(ajaxurl, {
                action: 'get_realtime_events',
                nonce: '<?php echo wp_create_nonce("realtime_events_nonce"); ?>'
            }, function(response) {
                if (response.success) {
                    $('#recent-activity').html(response.data.html);
                }
            });
        }
        
        updateRecentActivity();
        setInterval(updateRecentActivity, 10000);
    });
    </script>
    <?php
}

/**
 * Enhanced tracking realtime page
 */
function enhanced_tracking_realtime_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Real-time Events', 'alhadiya'); ?></h1>
        
        <div id="realtime-events">
            <p><?php _e('Loading real-time events...', 'alhadiya'); ?></p>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        function updateRealtimeEvents() {
            $.post(ajaxurl, {
                action: 'get_realtime_events',
                nonce: '<?php echo wp_create_nonce("realtime_events_nonce"); ?>'
            }, function(response) {
                if (response.success) {
                    $('#realtime-events').html(response.data.html);
                }
            });
        }
        
        updateRealtimeEvents();
        setInterval(updateRealtimeEvents, 5000);
    });
    </script>
    <?php
}

/**
 * Enhanced tracking settings page
 */
function enhanced_tracking_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('alhadiya_tracking_settings', $_POST['tracking_settings']);
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'alhadiya') . '</p></div>';
    }
    
    $settings = get_option('alhadiya_tracking_settings', array());
    ?>
    <div class="wrap">
        <h1><?php _e('Tracking Settings', 'alhadiya'); ?></h1>
        
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Enable Device Tracking', 'alhadiya'); ?></th>
                    <td>
                        <input type="checkbox" name="tracking_settings[enable_device_tracking]" value="1" 
                               <?php checked(isset($settings['enable_device_tracking']) ? $settings['enable_device_tracking'] : true); ?>>
                        <p class="description"><?php _e('Track device information and user behavior', 'alhadiya'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable Facebook CAPI', 'alhadiya'); ?></th>
                    <td>
                        <input type="checkbox" name="tracking_settings[enable_facebook_capi]" value="1" 
                               <?php checked(isset($settings['enable_facebook_capi']) ? $settings['enable_facebook_capi'] : true); ?>>
                        <p class="description"><?php _e('Send events to Facebook Conversion API', 'alhadiya'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable Google Analytics', 'alhadiya'); ?></th>
                    <td>
                        <input type="checkbox" name="tracking_settings[enable_google_analytics]" value="1" 
                               <?php checked(isset($settings['enable_google_analytics']) ? $settings['enable_google_analytics'] : true); ?>>
                        <p class="description"><?php _e('Send events to Google Analytics', 'alhadiya'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable Microsoft Clarity', 'alhadiya'); ?></th>
                    <td>
                        <input type="checkbox" name="tracking_settings[enable_microsoft_clarity]" value="1" 
                               <?php checked(isset($settings['enable_microsoft_clarity']) ? $settings['enable_microsoft_clarity'] : true); ?>>
                        <p class="description"><?php _e('Send events to Microsoft Clarity', 'alhadiya'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Handle get realtime events
 */
function handle_get_realtime_events() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'device_events';
    $recent_events = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10"
    );
    
    $html = '<table class="wp-list-table widefat fixed striped">';
    $html .= '<thead><tr>';
    $html .= '<th>' . __('Event Name', 'alhadiya') . '</th>';
    $html .= '<th>' . __('Event Data', 'alhadiya') . '</th>';
    $html .= '<th>' . __('Time', 'alhadiya') . '</th>';
    $html .= '</tr></thead><tbody>';
    
    if ($recent_events) {
        foreach ($recent_events as $event) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($event->event_name) . '</td>';
            $html .= '<td>' . esc_html($event->event_data) . '</td>';
            $html .= '<td>' . esc_html($event->created_at) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="3">' . __('No recent events', 'alhadiya') . '</td></tr>';
    }
    
    $html .= '</tbody></table>';
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_get_realtime_events', 'handle_get_realtime_events');

/**
 * Export Facebook events
 */
function export_facebook_events() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'server_events';
    $events = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE status = 'sent' ORDER BY created_at DESC"
    );
    
    $filename = 'facebook-events-' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, array('Event ID', 'Event Name', 'Event Time', 'Status', 'Created At'));
    
    // CSV data
    foreach ($events as $event) {
        fputcsv($output, array(
            $event->event_id,
            $event->event_name,
            date('Y-m-d H:i:s', $event->event_time),
            $event->status,
            $event->created_at
        ));
    }
    
    fclose($output);
    exit;
}
add_action('wp_ajax_export_facebook_events', 'export_facebook_events');