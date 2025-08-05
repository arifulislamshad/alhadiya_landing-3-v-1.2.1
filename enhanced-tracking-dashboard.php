<?php
/**
 * Enhanced Tracking Dashboard
 * 
 * This file provides a comprehensive dashboard for viewing all tracking data
 * including device info, events, performance metrics, and user behavior analytics.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if user has admin privileges
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

global $wpdb;

// Get tracking tables
$device_table = $wpdb->prefix . 'device_tracking';
$events_table = $wpdb->prefix . 'device_events';
$server_events_table = $wpdb->prefix . 'server_events';

// Date filter
$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date('Y-m-d', strtotime('-7 days'));
$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : date('Y-m-d');

// Get summary statistics
$total_sessions = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(DISTINCT session_id) FROM $device_table WHERE DATE(last_visit) BETWEEN %s AND %s",
    $date_from, $date_to
));

$total_events = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $events_table WHERE DATE(event_timestamp) BETWEEN %s AND %s",
    $date_from, $date_to
));

$total_server_events = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $server_events_table WHERE DATE(event_timestamp) BETWEEN %s AND %s",
    $date_from, $date_to
));

$total_page_views = $wpdb->get_var($wpdb->prepare(
    "SELECT SUM(page_views) FROM $device_table WHERE DATE(last_visit) BETWEEN %s AND %s",
    $date_from, $date_to
));

// Get top events
$top_events = $wpdb->get_results($wpdb->prepare(
    "SELECT event_type, COUNT(*) as count FROM $events_table 
     WHERE DATE(event_timestamp) BETWEEN %s AND %s 
     GROUP BY event_type ORDER BY count DESC LIMIT 10",
    $date_from, $date_to
));

// Get browser stats
$browser_stats = $wpdb->get_results($wpdb->prepare(
    "SELECT browser_name, COUNT(*) as count FROM $device_table 
     WHERE DATE(last_visit) BETWEEN %s AND %s AND browser_name IS NOT NULL 
     GROUP BY browser_name ORDER BY count DESC",
    $date_from, $date_to
));

// Get device type stats
$device_stats = $wpdb->get_results($wpdb->prepare(
    "SELECT device_type, COUNT(*) as count FROM $device_table 
     WHERE DATE(last_visit) BETWEEN %s AND %s AND device_type IS NOT NULL 
     GROUP BY device_type ORDER BY count DESC",
    $date_from, $date_to
));

// Get recent events from both tables
$recent_device_events = $wpdb->get_results($wpdb->prepare(
    "SELECT e.*, d.browser_name, d.device_type, 'device' as source FROM $events_table e 
     LEFT JOIN $device_table d ON e.session_id = d.session_id 
     WHERE DATE(e.event_timestamp) BETWEEN %s AND %s 
     ORDER BY e.event_timestamp DESC LIMIT 25",
    $date_from, $date_to
));

$recent_server_events = $wpdb->get_results($wpdb->prepare(
    "SELECT event_name, event_timestamp, event_value, browser_name, device_type, 'server' as source
     FROM $server_events_table 
     WHERE DATE(event_timestamp) BETWEEN %s AND %s 
     ORDER BY event_timestamp DESC LIMIT 25",
    $date_from, $date_to
));

// Combine and sort all events
$recent_events = array_merge($recent_device_events, $recent_server_events);
usort($recent_events, function($a, $b) {
    return strtotime($b->event_timestamp) - strtotime($a->event_timestamp);
});
$recent_events = array_slice($recent_events, 0, 50);

?>

<div class="wrap">
    <h1><span class="dashicons dashicons-chart-area" style="color: #dd0055;"></span> Enhanced Tracking Dashboard</h1>
    
    <!-- Date Filter -->
    <div class="tablenav top">
        <form method="get" style="display: inline-block;">
            <input type="hidden" name="page" value="enhanced-tracking-dashboard">
            <label for="date_from">From:</label>
            <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>" />
            <label for="date_to">To:</label>
            <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>" />
            <input type="submit" class="button" value="Filter" />
        </form>
        
        <button id="test-server-event" class="button button-primary" style="margin-left: 20px;">
            🧪 Test Server Event
        </button>
        
        <button id="refresh-events" class="button" style="margin-left: 10px;">
            🔄 Refresh
        </button>
        
        <a href="<?php echo admin_url('admin.php?page=enhanced-tracking-dashboard&export_facebook_events=1'); ?>" 
           class="button" style="margin-left: 10px;">
            📊 Export Facebook Events (CSV)
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="enhanced-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px;">
            <div class="stat-icon" style="font-size: 24px; margin-bottom: 10px;">👥</div>
            <div class="stat-number" style="font-size: 32px; font-weight: bold;"><?php echo number_format($total_sessions); ?></div>
            <div class="stat-label">Total Sessions</div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 10px;">
            <div class="stat-icon" style="font-size: 24px; margin-bottom: 10px;">⚡</div>
            <div class="stat-number" style="font-size: 32px; font-weight: bold;"><?php echo number_format($total_events); ?></div>
            <div class="stat-label">Custom Events</div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 10px;">
            <div class="stat-icon" style="font-size: 24px; margin-bottom: 10px;">🎯</div>
            <div class="stat-number" style="font-size: 32px; font-weight: bold;"><?php echo number_format($total_server_events); ?></div>
            <div class="stat-label">Server Events</div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 10px;">
            <div class="stat-icon" style="font-size: 24px; margin-bottom: 10px;">📄</div>
            <div class="stat-number" style="font-size: 32px; font-weight: bold;"><?php echo number_format($total_page_views); ?></div>
            <div class="stat-label">Page Views</div>
        </div>
    </div>

    <!-- Analytics Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
        
        <!-- Top Events -->
        <div class="postbox">
            <h2 class="hndle" style="background: #dd0055; color: white; padding: 10px;">🔥 Top Events</h2>
            <div class="inside">
                <?php if ($top_events): ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Event Type</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_events as $event): ?>
                                <?php $percentage = ($total_events > 0) ? round(($event->count / $total_events) * 100, 1) : 0; ?>
                                <tr>
                                    <td><strong><?php echo esc_html($event->event_type); ?></strong></td>
                                    <td><?php echo number_format($event->count); ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <div style="background: #dd0055; height: 10px; width: <?php echo $percentage; ?>%; max-width: 100px; margin-right: 10px; border-radius: 5px;"></div>
                                            <?php echo $percentage; ?>%
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No event data available for the selected date range.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Browser Stats -->
        <div class="postbox">
            <h2 class="hndle" style="background: #4CAF50; color: white; padding: 10px;">🌐 Browser Usage</h2>
            <div class="inside">
                <?php if ($browser_stats): ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Browser</th>
                                <th>Sessions</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($browser_stats as $browser): ?>
                                <?php $percentage = ($total_sessions > 0) ? round(($browser->count / $total_sessions) * 100, 1) : 0; ?>
                                <tr>
                                    <td><strong><?php echo esc_html($browser->browser_name); ?></strong></td>
                                    <td><?php echo number_format($browser->count); ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <div style="background: #4CAF50; height: 10px; width: <?php echo $percentage; ?>%; max-width: 100px; margin-right: 10px; border-radius: 5px;"></div>
                                            <?php echo $percentage; ?>%
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No browser data available for the selected date range.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Device Stats -->
    <div class="postbox">
        <h2 class="hndle" style="background: #FF9800; color: white; padding: 10px;">📱 Device Types</h2>
        <div class="inside">
            <?php if ($device_stats): ?>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <?php foreach ($device_stats as $device): ?>
                        <?php 
                        $percentage = ($total_sessions > 0) ? round(($device->count / $total_sessions) * 100, 1) : 0;
                        $device_icons = [
                            'Desktop' => '🖥️',
                            'Mobile' => '📱',
                            'Tablet' => '📱'
                        ];
                        $icon = isset($device_icons[$device->device_type]) ? $device_icons[$device->device_type] : '💻';
                        ?>
                        <div style="text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 10px; min-width: 150px;">
                            <div style="font-size: 48px; margin-bottom: 10px;"><?php echo $icon; ?></div>
                            <h3 style="margin: 10px 0;"><?php echo esc_html($device->device_type); ?></h3>
                            <div style="font-size: 24px; font-weight: bold; color: #FF9800;"><?php echo number_format($device->count); ?></div>
                            <div style="color: #666;"><?php echo $percentage; ?>%</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No device data available for the selected date range.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Events Timeline -->
    <div class="postbox">
        <h2 class="hndle" style="background: #9C27B0; color: white; padding: 10px;">⏰ Recent Events Timeline</h2>
        <div class="inside">
                            <?php if ($recent_events): ?>
                    <div class="events-timeline" style="max-height: 500px; overflow-y: auto;">
                        <?php foreach ($recent_events as $event): ?>
                            <?php 
                            $border_color = ($event->source === 'server') ? '#4CAF50' : '#dd0055';
                            $source_icon = ($event->source === 'server') ? '🎯' : '📱';
                            $event_type = isset($event->event_type) ? $event->event_type : $event->event_name;
                            ?>
                            <div class="timeline-event" style="padding: 10px; border-left: 3px solid <?php echo $border_color; ?>; margin: 10px 0; background: #f9f9f9;">
                                <div style="display: flex; justify-content: between; align-items: center;">
                                    <div>
                                        <strong><?php echo $source_icon; ?> <?php echo esc_html($event->event_name); ?></strong>
                                        <div style="font-size: 12px; color: #666;">
                                            Type: <?php echo esc_html($event_type); ?> | 
                                            Source: <?php echo esc_html(ucfirst($event->source)); ?> | 
                                            Browser: <?php echo esc_html($event->browser_name ?: 'Unknown'); ?> | 
                                            Device: <?php echo esc_html($event->device_type ?: 'Unknown'); ?>
                                        </div>
                                        <?php if ($event->event_value): ?>
                                            <div style="font-size: 11px; color: #888; margin-top: 5px;">
                                                Value: <?php echo esc_html($event->event_value); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($event->source === 'server'): ?>
                                            <div style="font-size: 10px; background: #4CAF50; color: white; padding: 2px 6px; border-radius: 10px; display: inline-block; margin-top: 5px;">
                                                Facebook Ready
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align: right; font-size: 11px; color: #666;">
                                        <?php echo date('H:i:s', strtotime($event->event_timestamp)); ?><br>
                                        <?php echo date('d/m/Y', strtotime($event->event_timestamp)); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
            <?php else: ?>
                <p>No recent events found for the selected date range.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Facebook Events Summary -->
    <div class="postbox">
        <h2 class="hndle" style="background: #1877F2; color: white; padding: 10px;">📘 Facebook Events Summary</h2>
        <div class="inside">
            <?php
            // Get Facebook event mapping stats
            $facebook_events = $wpdb->get_results($wpdb->prepare(
                "SELECT event_name, COUNT(*) as count FROM $server_events_table 
                 WHERE DATE(event_timestamp) BETWEEN %s AND %s 
                 GROUP BY event_name ORDER BY count DESC LIMIT 10",
                $date_from, $date_to
            ));
            
            $facebook_mapping = array(
                'page_view' => 'PageView',
                'phone_click' => 'Contact',
                'whatsapp_click' => 'Contact',
                'product_select' => 'ViewContent',
                'begin_checkout' => 'InitiateCheckout',
                'purchase_complete' => 'Purchase',
                'form_start' => 'Lead',
                'form_submit' => 'CompleteRegistration',
                'video_play' => 'ViewContent',
                'faq_click' => 'ViewContent'
            );
            ?>
            
            <?php if ($facebook_events): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Custom Event</th>
                            <th>Facebook Event</th>
                            <th>Count</th>
                            <th>Analytics Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facebook_events as $event): ?>
                            <?php 
                            $fb_event = isset($facebook_mapping[$event->event_name]) ? $facebook_mapping[$event->event_name] : 'CustomEvent';
                            $analytics_value = '';
                            switch($fb_event) {
                                case 'PageView': $analytics_value = '👀 Brand Awareness'; break;
                                case 'Contact': $analytics_value = '📞 Lead Generation'; break;
                                case 'ViewContent': $analytics_value = '👁️ Content Engagement'; break;
                                case 'InitiateCheckout': $analytics_value = '🛒 Purchase Intent'; break;
                                case 'Purchase': $analytics_value = '💰 Conversion'; break;
                                case 'Lead': $analytics_value = '🎯 Lead Quality'; break;
                                default: $analytics_value = '📊 Custom Tracking'; break;
                            }
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($event->event_name); ?></strong></td>
                                <td>
                                    <span style="background: #1877F2; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px;">
                                        <?php echo esc_html($fb_event); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($event->count); ?></td>
                                <td><?php echo $analytics_value; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 15px; padding: 10px; background: #f0f8ff; border-left: 4px solid #1877F2;">
                    <strong>📘 Facebook Analytics Benefits:</strong>
                    <ul style="margin: 10px 0;">
                        <li>✅ <strong>Audience Insights:</strong> Understanding user behavior patterns</li>
                        <li>✅ <strong>Ad Optimization:</strong> Better targeting based on tracked events</li>
                        <li>✅ <strong>Conversion Tracking:</strong> Measuring ad performance and ROI</li>
                        <li>✅ <strong>Custom Audiences:</strong> Retargeting based on specific actions</li>
                        <li>✅ <strong>Lookalike Audiences:</strong> Finding similar customers</li>
                    </ul>
                </div>
            <?php else: ?>
                <p>No Facebook events tracked yet. Events will appear here once users start interacting with your site.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Real-time Tracking Status -->
    <div class="postbox">
        <h2 class="hndle" style="background: #2196F3; color: white; padding: 10px;">🔴 Live Tracking Status</h2>
        <div class="inside">
            <div class="tracking-status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="status-item">
                    <div class="status-indicator" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <span class="status-dot" style="width: 12px; height: 12px; background: #4CAF50; border-radius: 50%; margin-right: 10px; animation: pulse 1s infinite;"></span>
                        <strong>Device Tracking</strong>
                    </div>
                    <div>Status: <?php echo get_theme_mod('enable_device_tracking', true) ? '✅ Active' : '❌ Disabled'; ?></div>
                </div>
                
                <div class="status-item">
                    <div class="status-indicator" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <span class="status-dot" style="width: 12px; height: 12px; background: #FF9800; border-radius: 50%; margin-right: 10px; animation: pulse 1s infinite;"></span>
                        <strong>Custom Events</strong>
                    </div>
                    <div>Status: <?php echo get_theme_mod('enable_custom_events_tracking', true) ? '✅ Active' : '❌ Disabled'; ?></div>
                </div>
                
                <div class="status-item">
                    <div class="status-indicator" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <span class="status-dot" style="width: 12px; height: 12px; background: #9C27B0; border-radius: 50%; margin-right: 10px; animation: pulse 1s infinite;"></span>
                        <strong>Server Events</strong>
                    </div>
                    <div>Status: <?php echo get_theme_mod('enable_server_tracking', true) ? '✅ Active' : '❌ Disabled'; ?></div>
                </div>
                
                <div class="status-item">
                    <div class="status-indicator" style="display: flex; align-items: center; margin-bottom: 10px;">
                        <span class="status-dot" style="width: 12px; height: 12px; background: #f44336; border-radius: 50%; margin-right: 10px; animation: pulse 1s infinite;"></span>
                        <strong>Enhanced Tracking</strong>
                    </div>
                    <div>Status: <?php echo get_theme_mod('enable_enhanced_tracking', true) ? '✅ Active' : '❌ Disabled'; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.timeline-event {
    transition: all 0.2s ease;
}

.timeline-event:hover {
    background: #f0f0f0 !important;
    border-left-width: 5px !important;
}

.events-timeline {
    scrollbar-width: thin;
    scrollbar-color: #dd0055 #f1f1f1;
}

.events-timeline::-webkit-scrollbar {
    width: 6px;
}

.events-timeline::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.events-timeline::-webkit-scrollbar-thumb {
    background: #dd0055;
    border-radius: 3px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test Server Event button
    document.getElementById('test-server-event').addEventListener('click', function() {
        this.innerHTML = '⏳ Testing...';
        this.disabled = true;
        
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'alhadiya_server_event',
                event_name: 'test_server_event',
                event_data: JSON.stringify({
                    test: true,
                    timestamp: new Date().toISOString(),
                    source: 'admin_dashboard'
                }),
                event_value: 'Dashboard Test',
                // Skip nonce for testing
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Server Event Test Successful!\n\nEvent ID: ' + data.data.event_id + '\nSession ID: ' + data.data.session_id);
                location.reload(); // Refresh to show the new event
            } else {
                alert('❌ Server Event Test Failed!\n\nError: ' + data.data);
            }
        })
        .catch(error => {
            alert('❌ Network Error: ' + error.message);
        })
        .finally(() => {
            this.innerHTML = '🧪 Test Server Event';
            this.disabled = false;
        });
    });
    
    // Refresh button
    document.getElementById('refresh-events').addEventListener('click', function() {
        location.reload();
    });
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        const refreshButton = document.getElementById('refresh-events');
        refreshButton.innerHTML = '🔄 Auto-refreshing...';
        setTimeout(() => {
            location.reload();
        }, 1000);
    }, 30000);
});
</script>