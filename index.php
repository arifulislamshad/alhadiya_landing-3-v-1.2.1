<?php 
// Initialize device tracking and create session ID
if (get_theme_mod('enable_device_tracking', true)) {
    $device_info = track_enhanced_device_info();
    $session_id = $device_info['session_id'] ?? null;
} else {
    $session_id = null;
}
?>
<?php get_header(); ?>

<div class="container">
    <!-- Logo Section -->
    <div class="logo">
        <?php if (has_custom_logo()): ?>
            <?php the_custom_logo(); ?>
        <?php else: ?>
            <h1 style="color: #dd0055; font-size: 32px; margin: 15px 0; font-family: 'SolaimanLipi', Arial, sans-serif;">
                <?php echo esc_html(get_bloginfo('name')); ?>
            </h1>
        <?php endif; ?>
    </div>

    <!-- Main Heading -->
    <h1 class="main-heading">
        <?php echo esc_html(get_theme_mod('main_heading_text', 'অর্গানিক হাতের মেহেদি বানানোর কোর্স মাত্র ৪৯০ টাকা')); ?>
    </h1>
    
    <!-- Video Section -->
    <?php 
    $youtube_video_url = get_theme_mod('youtube_video_url', '');
    if ($youtube_video_url) {
        $embed_url = get_youtube_embed_url($youtube_video_url);
        if ($embed_url) {
    ?>
    <div class="video-container" id="video-section">
        <div class="video-wrapper">
            <iframe id="youtube-video-player" src="<?php echo esc_url($embed_url); ?>?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    <?php 
        }
    }
    ?>
    
    <!-- Course Details - Now Dynamic with Individual Colors -->
    <section class="Corse_container" id="course-section-1" style="--section-color: <?php echo get_theme_mod('section1_color', '#28a745'); ?>">
        <h3 style="color: var(--section-color);"><?php echo esc_html(get_theme_mod('section1_title', '🌱 অর্গানিক মেহেদী তৈরির সহজ উপায়')); ?></h3>
        <div class="Corse_dtail">
            <ul class="Corse_dtail_left">
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item1_color', '#28a745')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item1_text', 'অর্গানিক হাতের মেহেদী তৈরি')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item2_color', '#28a745')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item2_text', 'ড্রাই রিলিজ কিভাবে করে')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item3_color', '#28a745')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item3_text', 'মেহেদী কোণ তৈরি')); ?>
                </li>
            </ul>
            <ul class="Corse_dtail_right">
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item4_color', '#28a745')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item4_text', 'প্রফেশনাল রেসিপি শিট')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item5_color', '#28a745')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item5_text', 'কি কি তেল ব্যবহার করা যাবে')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item6_color', '#28a745')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item6_text', 'কিভাবে মেহেদির রঙ গাড় হবে (সিক্রেট টিপস)')); ?>
                </li>
            </ul>
        </div>
    </section>
    
    <section class="Corse_container" id="course-section-2" style="--section-color: <?php echo get_theme_mod('section2_color', '#dc3545'); ?>">
        <h3 style="color: var(--section-color);"><?php echo esc_html(get_theme_mod('section2_title', '🔥 মেহেদী রঙ বাড়ানোর গোপন টিপস')); ?></h3>
        <div class="Corse_dtail">
            <ul class="Corse_dtail_left">
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item7_color', '#dc3545')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item7_text', 'মেহেদির মূল্য নির্ধারণ')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item8_color', '#dc3545')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item8_text', 'দীর্ঘদিন সংরক্ষণ কিভাবে করবেন')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item9_color', '#dc3545')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item9_text', 'মেহেদী কিভাবে বিক্রি করবেন')); ?>
                </li>
            </ul>
            <ul class="Corse_dtail_right">
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item10_color', '#dc3545')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item10_text', 'সার্টিফিকেট প্রদান')); ?>
                </li>
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item11_color', '#dc3545')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item11_text', 'মেহেদী কোণ প্যাকেজিং')); ?>
                </li>
            </ul>
        </div>
    </section>
   
    <section class="Corse_container" id="course-section-3" style="--section-color: <?php echo get_theme_mod('section3_color', '#6f42c1'); ?>">
        <h3 style="color: var(--section-color);"><?php echo esc_html(get_theme_mod('section3_title', '📦 প্যাকেজিং ও সার্টিফিকেশন')); ?></h3>
        <div class="Corse_dtail">
            <ul class="Corse_dtail_left">
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item12_color', '#6f42c1')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item12_text', 'প্যাকেজিং ডিজাইন ও লেবেলিং')); ?>
                </li>
            </ul>
            <ul class="Corse_dtail_right">
                <li style="color: <?php echo esc_attr(get_theme_mod('course_item13_color', '#6f42c1')); ?>;">
                    <?php echo esc_html(get_theme_mod('course_item13_text', 'বিক্রির জন্য প্রস্তুতি')); ?>
                </li>
            </ul>
        </div>
    </section>
    
    <a href="#order" class="btn btn-primary btn-lg" id="order-button-top">এখানে অর্ডার করুন</a><br>
    
    <h2 class="title mt-3"><?php echo esc_html(get_theme_mod('review_heading', 'কাস্টমার রিভিউ')); ?></h2>
</div>

<!-- Customer Reviews Slider -->
<div class="review-slider" id="review-section">
    <div class="container">
        <div class="swiper reviewSwiper">
            <div class="swiper-wrapper">
                <?php
                $reviews = new WP_Query(array(
                    'post_type' => 'course_review',
                    'posts_per_page' => 10,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($reviews->have_posts()) :
                    while ($reviews->have_posts()) : $reviews->the_post();
                        $customer_name = get_post_meta(get_the_ID(), '_customer_name', true);
                        $customer_rating = get_post_meta(get_the_ID(), '_customer_rating', true);
                ?>
                <div class="swiper-slide">
                    <div class="review-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('class' => 'review-image', 'alt' => get_the_title())); ?>
                        <?php endif; ?>
                        
                        <?php if ($customer_name || $customer_rating || get_the_content()) : ?>
                        <div class="review-content">
                            <h5 class="customer-name"><?php echo esc_html($customer_name); ?></h5>
                            
                            <?php if ($customer_rating) : ?>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <i class="fa<?php echo $i <= $customer_rating ? 's' : 'r'; ?> fa-star text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (get_the_content()) : ?>
                                <div class="review-text">
                                    <?php the_content(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-content">
                            <p style="color: white;">কোন রিভিউ পাওয়া যায়নি। অনুগ্রহ করে অ্যাডমিন প্যানেল থেকে Customer Reviews যোগ করুন।</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <a href="#order" class="btn btn-primary btn-lg" id="order-button-middle">এখানে অর্ডার করুন</a><br>
    </div>
</div>

<!-- FAQ Section -->
<div class="faq" id="faq-section">
    <div class="container">
        <h2 class="title mt-5"><?php echo esc_html(get_theme_mod('faq_heading', 'প্রশ্ন ও উত্তর')); ?></h2>
    </div>
</div>

<div class="faq_section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="accordion accordion-flush" id="accordionExample">
                    <?php
                    $faqs = new WP_Query(array(
                        'post_type' => 'course_faq',
                        'posts_per_page' => 10,
                        'orderby' => 'menu_order',
                        'order' => 'ASC'
                    ));
                    
                    if ($faqs->have_posts()) :
                        $i = 1;
                        while ($faqs->have_posts()) : $faqs->the_post();
                            $i++;
                    ?>
                    <div class="accordion-item shadow-sm">
                        <h2 class="accordion-header" id="heading<?php echo $i; ?>">
                            <button class="accordion-button bg-transparent fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="false" aria-controls="collapse<?php echo $i; ?>">
                                <?php the_title(); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else:
                    ?>
                    <div class="col-12">
                        <p style="color: white;">কোন FAQ পাওয়া যায়নি। অনুগ্রহ করে অ্যাডমিন প্যানেল থেকে FAQs যোগ করুন।</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Section -->
<div class="order_section" id="order">
    <div class="container">
        <form id="wc-order-form" method="POST" action="">
            <?php wp_nonce_field('alhadiya_nonce', 'nonce'); ?>
            <input type="hidden" name="submit_wc_order" value="1">
            
            <h2 class="title" style="font-size:24px">প্যাকেজটি সিলেক্ট করুনঃ</h2><br>
            
            <div class="product_desk">
                <div id="msg"></div>
                <div class="row mt-2 product-row">
                    <?php
                    if (class_exists('WooCommerce')) {
                        $products = wc_get_products(array(
                            'status' => 'publish',
                            'limit' => -1,
                            'orderby' => 'menu_order',
                            'order' => 'ASC'
                        ));
                        
                        if (!empty($products)) :
                            $first = true;
                            foreach ($products as $product) :
                                $product_id = $product->get_id();
                                $regular_price = $product->get_regular_price();
                                $sale_price = $product->get_sale_price();
                                $price = $product->get_price();
                    ?>
                    <div class="col-lg-4 col-md-6 col-6">
                        <label class="labels">
                            <input type="radio" class="products_id" name="product_id" value="<?php echo esc_attr($product_id); ?>" id="pro_id<?php echo esc_attr($product_id); ?>" <?php echo $first ? 'checked' : ''; ?> required>
                            <div class="products_dets">
                                <span class="checkmark"><i class="fa-solid fa-check"></i></span>
                                <div class="img_preview">
                                    <?php echo $product->get_image('medium', array('class' => 'img-fluid product-image')); ?>
                                </div>
                                <div class="product_description">
                                    <h2><?php echo esc_html($product->get_name()); ?></h2>
                                    <div class="price">
                                        <?php if ($sale_price && $regular_price != $sale_price) : ?>
                                            <p><del>৳ <?php echo esc_html($regular_price); ?></del></p>
                                            <p class="alex-mt"><strong style="color: #dd0055;">৳ <?php echo esc_html($sale_price); ?></strong></p>
                                        <?php else : ?>
                                            <p class="alex-mt"><strong style="color: #dd0055;">৳ <?php echo esc_html($price); ?></strong></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <?php 
                                $first = false;
                            endforeach;
                        else:
                    ?>
                    <div class="col-12">
                        <p style="color: white;">কোন প্রোডাক্ট পাওয়া যায়নি। অনুগ্রহ করে WooCommerce থেকে প্রোডাক্ট যোগ করুন।</p>
                    </div>
                    <?php 
                        endif;
                    } else {
                    ?>
                    <div class="col-12">
                        <p style="color: white;">WooCommerce প্লাগইন সক্রিয় নেই। অনুগ্রহ করে WooCommerce ইনস্টল এবং সক্রিয় করুন।</p>
                    </div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="order_details">
                <div class="row">
                    <h3 class="order_title">Billing & Shipping</h3>
                    <div class="col-lg-6 order-lg-1">
                        <input type="text" name="billing_first_name" class="form-control mb-3" placeholder="আপনার নাম লিখুনঃ" required>
                        <input type="tel" name="billing_phone" class="form-control mb-3" placeholder="আপনার মোবাইল নাম্বার লিখুনঃ" required>
                        <textarea name="billing_address_1" class="form-control mb-3" placeholder="আপনার ঠিকানা লিখুনঃ" rows="3" required></textarea>
                        
                        <!-- Delivery Options -->
                        <div class="delivery-options">
                            <label class="delivery-options-label">🚚 ডেলিভারি অপশন:</label>
                            <div class="delivery-options-container">
                                <div class="delivery-option">
                                    <input type="radio" id="dhaka" name="delivery_zone" value="1" checked required>
                                    <label for="dhaka"><?php echo esc_html(get_theme_mod('dhaka_delivery_title', 'ঢাকার মধ্যে ডেলিভারি')); ?> - ৳<?php echo esc_html(get_theme_mod('dhaka_delivery_charge', 0)); ?></label>
                                </div>
                                <div class="delivery-option">
                                    <input type="radio" id="outside_dhaka" name="delivery_zone" value="2" required>
                                    <label for="outside_dhaka"><?php echo esc_html(get_theme_mod('outside_dhaka_delivery_title', 'ঢাকার বাইরে ডেলিভারি')); ?> - ৳<?php echo esc_html(get_theme_mod('outside_dhaka_delivery_charge', 0)); ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods - Updated Order: Pay Later first as default -->
                        <div class="payment-methods">
                            <label class="payment-methods-label">💳 পেমেন্ট পদ্ধতি:</label>
                            <div class="payment-methods-container">
                                <div class="payment-method">
                                    <input type="radio" id="pay_later" name="payment_method" value="pay_later" checked required>
                                    <label for="pay_later">
                                        <span class="payment-icon pay-later-icon">⏰</span>
                                        <?php if (get_theme_mod('show_payment_text', true)) : ?>
                                            <span class="payment-text">Pay Later</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="bkash" name="payment_method" value="bkash" required>
                                    <label for="bkash">
                                        <?php 
                                        $bkash_icon = get_theme_mod('bkash_icon');
                                        if ($bkash_icon) {
                                            echo '<img src="' . esc_url(wp_get_attachment_url($bkash_icon)) . '" alt="bKash" class="payment-icon-img">';
                                        } else {
                                            echo '<span class="payment-icon bkash-icon">📱</span>';
                                        }
                                        ?>
                                        <?php if (get_theme_mod('show_payment_text', true)) : ?>
                                            <span class="payment-text">bKash</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="nagad" name="payment_method" value="nagad" required>
                                    <label for="nagad">
                                        <?php 
                                        $nagad_icon = get_theme_mod('nagad_icon');
                                        if ($nagad_icon) {
                                            echo '<img src="' . esc_url(wp_get_attachment_url($nagad_icon)) . '" alt="Nagad" class="payment-icon-img">';
                                        } else {
                                            echo '<span class="payment-icon nagad-icon">💰</span>';
                                        }
                                        ?>
                                        <?php if (get_theme_mod('show_payment_text', true)) : ?>
                                            <span class="payment-text">Nagad</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="rocket" name="payment_method" value="rocket" required>
                                    <label for="rocket">
                                        <?php 
                                        $rocket_icon = get_theme_mod('rocket_icon');
                                        if ($rocket_icon) {
                                            echo '<img src="' . esc_url(wp_get_attachment_url($rocket_icon)) . '" alt="Rocket" class="payment-icon-img">';
                                        } else {
                                            echo '<span class="payment-icon rocket-icon">🚀</span>';
                                        }
                                        ?>
                                        <?php if (get_theme_mod('show_payment_text', true)) : ?>
                                            <span class="payment-text">Rocket</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div class="payment-instructions" id="payment-instructions" style="display: block;">
                            <div class="payment-instruction" id="pay_later-instruction" style="display: block;">
                                <div class="instruction-header">
                                    <h4 style="color: #6c757d;">⏰ Pay Later</h4>
                                </div>
                                <p>Pay Later - Pay at delivery (ডেলিভারির সময় টাকা দিবেন)</p>
                            </div>

                            <div class="payment-instruction" id="bkash-instruction">
                                <div class="instruction-header">
                                    <h4 style="color: <?php echo esc_attr(get_theme_mod('bkash_color', '#e2136e')); ?>;">📱 bKash পেমেন্ট</h4>
                                    <div class="number-copy">
                                        <span id="bkash-number"><?php echo esc_html(get_theme_mod('bkash_number', '01975669946')); ?></span>
                                        <button type="button" class="copy-btn" onclick="copyNumber('bkash-number')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                                <p><?php echo esc_html(get_theme_mod('bkash_instruction', 'আর এই নাম্বারে বিকাশ সেন্ডমানি করে ফর্ম এ লিখুন (Personal)')); ?></p>
                                <input type="text" name="transaction_number" class="form-control transaction-input" placeholder="ট্রানজেকশন নাম্বার লিখুন">
                            </div>

                            <div class="payment-instruction" id="nagad-instruction">
                                <div class="instruction-header">
                                    <h4 style="color: <?php echo esc_attr(get_theme_mod('nagad_color', '#f47920')); ?>;">💰 Nagad পেমেন্ট</h4>
                                    <div class="number-copy">
                                        <span id="nagad-number"><?php echo esc_html(get_theme_mod('nagad_number', '01737146996')); ?></span>
                                        <button type="button" class="copy-btn" onclick="copyNumber('nagad-number')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                                <p><?php echo esc_html(get_theme_mod('nagad_instruction', 'আর এই নাম্বারে নগদে সেন্ডমানি করে ফর্ম এ লিখুন (Personal)')); ?></p>
                                <input type="text" name="transaction_number" class="form-control transaction-input" placeholder="ট্রানজেকশন নাম্বার লিখুন">
                            </div>

                            <div class="payment-instruction" id="rocket-instruction">
                                <div class="instruction-header">
                                    <h4 style="color: <?php echo esc_attr(get_theme_mod('rocket_color', '#8b1538')); ?>;">🚀 Rocket পেমেন্ট</h4>
                                    <div class="number-copy">
                                        <span id="rocket-number"><?php echo esc_html(get_theme_mod('rocket_number', '01737146996')); ?></span>
                                        <button type="button" class="copy-btn" onclick="copyNumber('rocket-number')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                                <p><?php echo esc_html(get_theme_mod('rocket_instruction', 'আর এই নাম্বারে রকেটে সেন্ডমানি করে ফর্ম এ লিখুন (Personal)')); ?></p>
                                <input type="text" name="transaction_number" class="form-control transaction-input" placeholder="ট্রানজেকশন নাম্বার লিখুন">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-2">
                        <div class="invoice-bills" style="display:none">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="w-60">Description</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="name_cart"></td>
                                            <td id="price_cart">৳<span class="price_cart"></span></td>
                                            <td>1</td>
                                            <td>৳<span class="price_cart"></span></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="1"></td>
                                            <td colspan="2"><b>Subtotal</b></td>
                                            <td>৳<span class="price_cart"></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="1"></td>
                                            <td colspan="2"><b>Delivery Charge</b></td>
                                            <td>৳<span id="delivery_charge">0</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="1"></td>
                                            <td colspan="2"><b>Total</b></td>
                                            <td><b>৳<span id="total_amount"></span></b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Order Button Moved Here - Below Payment Instructions -->
                    <div class="col-12 order-lg-3">
                        <div class="order-button-section" style="margin-top: 25px; text-align: center;">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-order-btn">🛒 অর্ডার করুন</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Floating Contact Buttons -->
<a href="tel:<?php echo esc_attr(get_theme_mod('phone_number', '+8801737146996')); ?>" class="callbtnlaptop">
    <i class="fas fa-phone"></i>
</a>

<a href="https://wa.me/88<?php echo esc_attr(get_theme_mod('whatsapp_number', '01737146996')); ?>" target="_blank" class="float">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Enhanced Invoice Modal -->
<div class="invoice-modal" id="invoice-modal">
    <div class="invoice-content">
        <div class="invoice-header">
            <button class="invoice-close" onclick="closeInvoiceModal()">&times;</button>
            <div class="invoice-success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="invoice-title">অর্ডার সফল!</h2>
            <p class="invoice-subtitle">আপনার অর্ডার সফলভাবে সম্পন্ন হয়েছে</p>
        </div>
        <div class="invoice-body">
            <div class="invoice-section">
                <h4>অর্ডার বিবরণ</h4>
                <div class="invoice-details">
                    <div class="invoice-row">
                        <span>অর্ডার নাম্বার:</span>
                        <span id="invoice-order-id">-</span>
                    </div>
                    <div class="invoice-row">
                        <span>কাস্টমার নাম:</span>
                        <span id="invoice-customer-name">-</span>
                    </div>
                    <div class="invoice-row">
                        <span>মোবাইল নাম্বার:</span>
                        <span id="invoice-customer-phone">-</span>
                    </div>
                    <div class="invoice-row">
                        <span>পেমেন্ট পদ্ধতি:</span>
                        <span id="invoice-payment-method">-</span>
                    </div>
                    <div class="invoice-row">
                        <span>ট্রানজেকশন নাম্বার:</span>
                        <span id="invoice-transaction-number">-</span>
                    </div>
                    <div class="invoice-row">
                        <span>মোট পরিমাণ:</span>
                        <span id="invoice-total-amount">-</span>
                    </div>
                </div>
            </div>
            
            <div class="invoice-actions">
                <button class="invoice-btn invoice-btn-primary" onclick="closeInvoiceModal()">
                    <i class="fas fa-check"></i> ঠিক আছে
                </button>
                <a href="tel:<?php echo esc_attr(get_theme_mod('phone_number', '+8801737146996')); ?>" class="invoice-btn invoice-btn-success">
                    <i class="fas fa-phone"></i> কল করুন
                </a>
                <a href="https://wa.me/88<?php echo esc_attr(get_theme_mod('whatsapp_number', '1737146996')); ?>" target="_blank" class="invoice-btn invoice-btn-secondary">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Load YouTube IFrame Player API
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
var videoPlayStartTime;
var videoCurrentTime = 0;
var videoDuration = 0;
var videoTrackingEnabled = <?php echo get_theme_mod('enable_video_tracking', true) ? 'true' : 'false'; ?>;

// Global tracking settings
var deviceTrackingEnabled = <?php echo get_theme_mod('enable_device_tracking', true) ? 'true' : 'false'; ?>;
var customEventsTrackingEnabled = <?php echo get_theme_mod('enable_custom_events_tracking', true) ? 'true' : 'false'; ?>;
var deviceDetailsTrackingEnabled = <?php echo get_theme_mod('enable_device_details_tracking', true) ? 'true' : 'false'; ?>;
var timeSpentTrackingEnabled = <?php echo get_theme_mod('enable_time_spent_tracking', true) ? 'true' : 'false'; ?>;
var serverTrackingEnabled = <?php echo get_theme_mod('enable_server_tracking', true) ? 'true' : 'false'; ?>;

// Server-side tracking IDs
var facebookPixelId = '<?php echo esc_js(get_theme_mod('facebook_pixel_id', '')); ?>';
var ga4MeasurementId = '<?php echo esc_js(get_theme_mod('ga4_measurement_id', '')); ?>';
var microsoftClarityId = '<?php echo esc_js(get_theme_mod('microsoft_clarity_id', '')); ?>';
var gtmContainerId = '<?php echo esc_js(get_theme_mod('gtm_container_id', '')); ?>';

// Session ID from PHP
var phpSessionId = '<?php echo $session_id ?: ''; ?>';

console.log('Tracking Settings:', {
    deviceTrackingEnabled,
    customEventsTrackingEnabled,
    deviceDetailsTrackingEnabled,
    timeSpentTrackingEnabled,
    serverTrackingEnabled
});
console.log('Server Tracking IDs:', {
    facebookPixelId,
    ga4MeasurementId,
    microsoftClarityId,
    gtmContainerId
});
console.log('PHP Session ID:', phpSessionId);

// ========================================
// SERVER-SIDE TRACKING FUNCTIONS
// ========================================

// Server-side event tracking
function trackServerEvent(eventName, eventData = {}, eventValue = '') {
    if (!serverTrackingEnabled || typeof ajax_object === 'undefined') {
        console.log('Server tracking disabled or ajax_object not available');
        return;
    }
    
    console.log('Tracking server event:', { eventName, eventData, eventValue });
    
    // Send to server
    jQuery.post(ajax_object.ajax_url, {
        action: 'alhadiya_server_event',
        event_name: eventName,
        event_data: eventData,
        event_value: eventValue,
        server_event_nonce: ajax_object.server_event_nonce
    }).done(function(response) {
        if (response.success) {
            console.log('Server event tracked successfully:', response.data);
        } else {
            console.error('Failed to track server event:', response.data);
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX failed for server event:', error);
    });
}

// Google Tag Manager Data Layer
function pushToDataLayer(eventName, eventData = {}) {
    if (typeof dataLayer !== 'undefined') {
        dataLayer.push({
            'event': eventName,
            'event_data': eventData,
            'session_id': phpSessionId,
            'timestamp': new Date().toISOString()
        });
        console.log('Pushed to DataLayer:', eventName, eventData);
    }
}

// Microsoft Clarity Integration
function trackClarityEvent(eventName, eventData = {}) {
    if (typeof clarity !== 'undefined') {
        clarity('event', eventName, eventData);
        console.log('Clarity event tracked:', eventName, eventData);
    }
}

// Facebook Pixel Integration (if available)
function trackFacebookEvent(eventName, eventData = {}) {
    if (typeof fbq !== 'undefined' && facebookPixelId) {
        fbq('track', eventName, eventData);
        console.log('Facebook event tracked:', eventName, eventData);
    }
}

// Google Analytics 4 Integration (if available)
function trackGA4Event(eventName, eventData = {}) {
    if (typeof gtag !== 'undefined' && ga4MeasurementId) {
        gtag('event', eventName, {
            ...eventData,
            'session_id': phpSessionId,
            'custom_parameter': 'server_tracked'
        });
        console.log('GA4 event tracked:', eventName, eventData);
    }
}

// Enhanced Facebook Pixel event mapping
function getFacebookEventName(eventName) {
    const facebookEventMapping = {
        'page_view': 'PageView',
        'phone_click': 'Contact',
        'whatsapp_click': 'Contact',
        'product_select': 'ViewContent',
        'begin_checkout': 'InitiateCheckout',
        'purchase_complete': 'Purchase',
        'form_start': 'Lead',
        'form_submit': 'CompleteRegistration',
        'video_play': 'ViewContent',
        'faq_click': 'ViewContent',
        'copy_text': 'Contact',
        'delivery_option_click': 'AddToCart',
        'payment_method_click': 'AddPaymentInfo',
        'order_button_click': 'InitiateCheckout'
    };
    
    return facebookEventMapping[eventName] || 'CustomEvent';
}

// Enhanced Facebook Pixel tracking
function trackFacebookEvent(eventName, eventData = {}) {
    if (typeof fbq !== 'undefined' && facebookPixelId) {
        const fbEventName = getFacebookEventName(eventName);
        
        // Enhanced event data for Facebook
        const fbEventData = {
            content_name: eventData.event_name || eventName,
            content_category: 'User Interaction',
            custom_parameter: eventData,
            event_source_url: window.location.href,
            ...eventData
        };
        
        // Add value for purchase events
        if (['Purchase', 'InitiateCheckout', 'AddToCart'].includes(fbEventName)) {
            fbEventData.value = eventData.total_value || eventData.price || 0;
            fbEventData.currency = 'BDT';
        }
        
        fbq('track', fbEventName, fbEventData);
        fbq('trackCustom', eventName, eventData); // Also track as custom event
        
        console.log('Facebook Event Tracked:', fbEventName, fbEventData);
    }
}

// Universal tracking function
function trackUniversalEvent(eventName, eventData = {}, eventValue = '') {
    // Server-side tracking
    trackServerEvent(eventName, eventData, eventValue);
    
    // GTM Data Layer
    pushToDataLayer(eventName, eventData);
    
    // Microsoft Clarity
    trackClarityEvent(eventName, eventData);
    
    // Enhanced Facebook Pixel
    trackFacebookEvent(eventName, eventData);
    
    // Google Analytics 4
    trackGA4Event(eventName, eventData);
}

// Helper functions (always available)
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function getSessionId() {
    // First try PHP session ID, then cookie
    if (phpSessionId && phpSessionId !== '') {
        console.log('Using PHP session ID:', phpSessionId);
        return phpSessionId;
    }
    
    const sessionId = getCookie('device_session');
    console.log('All cookies:', document.cookie);
    console.log('Looking for device_session cookie...');
    console.log('Session ID found:', sessionId);
    return sessionId;
}

function trackCustomEvent(eventType, eventName, eventValue = '') {
    if (!customEventsTrackingEnabled || typeof ajax_object === 'undefined') {
        console.log('Event tracking disabled or ajax_object not available');
        return;
    }
    const sessionId = getSessionId();
    if (!sessionId) {
        console.log('No session ID found');
        return;
    }

    console.log('Tracking event:', { eventType, eventName, eventValue });

    // Track to existing system
    jQuery.post(ajax_object.ajax_url, {
        action: 'track_custom_event',
        session_id: sessionId,
        event_type: eventType,
        event_name: eventName,
        event_value: eventValue,
        nonce: ajax_object.event_nonce
    }).done(function(response) {
        console.log('Event tracked successfully:', response);
    }).fail(function(xhr, status, error) {
        console.error('Failed to track event:', error);
    });
    
    // Universal tracking
    trackUniversalEvent(eventType, {
        event_name: eventName,
        event_value: eventValue,
        session_id: sessionId
    }, eventValue);
}

// ========================================
// ENHANCED DEVICE & BROWSER INFO TRACKING
// ========================================

// Get detailed browser information
function getBrowserInfo() {
    const userAgent = navigator.userAgent;
    let browserName = 'Unknown';
    let browserVersion = 'Unknown';
    
    if (userAgent.indexOf('Chrome') > -1) {
        browserName = 'Chrome';
        browserVersion = userAgent.match(/Chrome\/([0-9.]+)/)?.[1] || 'Unknown';
    } else if (userAgent.indexOf('Firefox') > -1) {
        browserName = 'Firefox';
        browserVersion = userAgent.match(/Firefox\/([0-9.]+)/)?.[1] || 'Unknown';
    } else if (userAgent.indexOf('Safari') > -1) {
        browserName = 'Safari';
        browserVersion = userAgent.match(/Version\/([0-9.]+)/)?.[1] || 'Unknown';
    } else if (userAgent.indexOf('Edge') > -1) {
        browserName = 'Edge';
        browserVersion = userAgent.match(/Edge\/([0-9.]+)/)?.[1] || 'Unknown';
    } else if (userAgent.indexOf('Opera') > -1) {
        browserName = 'Opera';
        browserVersion = userAgent.match(/Opera\/([0-9.]+)/)?.[1] || 'Unknown';
    }
    
    return { name: browserName, version: browserVersion };
}

// Get device type
function getDeviceType() {
    const userAgent = navigator.userAgent;
    if (/tablet|ipad|playbook|silk/i.test(userAgent)) {
        return 'tablet';
    }
    if (/mobile|iphone|ipod|android|blackberry|opera|mini|windows\sce|palm|smartphone|iemobile/i.test(userAgent)) {
        return 'mobile';
    }
    return 'desktop';
}

// Get device performance info
function getDevicePerformance() {
    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    
    return {
        memory: navigator.deviceMemory || 'unknown',
        cores: navigator.hardwareConcurrency || 'unknown',
        connection_type: connection?.effectiveType || 'unknown',
        connection_speed: connection?.downlink || 'unknown',
        battery_level: 'checking...',
        online: navigator.onLine
    };
}

// Check battery status
async function getBatteryInfo() {
    try {
        if ('getBattery' in navigator) {
            const battery = await navigator.getBattery();
            return {
                level: Math.round(battery.level * 100),
                charging: battery.charging,
                charging_time: battery.chargingTime,
                discharging_time: battery.dischargingTime
            };
        }
    } catch (error) {
        console.log('Battery API not available');
    }
    return { level: 'unknown', charging: 'unknown' };
}

// ========================================
// AUTOMATIC EVENT TRACKING
// ========================================

// Track page load events with enhanced details
function trackPageLoadEvents() {
    const browserInfo = getBrowserInfo();
    const devicePerf = getDevicePerformance();
    const loadTime = performance.now();
    
    trackUniversalEvent('page_view', {
        page_title: document.title,
        page_url: window.location.href,
        page_path: window.location.pathname,
        referrer: document.referrer,
        browser: browserInfo.name,
        browser_version: browserInfo.version,
        device_type: getDeviceType(),
        timestamp: new Date().toISOString()
    });
    
    trackUniversalEvent('page_load', {
        load_time: loadTime.toFixed(2),
        user_agent: navigator.userAgent,
        screen_resolution: `${screen.width}x${screen.height}`,
        viewport_size: `${window.innerWidth}x${window.innerHeight}`,
        device_memory: devicePerf.memory,
        cpu_cores: devicePerf.cores,
        connection_type: devicePerf.connection_type,
        connection_speed: devicePerf.connection_speed,
        online_status: devicePerf.online
    });
    
    // Track battery info if available
    getBatteryInfo().then(batteryInfo => {
        if (batteryInfo.level !== 'unknown') {
            trackUniversalEvent('device_status', {
                battery_level: batteryInfo.level,
                battery_charging: batteryInfo.charging,
                memory_usage: getMemoryUsage()
            });
            
            // Track low battery
            if (batteryInfo.level < 20) {
                trackUniversalEvent('battery_low', {
                    battery_level: batteryInfo.level,
                    timestamp: new Date().toISOString()
                });
            }
        }
    });
}

// Get memory usage estimate
function getMemoryUsage() {
    if ('memory' in performance) {
        return {
            used: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024),
            total: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024),
            limit: Math.round(performance.memory.jsHeapSizeLimit / 1024 / 1024)
        };
    }
    return 'unknown';
}

// Track scroll events
function trackScrollEvents() {
    let lastScrollDepth = 0;
    let scrollTimeout;
    
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const scrollY = window.scrollY;
            const docHeight = document.documentElement.scrollHeight;
            const windowHeight = window.innerHeight;
            const currentScrollDepth = Math.min(100, (scrollY / (docHeight - windowHeight)) * 100);

            if (Math.abs(currentScrollDepth - lastScrollDepth) >= 10) {
                trackUniversalEvent('scroll_depth', {
                    scroll_percentage: currentScrollDepth.toFixed(2),
                    scroll_position: scrollY,
                    page_height: docHeight
                }, currentScrollDepth.toFixed(2));
                lastScrollDepth = currentScrollDepth;
            }
        }, 100);
    });
}

// Track click events
function trackClickEvents() {
    document.addEventListener('click', function(e) {
        const target = e.target;
        const tagName = target.tagName.toLowerCase();
        const className = target.className || '';
        const id = target.id || '';
        const text = target.textContent?.trim() || '';
        const href = target.href || '';
        
        // Determine event type
        let eventType = 'click';
        let eventData = {
            element_type: tagName,
            element_class: className,
            element_id: id,
            element_text: text.substring(0, 100),
            click_position: `${e.clientX},${e.clientY}`,
            page_url: window.location.href
        };
        
        // Specific event types
        if (target.matches('.btn, button, a[href="#order"]')) {
            eventType = 'button_click';
            eventData.button_type = 'primary';
        } else if (target.matches('a')) {
            eventType = 'link_click';
            eventData.link_url = href;
        } else if (target.matches('input, select, textarea')) {
            eventType = 'form_interaction';
            eventData.field_type = target.type || 'text';
        }
        
        trackUniversalEvent(eventType, eventData);
    });
}

// Track form events
function trackFormEvents() {
    // Enhanced form submission tracking
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const formId = form.id || '';
        const formAction = form.action || '';
        
        trackUniversalEvent('form_submit', {
            form_id: formId,
            form_action: formAction,
            form_method: form.method || 'POST',
            page_url: window.location.href,
            timestamp: new Date().toISOString(),
            browser: getBrowserInfo().name,
            device_type: getDeviceType()
        });
        
        trackCustomEvent('form_interaction', 'Form Submitted', formId || 'unnamed-form');
    });
    
    // Form start tracking (when user first interacts with form)
    let formStartTracked = false;
    document.addEventListener('focus', function(e) {
        if (e.target.matches('input, select, textarea')) {
            // Track form start on first field focus
            if (!formStartTracked) {
                formStartTracked = true;
                trackUniversalEvent('form_start', {
                    form_id: e.target.form?.id || '',
                    first_field: e.target.name || e.target.id || 'unknown',
                    timestamp: new Date().toISOString(),
                    browser: getBrowserInfo().name,
                    device_type: getDeviceType()
                });
                
                trackCustomEvent('form_interaction', 'Form Started', 'User began filling form');
            }
            
            // Track individual field focus
            trackUniversalEvent('input_focus', {
                field_type: e.target.type || 'text',
                field_name: e.target.name || '',
                field_id: e.target.id || '',
                form_id: e.target.form?.id || '',
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                device_type: getDeviceType()
            });
            
            trackCustomEvent('form_field_focus', 'Form Field Focused', e.target.name || e.target.id || 'unknown-field');
        }
    }, true);
    
    // Enhanced form field change tracking
    document.addEventListener('change', function(e) {
        if (e.target.matches('input, select, textarea')) {
            trackUniversalEvent('form_field_change', {
                field_type: e.target.type || 'text',
                field_name: e.target.name || '',
                field_id: e.target.id || '',
                form_id: e.target.form?.id || '',
                value_length: e.target.value ? e.target.value.length : 0,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                device_type: getDeviceType()
            });
            
            trackCustomEvent('form_field_change', 'Form Field Changed', `${e.target.name || e.target.id}: ${e.target.value.substring(0, 50)}`);
        }
    });
    
    // Input typing detection
    let typingTimers = new Map();
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[type="text"], input[type="tel"], input[type="email"], textarea')) {
            const fieldKey = e.target.name || e.target.id || 'unknown';
            
            // Clear previous timer
            if (typingTimers.has(fieldKey)) {
                clearTimeout(typingTimers.get(fieldKey));
            }
            
            // Set new timer
            const timer = setTimeout(() => {
                trackUniversalEvent('input_typing', {
                    field_name: e.target.name || '',
                    field_id: e.target.id || '',
                    field_type: e.target.type || 'text',
                    value_length: e.target.value.length,
                    timestamp: new Date().toISOString()
                });
                
                trackCustomEvent('form_interaction', 'User Typing', `${fieldKey}: ${e.target.value.length} chars`);
                typingTimers.delete(fieldKey);
            }, 1000); // Track after 1 second of no typing
            
            typingTimers.set(fieldKey, timer);
        }
    });
}

// Track section visibility
function trackSectionVisibility() {
    const sections = [
        { id: 'course-section-1', name: 'অর্গানিক মেহেদী তৈরির সহজ উপায়' },
        { id: 'course-section-2', name: 'মেহেদী রঙ বাড়ানোর গোপন টিপস' },
        { id: 'course-section-3', name: 'প্যাকেজিং ও সার্টিফিকেশন' }
    ];
    
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const sectionId = entry.target.id;
            const sectionName = sections.find(s => s.id === sectionId)?.name || sectionId;
            
            if (entry.isIntersecting) {
                trackUniversalEvent('section_view', {
                    section_id: sectionId,
                    section_name: sectionName,
                    visibility_percentage: Math.round(entry.intersectionRatio * 100),
                    page_url: window.location.href
                });
            }
        });
    }, { threshold: 0.5 });
    
    sections.forEach(section => {
        const element = document.getElementById(section.id);
        if (element) {
            sectionObserver.observe(element);
        }
    });
}

// Track time spent on page
function trackTimeSpent() {
    const startTime = Date.now();
    
    // Track every 30 seconds
    setInterval(() => {
        const timeSpent = Math.floor((Date.now() - startTime) / 1000);
        if (timeSpent % 30 === 0 && timeSpent > 0) {
            trackUniversalEvent('time_spent', {
                time_spent_seconds: timeSpent,
                page_url: window.location.href
            }, timeSpent.toString());
        }
    }, 1000);
    
    // Track on page unload
    window.addEventListener('beforeunload', () => {
        const timeSpent = Math.floor((Date.now() - startTime) / 1000);
        if (timeSpent > 10) {
            trackUniversalEvent('page_exit', {
                time_spent_seconds: timeSpent,
                page_url: window.location.href
            }, timeSpent.toString());
        }
    });
}

// ========================================
// INITIALIZATION
// ========================================

// Global function for device tracking initialization
function initializeDeviceTracking() {
    console.log('Initializing device tracking...');
    
    // Collect device details
    var deviceDetails = {
        screen_size: window.screen.width + 'x' + window.screen.height,
        language: navigator.language || '',
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || '',
        connection_type: (navigator.connection && navigator.connection.effectiveType) ? navigator.connection.effectiveType : '',
        battery_level: null,
        memory_info: navigator.deviceMemory || '',
        cpu_cores: navigator.hardwareConcurrency || '',
        touchscreen_detected: ('ontouchstart' in window) ? 1 : 0
    };
    
    // Get battery level if available
    if (navigator.getBattery) {
        navigator.getBattery().then(function(battery) {
            deviceDetails.battery_level = battery.level;
            sendDeviceDetails(deviceDetails);
        }).catch(function() {
            sendDeviceDetails(deviceDetails);
        });
    } else {
        sendDeviceDetails(deviceDetails);
    }
}

function sendDeviceDetails(deviceDetails) {
    if (typeof ajax_object === 'undefined') {
        console.log('ajax_object not available for device details');
        return;
    }
    
    // Send device details to server via AJAX
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'alhadiya_server_event',
            event_name: 'device_details',
            event_data: deviceDetails,
            event_value: '',
            server_event_nonce: ajax_object.server_event_nonce
        },
        success: function(response) {
            if (response.success) {
                console.log('Device details sent successfully:', response.data);
            } else {
                console.error('Failed to send device details:', response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to send device details:', error);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    console.log('ajax_object available:', typeof ajax_object !== 'undefined');
    if (typeof ajax_object !== 'undefined') {
        console.log('ajax_object:', ajax_object);
    }
    
    // Check for session ID immediately
    const initialSessionId = getSessionId();
    console.log('Initial session ID check:', initialSessionId);
    
    // Initialize automatic tracking
    if (serverTrackingEnabled) {
        console.log('Initializing server-side tracking...');
        
        // Track page load events
        trackPageLoadEvents();
        
        // Track scroll events
        trackScrollEvents();
        
        // Track click events
        trackClickEvents();
        
        // Track form events
        trackFormEvents();
        
        // Track section visibility
        trackSectionVisibility();
        
        // Track time spent
        trackTimeSpent();
    }
    
    // Initialize Swiper with enhanced autoplay
    const swiperContainer = document.querySelector('.reviewSwiper');
    if (swiperContainer) {
        const slidesCount = swiperContainer.querySelectorAll('.swiper-slide').length;
        console.log('Swiper slides count:', slidesCount);
        
        const swiper = new Swiper('.reviewSwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: slidesCount > 1, // Only enable loop if there are multiple slides
            autoplay: slidesCount > 1 ? {
                delay: 2500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
        
        // Store swiper instance globally for event tracking
        window.reviewSwiper = swiper;
    }
    
    // Initialize tracking based on settings
    if (deviceTrackingEnabled) {
        console.log('Device tracking enabled, initializing...');
        initializeDeviceTracking();
        
        if (timeSpentTrackingEnabled) {
            console.log('Time spent tracking enabled');
        }
        
        if (deviceDetailsTrackingEnabled) {
            console.log('Device details tracking enabled');
        }
    } else {
        console.log('Device tracking disabled');
    }
    
    // Track custom events if enabled
    if (customEventsTrackingEnabled) {
        console.log('Custom events tracking enabled');
        
        // Track Swiper interactions
        if (typeof window.reviewSwiper !== 'undefined' && window.reviewSwiper) {
            window.reviewSwiper.on('slideChange', function () {
                trackUniversalEvent('swiper_slide_change', {
                    slide_index: this.realIndex + 1,
                    total_slides: this.slides.length
                });
            });
        }
        
        // Track FAQ accordion clicks with enhanced details
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', function() {
                const faqTitle = this.textContent.trim();
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                const browserInfo = getBrowserInfo();
                
                trackUniversalEvent('faq_click', {
                    faq_title: faqTitle,
                    action: isExpanded ? 'close' : 'open',
                    timestamp: new Date().toISOString(),
                    browser: browserInfo.name,
                    browser_version: browserInfo.version,
                    device_type: getDeviceType(),
                    screen_size: `${screen.width}x${screen.height}`,
                    viewport_size: `${window.innerWidth}x${window.innerHeight}`
                });
                
                // Track custom event for existing system
                trackCustomEvent('faq_toggle', `FAQ Clicked: ${faqTitle}`, `Action: ${isExpanded ? 'close' : 'open'}`);
            });
        });
    } else {
        console.log('Custom events tracking disabled');
    }
    
    // Product selection and price calculation
    const productRadios = document.querySelectorAll('input[name="product_id"]');
    const deliveryRadios = document.querySelectorAll('input[name="delivery_zone"]');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    
    // Initialize with first product
    if (productRadios.length > 0) {
        updateProductInfo(productRadios[0].value);
    }
    
    // Product selection handler
    productRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                updateProductInfo(this.value);
                // GA4 Event: select_content
                if (typeof gtag === 'function') {
                    gtag('event', 'select_content', {
                        content_type: 'product',
                        item_id: this.value,
                        item_name: document.querySelector(`#pro_id${this.value} + .products_dets .product_description h2`).textContent,
                        value: parseFloat(document.querySelector(`#pro_id${this.value} + .products_dets .price .alex-mt strong`).textContent.replace('৳ ', '')),
                        currency: 'BDT'
                    });
                }
                // FB Pixel Event: ViewContent (or AddToCart if user intends to buy)
                if (typeof fbq === 'function') {
                    fbq('track', 'ViewContent', {
                        content_ids: [this.value],
                        content_name: document.querySelector(`#pro_id${this.value} + .products_dets .product_description h2`).textContent,
                        content_type: 'product',
                        value: parseFloat(document.querySelector(`#pro_id${this.value} + .products_dets .price .alex-mt strong`).textContent.replace('৳ ', '')),
                        currency: 'BDT'
                    });
                }
                // Enhanced product selection tracking with e-commerce events
                const productName = document.querySelector(`#pro_id${this.value} + .products_dets .product_description h2`).textContent;
                const productPrice = parseFloat(document.querySelector(`#pro_id${this.value} + .products_dets .price .alex-mt strong`).textContent.replace('৳ ', ''));
                
                trackUniversalEvent('product_select', {
                    product_id: this.value,
                    product_name: productName,
                    product_price: productPrice,
                    currency: 'BDT',
                    timestamp: new Date().toISOString(),
                    browser: getBrowserInfo().name,
                    device_type: getDeviceType()
                });
                
                // E-commerce: View Product
                trackUniversalEvent('view_product', {
                    item_id: this.value,
                    item_name: productName,
                    item_category: 'Course',
                    price: productPrice,
                    currency: 'BDT',
                    timestamp: new Date().toISOString()
                });
                
                trackCustomEvent('product_select', 'Product Selected', `Product ID: ${this.value}, Name: ${productName}`);
            }
        });
    });
    
    // Enhanced delivery zone change handler
    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateTotalAmount();
            const deliveryType = this.id === 'dhaka' ? 'ঢাকার মধ্যে ডেলিভারি' : 'ঢাকার বাইরে ডেলিভারি';
            const deliveryCharge = this.id === 'dhaka' ? ajax_object.dhaka_delivery_charge : ajax_object.outside_dhaka_delivery_charge;
            
            trackUniversalEvent('delivery_option_click', {
                delivery_zone: this.id,
                delivery_type: deliveryType,
                delivery_charge: deliveryCharge,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                device_type: getDeviceType()
            });
            
            trackCustomEvent('delivery_option_select', 'Delivery Option Selected', `${deliveryType} - ৳${deliveryCharge}`);
        });
    });
    
    // Enhanced payment method change handler
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            showPaymentInstructions(this.value);
            const paymentMethodNames = {
                'pay_later': 'Pay Later',
                'bkash': 'bKash',
                'nagad': 'Nagad',
                'rocket': 'Rocket'
            };
            
            trackUniversalEvent('payment_method_click', {
                payment_method: this.value,
                payment_name: paymentMethodNames[this.value] || this.value,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                device_type: getDeviceType()
            });
            
            trackCustomEvent('payment_method_select', 'Payment Method Selected', paymentMethodNames[this.value] || this.value);
        });
    });
    
    // Initialize payment instructions
    showPaymentInstructions('pay_later');
    
    // ========================================
    // SPECIFIC EVENT TRACKING (YOUR REQUIREMENTS)
    // ========================================
    
    // Phone click tracking (ফোন নম্বরে ক্লিক করলে)
    document.querySelectorAll('a[href^="tel:"]').forEach(phoneLink => {
        phoneLink.addEventListener('click', function(e) {
            const phoneNumber = this.href.replace('tel:', '');
            
            trackUniversalEvent('phone_click', {
                phone_number: phoneNumber,
                element_text: this.textContent.trim(),
                element_class: this.className,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                browser_version: getBrowserInfo().version,
                device_type: getDeviceType(),
                page_url: window.location.href
            });
            
            trackCustomEvent('contact_action', 'Phone Number Clicked', phoneNumber);
        });
    });
    
    // WhatsApp click tracking (WhatsApp আইকনে ক্লিক করলে)
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(whatsappLink => {
        whatsappLink.addEventListener('click', function(e) {
            const whatsappUrl = this.href;
            const whatsappNumber = whatsappUrl.match(/\d+/)?.[0] || 'unknown';
            
            trackUniversalEvent('whatsapp_click', {
                whatsapp_number: whatsappNumber,
                whatsapp_url: whatsappUrl,
                element_text: this.textContent.trim(),
                element_class: this.className,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                browser_version: getBrowserInfo().version,
                device_type: getDeviceType(),
                page_url: window.location.href
            });
            
            trackCustomEvent('contact_action', 'WhatsApp Link Clicked', whatsappNumber);
        });
    });
    
    // Copy button click tracking (payment number copy)
    document.querySelectorAll('.copy-btn').forEach(copyBtn => {
        copyBtn.addEventListener('click', function(e) {
            const numberElement = this.previousElementSibling;
            const numberToCopy = numberElement ? numberElement.textContent : 'unknown';
            const paymentMethod = this.closest('.payment-instruction')?.id?.replace('-instruction', '') || 'unknown';
            
            trackUniversalEvent('copy_text', {
                copied_number: numberToCopy,
                payment_method: paymentMethod,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                browser_version: getBrowserInfo().version,
                device_type: getDeviceType()
            });
            
            trackCustomEvent('copy_action', 'Payment Number Copied', `${paymentMethod}: ${numberToCopy}`);
        });
    });
    
    // Enhanced section time tracking for specific sections
    const sectionTimers = {};
    const sectionsToTrack = [
        { id: 'course-section-1', name: 'অর্গানিক মেহেদী তৈরির সহজ উপায়' },
        { id: 'course-section-2', name: 'মেহেদী রঙ বাড়ানোর গোপন টিপস' },
        { id: 'course-section-3', name: 'প্যাকেজিং ও সার্টিফিকেশন' }
    ];
    
    sectionsToTrack.forEach(section => {
        const element = document.getElementById(section.id);
        if (element) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Start timing
                        sectionTimers[section.id] = Date.now();
                        
                        trackUniversalEvent('section_view', {
                            section_id: section.id,
                            section_name: section.name,
                            timestamp: new Date().toISOString(),
                            browser: getBrowserInfo().name,
                            browser_version: getBrowserInfo().version,
                            device_type: getDeviceType(),
                            visibility_percentage: Math.round(entry.intersectionRatio * 100)
                        });
                        
                        trackCustomEvent('section_view', `Section Viewed: ${section.name}`, section.id);
                    } else if (sectionTimers[section.id]) {
                        // Stop timing and track time spent
                        const timeSpent = (Date.now() - sectionTimers[section.id]) / 1000;
                        
                        trackUniversalEvent('section_time_spent', {
                            section_id: section.id,
                            section_name: section.name,
                            time_spent: timeSpent.toFixed(2),
                            timestamp: new Date().toISOString()
                        });
                        
                        trackCustomEvent('time_tracking', `Time Spent on Section: ${section.name}`, `${timeSpent.toFixed(2)}s`);
                        
                        delete sectionTimers[section.id];
                    }
                });
            }, { threshold: 0.3 });
            
            observer.observe(element);
        }
    });
    
    // Customer review slider interaction tracking
    const reviewSlider = document.querySelector('.review-slider, #review-section');
    if (reviewSlider) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    trackUniversalEvent('review_section_view', {
                        section_name: 'কাস্টোমার রিভিউ',
                        timestamp: new Date().toISOString(),
                        browser: getBrowserInfo().name,
                        browser_version: getBrowserInfo().version,
                        device_type: getDeviceType(),
                        visibility_percentage: Math.round(entry.intersectionRatio * 100)
                    });
                    
                    trackCustomEvent('section_view', 'Customer Review Section Viewed', 'কাস্টোমার রিভিউ');
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(reviewSlider);
    }
    
    // ========================================
    // ADVANCED TRACKING FEATURES
    // ========================================
    
    // Idle detection tracking
    let idleTimer;
    let isIdle = false;
    const IDLE_TIME = 30000; // 30 seconds
    
    function resetIdleTimer() {
        clearTimeout(idleTimer);
        if (isIdle) {
            isIdle = false;
            trackUniversalEvent('user_active', {
                timestamp: new Date().toISOString(),
                idle_duration: 'resumed'
            });
        }
        
        idleTimer = setTimeout(() => {
            isIdle = true;
            trackUniversalEvent('idle_detected', {
                timestamp: new Date().toISOString(),
                idle_duration: IDLE_TIME / 1000,
                page_url: window.location.href,
                scroll_position: window.pageYOffset
            });
            
            trackCustomEvent('user_behavior', 'User Idle Detected', `${IDLE_TIME / 1000}s`);
        }, IDLE_TIME);
    }
    
    // Reset idle timer on user activity
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
        document.addEventListener(event, resetIdleTimer, true);
    });
    
    // Initialize idle timer
    resetIdleTimer();
    
    // Exit intent tracking
    let exitIntentShown = false;
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY <= 0 && !exitIntentShown) {
            exitIntentShown = true;
            trackUniversalEvent('exit_intent', {
                timestamp: new Date().toISOString(),
                time_on_page: (Date.now() - performance.timing.navigationStart) / 1000,
                scroll_percentage: (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100,
                page_url: window.location.href
            });
            
            trackCustomEvent('user_behavior', 'Exit Intent Detected', 'Mouse left viewport');
        }
    });
    
    // Error detection tracking
    window.addEventListener('error', function(e) {
        trackUniversalEvent('error_detected', {
            error_message: e.message,
            error_filename: e.filename,
            error_line: e.lineno,
            error_column: e.colno,
            timestamp: new Date().toISOString(),
            page_url: window.location.href,
            user_agent: navigator.userAgent
        });
        
        trackCustomEvent('error_tracking', 'JavaScript Error Detected', e.message);
    });
    
    // Device change detection (orientation, resize)
    let lastOrientation = screen.orientation?.angle || window.orientation || 0;
    let lastWindowSize = `${window.innerWidth}x${window.innerHeight}`;
    
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            const newOrientation = screen.orientation?.angle || window.orientation || 0;
            trackUniversalEvent('device_change', {
                change_type: 'orientation',
                old_orientation: lastOrientation,
                new_orientation: newOrientation,
                timestamp: new Date().toISOString()
            });
            
            lastOrientation = newOrientation;
        }, 100);
    });
    
    window.addEventListener('resize', function() {
        const newWindowSize = `${window.innerWidth}x${window.innerHeight}`;
        if (newWindowSize !== lastWindowSize) {
            trackUniversalEvent('device_change', {
                change_type: 'window_resize',
                old_size: lastWindowSize,
                new_size: newWindowSize,
                timestamp: new Date().toISOString()
            });
            
            lastWindowSize = newWindowSize;
        }
    });
    
    // Battery monitoring (if supported)
    if ('getBattery' in navigator) {
        navigator.getBattery().then(function(battery) {
            function updateBatteryInfo() {
                const batteryLevel = Math.round(battery.level * 100);
                
                trackUniversalEvent('battery_status', {
                    battery_level: batteryLevel,
                    battery_charging: battery.charging,
                    timestamp: new Date().toISOString()
                });
                
                if (batteryLevel <= 15 && !battery.charging) {
                    trackUniversalEvent('battery_low', {
                        battery_level: batteryLevel,
                        battery_charging: battery.charging,
                        timestamp: new Date().toISOString()
                    });
                    
                    trackCustomEvent('device_status', 'Low Battery Warning', `${batteryLevel}%`);
                }
            }
            
            // Track battery changes
            battery.addEventListener('chargingchange', updateBatteryInfo);
            battery.addEventListener('levelchange', updateBatteryInfo);
            
            // Initial battery check
            updateBatteryInfo();
        });
    }
    
    // Memory usage monitoring
    function trackMemoryUsage() {
        if ('memory' in performance) {
            const memInfo = getMemoryUsage();
            const memoryUsagePercent = (memInfo.used / memInfo.total) * 100;
            
            trackUniversalEvent('memory_usage', {
                memory_used_mb: memInfo.used,
                memory_total_mb: memInfo.total,
                memory_limit_mb: memInfo.limit,
                memory_usage_percent: memoryUsagePercent.toFixed(2),
                timestamp: new Date().toISOString()
            });
            
            if (memoryUsagePercent > 80) {
                trackCustomEvent('performance_warning', 'High Memory Usage', `${memoryUsagePercent.toFixed(2)}%`);
            }
        }
    }
    
    // Track memory usage every 30 seconds
    setInterval(trackMemoryUsage, 30000);
    
    // Video play tracking (if YouTube video exists)
    function onYouTubeIframeAPIReady() {
        if (typeof player !== 'undefined' && player) {
            player.addEventListener('onStateChange', function(event) {
                const eventNames = {
                    [-1]: 'unstarted',
                    [0]: 'ended',
                    [1]: 'playing',
                    [2]: 'paused',
                    [3]: 'buffering',
                    [5]: 'cued'
                };
                
                const eventName = eventNames[event.data] || 'unknown';
                
                if (event.data === 1) { // Playing
                    trackUniversalEvent('video_play', {
                        video_title: player.getVideoData()?.title || 'Unknown',
                        video_duration: player.getDuration(),
                        current_time: player.getCurrentTime(),
                        timestamp: new Date().toISOString()
                    });
                    
                    trackCustomEvent('video_interaction', 'Video Started Playing', player.getVideoData()?.title || 'Unknown');
                }
                
                trackUniversalEvent('video_state_change', {
                    state: eventName,
                    current_time: player.getCurrentTime(),
                    duration: player.getDuration(),
                    timestamp: new Date().toISOString()
                });
            });
        }
    }
    
    // Initialize YouTube tracking if player exists
    if (typeof onYouTubeIframeAPIReady !== 'undefined') {
        onYouTubeIframeAPIReady();
    }
    
    function updateProductInfo(productId) {
        // Get product data via AJAX
        if (typeof jQuery !== 'undefined' && typeof ajax_object !== 'undefined') {
            jQuery.post(ajax_object.ajax_url, {
                action: 'get_wc_product_data',
                id: productId,
                nonce: ajax_object.nonce
            }, function(response) {
                if (response && response.name) {
                    document.querySelector('.name_cart').textContent = response.name;
                    document.querySelectorAll('.price_cart').forEach(el => {
                        el.textContent = response.price;
                    });
                    updateTotalAmount();
                    document.querySelector('.invoice-bills').style.display = 'block';
                }
            });
        }
    }
    
    function updateTotalAmount() {
        const selectedDelivery = document.querySelector('input[name="delivery_zone"]:checked');
        const productPrice = parseFloat(document.querySelector('.price_cart')?.textContent) || 0;
        
        let deliveryCharge = 0;
        if (selectedDelivery && typeof ajax_object !== 'undefined') {
            if (selectedDelivery.value === '1') {
                deliveryCharge = parseFloat(ajax_object.dhaka_delivery_charge) || 0;
            } else if (selectedDelivery.value === '2') {
                deliveryCharge = parseFloat(ajax_object.outside_dhaka_delivery_charge) || 0;
            }
        }
        
        const totalAmount = productPrice + deliveryCharge;
        
        const deliveryChargeEl = document.getElementById('delivery_charge');
        const totalAmountEl = document.getElementById('total_amount');
        
        if (deliveryChargeEl) deliveryChargeEl.textContent = deliveryCharge;
        if (totalAmountEl) totalAmountEl.textContent = totalAmount;
    }
    
    function showPaymentInstructions(method) {
        // Hide all instructions and remove required from their transaction inputs
        document.querySelectorAll('.payment-instruction').forEach(instruction => {
            instruction.style.display = 'none';
            const transactionInput = instruction.querySelector('input[name="transaction_number"]');
            if (transactionInput) {
                transactionInput.removeAttribute('required');
                transactionInput.value = ''; // Clear value when hidden
            }
        });
        
        // Show selected instruction and set required for its transaction input if needed
        const selectedInstruction = document.getElementById(method + '-instruction');
        if (selectedInstruction) {
            selectedInstruction.style.display = 'block';
            const transactionInput = selectedInstruction.querySelector('input[name="transaction_number"]');
            if (transactionInput && method !== 'pay_later') {
                transactionInput.setAttribute('required', 'required');
            }
        }
    }
    
    // Track form field interactions
    document.querySelectorAll('input[name="billing_first_name"], input[name="billing_phone"], textarea[name="billing_address_1"]').forEach(input => {
        input.addEventListener('focus', function() {
            trackCustomEvent('form_field_focus', 'Form Field Focused', this.name);
        });
        input.addEventListener('change', function() {
            trackCustomEvent('form_field_change', 'Form Field Changed', `${this.name}: ${this.value.substring(0, 50)}`);
        });
    });

    // Enhanced form submission with better error handling
    const orderForm = document.getElementById('wc-order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-order-btn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> প্রসেসিং...';
            
            const formData = new FormData(this);
            formData.append('action', 'submit_wc_order');
            
            // Enhanced e-commerce tracking for checkout
            const productId = formData.get('product_id');
            const productName = document.querySelector(`#pro_id${productId} + .products_dets .product_description h2`).textContent;
            const productPrice = parseFloat(document.querySelector(`#pro_id${productId} + .products_dets .price .alex-mt strong`).textContent.replace('৳ ', ''));
            const totalValue = parseFloat(document.getElementById('total_amount').textContent);
            const paymentMethod = formData.get('payment_method');
            const deliveryZone = formData.get('delivery_zone');
            const customerName = formData.get('billing_first_name');
            const customerPhone = formData.get('billing_phone');
            
            // Track begin checkout with enhanced details
            trackUniversalEvent('begin_checkout', {
                item_id: productId,
                item_name: productName,
                item_category: 'Course',
                price: productPrice,
                total_value: totalValue,
                currency: 'BDT',
                payment_method: paymentMethod,
                delivery_zone: deliveryZone,
                customer_name: customerName,
                customer_phone: customerPhone,
                timestamp: new Date().toISOString(),
                browser: getBrowserInfo().name,
                browser_version: getBrowserInfo().version,
                device_type: getDeviceType()
            });

            // GA4 Event: begin_checkout
            if (typeof gtag === 'function') {
                const items = [{
                    item_id: productId,
                    item_name: productName,
                    price: productPrice,
                    quantity: 1
                }];
                gtag('event', 'begin_checkout', {
                    currency: 'BDT',
                    value: totalValue,
                    items: items
                });
            }

            // FB Pixel Event: InitiateCheckout
            if (typeof fbq === 'function') {
                fbq('track', 'InitiateCheckout', {
                    content_ids: [productId],
                    content_type: 'product',
                    value: totalValue,
                    currency: 'BDT'
                });
            }
            
            trackCustomEvent('order_form_submit', 'Order Form Submitted');

            if (typeof ajax_object !== 'undefined') {
                fetch(ajax_object.ajax_url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Store form data for invoice
                        const customerName = formData.get('billing_first_name');
                        const customerPhone = formData.get('billing_phone');
                        
                        showInvoiceModal({
                            ...data.data,
                            customer_name: customerName,
                            customer_phone: customerPhone
                        });
                        
                        this.reset();
                        // Reset to first product
                        if (productRadios.length > 0) {
                            productRadios[0].checked = true;
                            updateProductInfo(productRadios[0].value);
                        }
                        // Reset to Pay Later
                        const payLaterRadio = document.getElementById('pay_later');
                        if (payLaterRadio) {
                            payLaterRadio.checked = true;
                            showPaymentInstructions('pay_later');
                        }
                        // Track purchase complete with enhanced details
                        trackUniversalEvent('purchase_complete', {
                            order_id: data.data.order_id,
                            item_id: productId,
                            item_name: productName,
                            item_category: 'Course',
                            price: productPrice,
                            total_value: totalValue,
                            currency: 'BDT',
                            payment_method: paymentMethod,
                            delivery_zone: deliveryZone,
                            customer_name: customerName,
                            customer_phone: customerPhone,
                            timestamp: new Date().toISOString(),
                            browser: getBrowserInfo().name,
                            browser_version: getBrowserInfo().version,
                            device_type: getDeviceType()
                        });
                        
                        trackCustomEvent('order_success', 'Order Placed Successfully', `Order ID: ${data.data.order_id}`);

                        // Save form data to localStorage for auto-fill
                        localStorage.setItem('billing_first_name', customerName);
                        localStorage.setItem('billing_phone', customerPhone);
                        localStorage.setItem('billing_address_1', formData.get('billing_address_1'));

                    } else {
                        alert(data.data || 'অর্ডার প্রসেসিং এ সমস্যা হয়েছে');
                        trackCustomEvent('order_failure', 'Order Submission Failed', data.data || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('অর্ডার প্রসেসিং এ সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
                    trackCustomEvent('order_error', 'Order Submission Error', error.message);
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.classList.remove('btn-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            }
        });
    }

    // Track order buttons clicks
    document.getElementById('order-button-top')?.addEventListener('click', function() {
        trackCustomEvent('button_click', 'Order Button Clicked', 'Top Order Button');
    });
    document.getElementById('order-button-middle')?.addEventListener('click', function() {
        trackCustomEvent('button_click', 'Order Button Clicked', 'Middle Order Button');
    });
    document.getElementById('submit-order-btn')?.addEventListener('click', function() {
        trackCustomEvent('button_click', 'Order Button Clicked', 'Submit Order Button');
    });

    // Auto-fill form fields from localStorage
    const billingFirstNameInput = document.querySelector('input[name="billing_first_name"]');
    const billingPhoneInput = document.querySelector('input[name="billing_phone"]');
    const billingAddressInput = document.querySelector('textarea[name="billing_address_1"]');

    if (billingFirstNameInput && localStorage.getItem('billing_first_name')) {
        billingFirstNameInput.value = localStorage.getItem('billing_first_name');
    }
    if (billingPhoneInput && localStorage.getItem('billing_phone')) {
        billingPhoneInput.value = localStorage.getItem('billing_phone');
    }
    if (billingAddressInput && localStorage.getItem('billing_address_1')) {
        billingAddressInput.value = localStorage.getItem('billing_address_1');
    }

    // Send screen size to server once per session
    const sessionId = getSessionId();
    if (sessionId && typeof jQuery !== 'undefined' && typeof ajax_object !== 'undefined') {
        const screenWidth = window.screen.width;
        const screenHeight = window.screen.height;
        const screenSize = `${screenWidth}x${screenHeight}`;

        // Check if screen size is already stored in session (or a temporary cookie) to avoid redundant updates
        if (!sessionStorage.getItem('screen_size_sent_' + sessionId)) {
            jQuery.post(ajax_object.ajax_url, {
                action: 'update_device_screen_size',
                session_id: sessionId,
                screen_size: screenSize,
                nonce: ajax_object.screen_size_nonce // Use the new nonce
            }, function(response) {
                if (response.success) {
                    sessionStorage.setItem('screen_size_sent_' + sessionId, 'true');
                }
            });
        }
    }


});

function copyNumber(elementId) {
    const numberElement = document.getElementById(elementId);
    if (!numberElement) return;
    
    const number = numberElement.textContent;
    
    navigator.clipboard.writeText(number).then(function() {
        // Show success feedback
        const button = numberElement.nextElementSibling;
        if (button) {
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copied!';
            button.style.background = '#28a745';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '#dd0055';
            }, 2000);
        }
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('কপি করতে সমস্যা হয়েছে। নাম্বারটি ম্যানুয়ালি কপি করুন: ' + number);
    });
}

function showInvoiceModal(data) {
    const orderIdEl = document.getElementById('invoice-order-id');
    const customerNameEl = document.getElementById('invoice-customer-name');
    const customerPhoneEl = document.getElementById('invoice-customer-phone');
    const paymentMethodEl = document.getElementById('invoice-payment-method');
    const transactionNumberEl = document.getElementById('invoice-transaction-number');
    const totalAmountEl = document.getElementById('invoice-total-amount');
    
    if (orderIdEl) orderIdEl.textContent = '#' + (data.order_id || 'N/A');
    if (customerNameEl) customerNameEl.textContent = data.customer_name || 'N/A';
    if (customerPhoneEl) customerPhoneEl.textContent = data.customer_phone || 'N/A';
    if (paymentMethodEl) paymentMethodEl.textContent = data.payment_method || 'N/A';
    if (transactionNumberEl) transactionNumberEl.textContent = data.transaction_number || 'N/A';
    if (totalAmountEl) {
        const totalAmount = document.getElementById('total_amount');
        totalAmountEl.textContent = '৳' + (totalAmount ? totalAmount.textContent : '0');
    }
    
    const modal = document.getElementById('invoice-modal');
    if (modal) modal.classList.add('show');
}

function closeInvoiceModal() {
    const modal = document.getElementById('invoice-modal');
    if (modal) modal.classList.remove('show');
}

// Close modal when clicking outside
const invoiceModal = document.getElementById('invoice-modal');
if (invoiceModal) {
    invoiceModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeInvoiceModal();
        }
    });
}

// Ensure time spent for visible sections and buttons is sent on page unload
window.addEventListener('beforeunload', function() {
    // Track time spent for any currently visible sections
    // This sectionTimers object is not defined in the provided PHP, so it's removed.
    // for (const sectionId in sectionTimers) {
    //     if (sectionTimers.hasOwnProperty(sectionId)) {
    //         const timeSpent = (Date.now() - sectionTimers[sectionId]) / 1000;
    //         // Use navigator.sendBeacon for reliable data transmission on unload
    //         if (navigator.sendBeacon) {
    //             const formData = new FormData();
    //             formData.append('action', 'track_custom_event');
    //             formData.append('session_id', getSessionId()); // Use the new getSessionId()
    //             formData.append('event_type', 'section_time_spent');
    //             formData.append('event_name', `Time Spent on Section: ${sectionId} (Unload)`);
    //             formData.append('event_value', `${timeSpent.toFixed(2)}s`);
    //             formData.append('nonce', ajax_object.event_nonce); // Use the new nonce
    //             navigator.sendBeacon(ajax_object.ajax_url, formData);
    //         } else {
    //             // Fallback for older browsers (less reliable on unload)
    //             trackCustomEvent('section_time_spent', `Time Spent on Section: ${sectionId} (Unload)`, `${timeSpent.toFixed(2)}s`);
    //         }
    //     }
    // }
});
</script>

<?php get_footer(); ?>
