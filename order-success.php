<?php
/**
 * Template Name: Order Success Page
 * Description: Custom template for displaying order success details.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$security_nonce = isset($_GET['security']) ? sanitize_text_field($_GET['security']) : '';

$order = wc_get_order($order_id);
$is_valid_order = $order && wp_verify_nonce($security_nonce, 'order_success_' . $order_id);

?>

<div class="container order-success-page">
    <div class="order-success-content">
        <?php if ($is_valid_order) : ?>
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="success-title">অর্ডার সফল!</h1>
                <p class="success-message">আপনার অর্ডার সফলভাবে সম্পন্ন হয়েছে। আমাদের প্রতিনিধি শীঘ্রই আপনার সাথে যোগাযোগ করবে।</p>
            </div>
            
            <!-- Order Summary Details -->
            <div class="order-summary-details">
                <h2>অর্ডার বিবরণ</h2>
                <p><strong>অর্ডার নাম্বার:</strong> #<?php echo esc_html($order->get_order_number()); ?></p>
                <p><strong>মোট পরিমাণ:</strong> ৳<?php echo esc_html($order->get_total()); ?></p>
                <p><strong>পেমেন্ট পদ্ধতি:</strong> <?php echo esc_html($order->get_payment_method_title()); ?></p>
                <?php 
                $transaction_number = $order->get_meta('_transaction_number');
                if ($transaction_number) : ?>
                    <p><strong>ট্রানজেকশন নাম্বার:</strong> <?php echo esc_html($transaction_number); ?></p>
                <?php endif; ?>
                <p><strong>কাস্টমার নাম:</strong> <?php echo esc_html($order->get_billing_first_name()); ?></p>
                <p><strong>মোবাইল নাম্বার:</strong> <?php echo esc_html($order->get_billing_phone()); ?></p>
                <p><strong>ঠিকানা:</strong> <?php echo esc_html($order->get_billing_address_1()); ?></p>
            </div>

            <!-- Order Items -->
            <div class="order-items-card">
                <div class="card-header">
                    <h3>অর্ডার করা পণ্য</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>পণ্য</th>
                                    <th>পরিমাণ</th>
                                    <th>মূল্য</th>
                                    <th>মোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->get_items() as $item_id => $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <?php 
                                            $product = $item->get_product();
                                            if ($product && $product->get_image_id()) {
                                                echo wp_get_attachment_image($product->get_image_id(), 'thumbnail', false, array('class' => 'product-thumb'));
                                            }
                                            ?>
                                            <div class="product-details">
                                                <h5><?php echo esc_html($item->get_name()); ?></h5>
                                                <?php if ($product): ?>
                                                    <small class="product-sku">SKU: <?php echo esc_html($product->get_sku() ?: 'N/A'); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html($item->get_quantity()); ?></td>
                                    <td>৳<?php echo esc_html(number_format($item->get_total() / $item->get_quantity(), 2)); ?></td>
                                    <td>৳<?php echo esc_html(number_format($item->get_total(), 2)); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="subtotal-row">
                                    <td colspan="3"><strong>সাবটোটাল:</strong></td>
                                    <td><strong>৳<?php echo esc_html(number_format($order->get_subtotal(), 2)); ?></strong></td>
                                </tr>
                                <?php if ($order->get_shipping_total() > 0): ?>
                                <tr class="shipping-row">
                                    <td colspan="3"><strong>ডেলিভারি চার্জ:</strong></td>
                                    <td><strong>৳<?php echo esc_html(number_format($order->get_shipping_total(), 2)); ?></strong></td>
                                </tr>
                                <?php endif; ?>
                                <?php foreach ($order->get_fees() as $fee): ?>
                                <tr class="fee-row">
                                    <td colspan="3"><strong><?php echo esc_html($fee->get_name()); ?>:</strong></td>
                                    <td><strong>৳<?php echo esc_html(number_format($fee->get_total(), 2)); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="total-row">
                                    <td colspan="3"><strong>মোট পরিমাণ:</strong></td>
                                    <td><strong class="total-amount">৳<?php echo esc_html(number_format($order->get_total(), 2)); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="next-steps-card">
                <div class="card-header">
                    <h3>পরবর্তী ধাপ</h3>
                </div>
                <div class="card-body">
                    <div class="steps-grid">
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="step-content">
                                <h5>কনফার্মেশন কল</h5>
                                <p>আমাদের প্রতিনিধি ২৪ ঘন্টার মধ্যে আপনার সাথে যোগাযোগ করবে</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="step-content">
                                <h5>ডেলিভারি</h5>
                                <p>
                                    <?php 
                                    $delivery_zone = $order->get_meta('_delivery_zone');
                                    if ($delivery_zone == '1') {
                                        echo esc_html('ঢাকার মধ্যে ১-২ দিনে ডেলিভারি');
                                    } else {
                                        echo esc_html('ঢাকার বাইরে ৩-৫ দিনে ডেলিভারি');
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="step-content">
                                <h5>কোর্স শুরু</h5>
                                <p>পণ্য পাওয়ার পর আপনি কোর্স শুরু করতে পারবেন</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Actions -->
            <div class="order-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-primary">হোমপেজে ফিরে যান</a>
                <a href="tel:<?php echo esc_attr(get_theme_mod('phone_number', '+8801737146996')); ?>" class="button button-success">
                    <i class="fas fa-phone"></i> কল করুন
                </a>
                <a href="https://wa.me/88<?php echo esc_attr(get_theme_mod('whatsapp_number', '1737146996')); ?>" target="_blank" class="button button-secondary">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>

        <?php else : ?>
            <!-- Error Header -->
            <div class="error-header">
                <div class="error-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1 class="error-title">অর্ডার খুঁজে পাওয়া যায়নি!</h1>
                <p class="error-message">আপনার অর্ডারটি খুঁজে পাওয়া যায়নি অথবা লিঙ্কটি অবৈধ। অনুগ্রহ করে সঠিক অর্ডার আইডি দিয়ে চেষ্টা করুন অথবা আমাদের সাথে যোগাযোগ করুন।</p>
            </div>

            <!-- Order Actions -->
            <div class="order-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-primary">হোমপেজে ফিরে যান</a>
                <a href="tel:<?php echo esc_attr(get_theme_mod('phone_number', '+8801737146996')); ?>" class="button button-success">
                    <i class="fas fa-phone"></i> কল করুন
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.order-success-page {
    padding: 40px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.order-success-content {
    max-width: 1000px;
    margin: 0 auto;
}

.success-header,
.error-header {
    text-align: center;
    margin-bottom: 40px;
}

.success-icon,
.error-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 50px;
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    animation: pulse 2s infinite;
}

.error-icon {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.success-title,
.error-title {
    font-size: 36px;
    color: #2c3e50;
    margin-bottom: 10px;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.success-message,
.error-message {
    font-size: 18px;
    color: #6c757d;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.order-summary-details {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
    padding: 30px;
}

.order-summary-details h2 {
    margin-bottom: 20px;
    font-family: "SolaimanLipi", Arial, sans-serif;
    border-bottom: 2px solid #dd0055;
    padding-bottom: 10px;
}

.order-summary-details p {
    margin-bottom: 10px;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.order-items-card,
.next-steps-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #dd0055 0%, #ff1744 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2,
.card-header h3 {
    margin: 0;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.order-items-table {
    width: 100%;
    border-collapse: collapse;
}

.order-items-table th,
.order-items-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.order-items-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.product-details h5 {
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.product-sku {
    color: #6c757d;
}

.subtotal-row,
.shipping-row,
.fee-row {
    background: #f8f9fa;
}

.total-row {
    background: #dd0055;
    color: white;
}

.total-row td {
    border-bottom: none;
}

.total-amount {
    font-size: 20px;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.step-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.step-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #dd0055 0%, #ff1744 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.step-content h5 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.step-content p {
    color: #6c757d;
    margin: 0;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.order-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin: 40px 0;
    flex-wrap: wrap;
}

.order-actions .button {
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    font-family: "SolaimanLipi", Arial, sans-serif;
}

.order-actions .button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .order-success-page {
        padding: 20px 0;
    }
    
    .success-title,
    .error-title {
        font-size: 28px;
    }
    
    .card-header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .order-items-table {
        font-size: 14px;
    }
    
    .product-info {
        flex-direction: column;
        text-align: center;
    }
    
    .steps-grid {
        grid-template-columns: 1fr;
    }
    
    .order-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .order-actions .button {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}
</style>

<?php
// Enhanced tracking for successful order
if (function_exists('track_enhanced_device_info')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'device_tracking';
    $session_id = isset($_COOKIE['device_session']) ? $_COOKIE['device_session'] : '';
    
    if ($session_id) {
        // Update device tracking
        $wpdb->update(
            $table_name,
            array('has_purchased' => 1),
            array('session_id' => $session_id)
        );
        
        // Track order success event
        $events_table = $wpdb->prefix . 'device_events';
        $wpdb->insert(
            $events_table,
            array(
                'session_id' => $session_id,
                'event_type' => 'order_success_page_view',
                'event_name' => 'Order Success Page Viewed',
                'event_value' => 'Order completion confirmed',
                'event_timestamp' => current_time('mysql')
            )
        );
        
        // Track server event for order success
        $server_events_table = $wpdb->prefix . 'server_events';
        $wpdb->insert(
            $server_events_table,
            array(
                'session_id' => $session_id,
                'event_name' => 'Order Success Page View',
                'event_data' => maybe_serialize(array(
                    'page' => 'order-success',
                    'conversion' => true,
                    'timestamp' => current_time('mysql')
                )),
                'event_timestamp' => current_time('mysql'),
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'page_url' => $_SERVER['REQUEST_URI']
            )
        );
    }
}
?>

<!-- Enhanced Order Success Tracking -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track order success page view
    if (typeof trackUniversalEvent === 'function') {
        trackUniversalEvent('order_success_page_view', {
            page: 'order-success',
            conversion: true,
            timestamp: new Date().toISOString(),
            browser: getBrowserInfo ? getBrowserInfo().name : 'Unknown',
            device_type: getDeviceType ? getDeviceType() : 'Unknown'
        });
    }
    
    // Google Analytics Enhanced E-commerce - Purchase Event
    if (typeof gtag === 'function') {
        gtag('event', 'purchase', {
            transaction_id: '<?php echo $order_id; ?>',
            value: <?php echo $total_amount; ?>,
            currency: 'BDT',
            items: [{
                item_id: '<?php echo $product_id; ?>',
                item_name: '<?php echo $product_name; ?>',
                category: 'Course',
                quantity: 1,
                price: <?php echo $product_price; ?>
            }]
        });
    }
    
    // Facebook Pixel Purchase Event
    if (typeof fbq === 'function') {
        fbq('track', 'Purchase', {
            value: <?php echo $total_amount; ?>,
            currency: 'BDT',
            content_ids: ['<?php echo $product_id; ?>'],
            content_type: 'product',
            content_name: '<?php echo $product_name; ?>'
        });
    }
    
    // Microsoft Clarity Custom Event
    if (typeof clarity === 'function') {
        clarity('event', 'purchase_complete', {
            order_id: '<?php echo $order_id; ?>',
            value: <?php echo $total_amount; ?>,
            product: '<?php echo $product_name; ?>'
        });
    }
    
    console.log('✅ Order success tracking completed for Order #<?php echo $order_id; ?>');
});
</script>

<?php get_footer(); ?>
