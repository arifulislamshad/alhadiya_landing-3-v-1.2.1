<?php
/**
 * Admin Functions
 * 
 * @package Alhadiya
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add enhanced device tracking menu
 */
function add_enhanced_device_tracking_menu() {
    add_menu_page(
        __('Device Tracking', 'alhadiya'),
        __('Device Tracking', 'alhadiya'),
        'manage_options',
        'enhanced-device-tracking',
        'enhanced_device_tracking_page',
        'dashicons-chart-area',
        30
    );
    
    add_submenu_page(
        'enhanced-device-tracking',
        __('Session Details', 'alhadiya'),
        __('Session Details', 'alhadiya'),
        'manage_options',
        'device-session-details',
        'device_session_details_page'
    );
}
add_action('admin_menu', 'add_enhanced_device_tracking_menu');

/**
 * Enhanced device tracking page
 */
function enhanced_device_tracking_page() {
    global $wpdb;
    
    // Handle bulk actions
    if (isset($_POST['action']) && $_POST['action'] !== '-1') {
        $action = sanitize_text_field($_POST['action']);
        $selected_ids = isset($_POST['bulk_ids']) ? array_map('intval', $_POST['bulk_ids']) : array();
        
        if (!empty($selected_ids)) {
            $table_name = $wpdb->prefix . 'device_tracking';
            
            switch ($action) {
                case 'delete':
                    $wpdb->query("DELETE FROM $table_name WHERE id IN (" . implode(',', $selected_ids) . ")");
                    echo '<div class="notice notice-success"><p>' . __('Selected records deleted successfully.', 'alhadiya') . '</p></div>';
                    break;
            }
        }
    }
    
    // Pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    $table_name = $wpdb->prefix . 'device_tracking';
    $total_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_records / $per_page);
    
    // Get records
    $records = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));
    
    ?>
    <div class="wrap">
        <h1><?php _e('Enhanced Device Tracking', 'alhadiya'); ?></h1>
        
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <form method="post">
                    <select name="action">
                        <option value="-1"><?php _e('Bulk Actions', 'alhadiya'); ?></option>
                        <option value="delete"><?php _e('Delete', 'alhadiya'); ?></option>
                    </select>
                    <input type="submit" class="button action" value="<?php _e('Apply', 'alhadiya'); ?>">
                </form>
            </div>
            
            <div class="tablenav-pages">
                <?php
                if ($total_pages > 1) {
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                }
                ?>
            </div>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all-1">
                    </td>
                    <th><?php _e('Session ID', 'alhadiya'); ?></th>
                    <th><?php _e('IP Address', 'alhadiya'); ?></th>
                    <th><?php _e('Device', 'alhadiya'); ?></th>
                    <th><?php _e('Browser', 'alhadiya'); ?></th>
                    <th><?php _e('OS', 'alhadiya'); ?></th>
                    <th><?php _e('Country', 'alhadiya'); ?></th>
                    <th><?php _e('Page Views', 'alhadiya'); ?></th>
                    <th><?php _e('Time Spent', 'alhadiya'); ?></th>
                    <th><?php _e('First Visit', 'alhadiya'); ?></th>
                    <th><?php _e('Last Activity', 'alhadiya'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($records): ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="bulk_ids[]" value="<?php echo esc_attr($record->id); ?>">
                            </th>
                            <td><?php echo esc_html(substr($record->session_id, 0, 15)) . '...'; ?></td>
                            <td><?php echo esc_html($record->ip_address); ?></td>
                            <td><?php echo esc_html($record->device_type); ?></td>
                            <td><?php echo esc_html($record->browser); ?></td>
                            <td><?php echo esc_html($record->os); ?></td>
                            <td><?php echo esc_html($record->country); ?></td>
                            <td><?php echo esc_html($record->page_views); ?></td>
                            <td><?php echo esc_html(gmdate('H:i:s', $record->time_spent)); ?></td>
                            <td><?php echo esc_html($record->first_visit_time); ?></td>
                            <td><?php echo esc_html($record->last_activity_time); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10"><?php _e('No records found.', 'alhadiya'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                if ($total_pages > 1) {
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Device session details page
 */
function device_session_details_page() {
    global $wpdb;
    
    $session_id = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';
    
    if (!$session_id) {
        echo '<div class="wrap"><h1>' . __('Session Details', 'alhadiya') . '</h1><p>' . __('No session ID provided.', 'alhadiya') . '</p></div>';
        return;
    }
    
    $table_name = $wpdb->prefix . 'device_tracking';
    $session_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE session_id = %s",
        $session_id
    ));
    
    if (!$session_data) {
        echo '<div class="wrap"><h1>' . __('Session Details', 'alhadiya') . '</h1><p>' . __('Session not found.', 'alhadiya') . '</p></div>';
        return;
    }
    
    // Get events for this session
    $events_table = $wpdb->prefix . 'device_events';
    $events = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $events_table WHERE session_id = %s ORDER BY created_at DESC",
        $session_id
    ));
    
    ?>
    <div class="wrap">
        <h1><?php _e('Session Details', 'alhadiya'); ?></h1>
        
        <h2><?php _e('Session Information', 'alhadiya'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php _e('Session ID', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->session_id); ?></td>
            </tr>
            <tr>
                <th><?php _e('IP Address', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->ip_address); ?></td>
            </tr>
            <tr>
                <th><?php _e('Device Type', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->device_type); ?></td>
            </tr>
            <tr>
                <th><?php _e('Browser', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->browser); ?></td>
            </tr>
            <tr>
                <th><?php _e('Operating System', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->os); ?></td>
            </tr>
            <tr>
                <th><?php _e('Country', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->country); ?></td>
            </tr>
            <tr>
                <th><?php _e('City', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->city); ?></td>
            </tr>
            <tr>
                <th><?php _e('ISP', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->isp); ?></td>
            </tr>
            <tr>
                <th><?php _e('Page Views', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->page_views); ?></td>
            </tr>
            <tr>
                <th><?php _e('Time Spent', 'alhadiya'); ?></th>
                <td><?php echo esc_html(gmdate('H:i:s', $session_data->time_spent)); ?></td>
            </tr>
            <tr>
                <th><?php _e('First Visit', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->first_visit_time); ?></td>
            </tr>
            <tr>
                <th><?php _e('Last Activity', 'alhadiya'); ?></th>
                <td><?php echo esc_html($session_data->last_activity_time); ?></td>
            </tr>
        </table>
        
        <h2><?php _e('Session Events', 'alhadiya'); ?></h2>
        <?php if ($events): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Event Name', 'alhadiya'); ?></th>
                        <th><?php _e('Event Data', 'alhadiya'); ?></th>
                        <th><?php _e('Event Value', 'alhadiya'); ?></th>
                        <th><?php _e('Page URL', 'alhadiya'); ?></th>
                        <th><?php _e('Time', 'alhadiya'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo esc_html($event->event_name); ?></td>
                            <td><?php echo esc_html($event->event_data); ?></td>
                            <td><?php echo esc_html($event->event_value); ?></td>
                            <td><?php echo esc_html($event->page_url); ?></td>
                            <td><?php echo esc_html($event->created_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('No events found for this session.', 'alhadiya'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Add server events menu
 */
function alhadiya_add_server_events_menu() {
    add_submenu_page(
        'enhanced-device-tracking',
        __('Server Events', 'alhadiya'),
        __('Server Events', 'alhadiya'),
        'manage_options',
        'server-events',
        'alhadiya_server_events_dashboard'
    );
}
add_action('admin_menu', 'alhadiya_add_server_events_menu');

/**
 * Server events dashboard
 */
function alhadiya_server_events_dashboard() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'server_events';
    
    // Handle actions
    if (isset($_POST['action'])) {
        $action = sanitize_text_field($_POST['action']);
        
        switch ($action) {
            case 'process_pending':
                alhadiya_force_process_all_pending_events();
                echo '<div class="notice notice-success"><p>' . __('Pending events processed successfully.', 'alhadiya') . '</p></div>';
                break;
        }
    }
    
    // Get statistics
    $total_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $pending_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
    $sent_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'sent'");
    $failed_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'failed'");
    
    // Get recent events
    $recent_events = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 20"
    );
    
    ?>
    <div class="wrap">
        <h1><?php _e('Server Events Dashboard', 'alhadiya'); ?></h1>
        
        <div class="stats-overview">
            <div class="stat-box">
                <h3><?php _e('Total Events', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($total_events); ?></p>
            </div>
            <div class="stat-box">
                <h3><?php _e('Pending', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($pending_events); ?></p>
            </div>
            <div class="stat-box">
                <h3><?php _e('Sent', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($sent_events); ?></p>
            </div>
            <div class="stat-box">
                <h3><?php _e('Failed', 'alhadiya'); ?></h3>
                <p><?php echo esc_html($failed_events); ?></p>
            </div>
        </div>
        
        <form method="post">
            <input type="hidden" name="action" value="process_pending">
            <input type="submit" class="button button-primary" value="<?php _e('Process Pending Events', 'alhadiya'); ?>">
        </form>
        
        <h2><?php _e('Recent Events', 'alhadiya'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Event ID', 'alhadiya'); ?></th>
                    <th><?php _e('Event Name', 'alhadiya'); ?></th>
                    <th><?php _e('Status', 'alhadiya'); ?></th>
                    <th><?php _e('Retry Count', 'alhadiya'); ?></th>
                    <th><?php _e('Created', 'alhadiya'); ?></th>
                    <th><?php _e('Sent', 'alhadiya'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_events): ?>
                    <?php foreach ($recent_events as $event): ?>
                        <tr>
                            <td><?php echo esc_html(substr($event->event_id, 0, 15)) . '...'; ?></td>
                            <td><?php echo esc_html($event->event_name); ?></td>
                            <td>
                                <span class="status-<?php echo esc_attr($event->status); ?>">
                                    <?php echo esc_html(ucfirst($event->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($event->retry_count); ?></td>
                            <td><?php echo esc_html($event->created_at); ?></td>
                            <td><?php echo esc_html($event->sent_at ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6"><?php _e('No events found.', 'alhadiya'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
    .status-pending { color: #f0ad4e; }
    .status-sent { color: #5cb85c; }
    .status-failed { color: #d9534f; }
    .status-retry { color: #f0ad4e; }
    </style>
    <?php
}