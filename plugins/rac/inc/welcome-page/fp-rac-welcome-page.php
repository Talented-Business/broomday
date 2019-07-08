<script>
    jQuery(document).ready(function () {
        jQuery('.tab_1').click(function () {
            jQuery('.tab_1').addClass('active_welcome');
            jQuery('.tab_2').removeClass('active_welcome');
            jQuery('.con_1').show();
            jQuery('.con_2').hide();
        });
        jQuery('.tab_2').click(function () {
            jQuery('.tab_2').addClass('active_welcome');
            jQuery('.tab_1').removeClass('active_welcome');
            jQuery('.con_1').hide();
            jQuery('.con_2').show();
        });
    });
</script>

<div class=" welcome_page">
    <div class="welcome_header" > 
        <div class="welcome_title" >
            <h1>Welcome to <strong>Recover Abandoned Cart</strong></h1>
        </div>
        <div class="branding_logo" >
            <a href="http://fantasticplugins.com/" target="_blank" ><img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Fantastic-Plugins-final-Logo.png" alt="" /></a>
        </div>
    </div> 

    <p>
        Thanks for installing Recover Abandoned Cart...
    </p>

    <div class="welcomepage_tab">
        <ul>
            <li><a href="#about" class="tab_1 active_welcome">About Recover Abandoned Cart</a></li>
            <!--            <li><a href="#compatibl plugins" class="tab_3">Compatible Plugins</a></li>-->
            <li><a href="#ourplugins" class="tab_2">Our Other Plugins</a></li> 
        </ul>        
        <a href="<?php echo admin_url('edit.php?post_type=raccartlist&page=fprac_slug'); ?>" class="admin_btn" >Go to Settings</a>        
        <a href="http://fantasticplugins.com/support/" class="support_btn" target="_blank" >Contact Support</a>        
    </div>
    <!--            about Recover Abandoned Cart tab content      -->
    <div class="con_1">
        <div class="section_1">
            <div class='section_a1'> 
                <h3>Points to Consider</h3>
                <ul>                    
                    <li>Products added to cart by  members(logged-in users) and guests will be captured in a separate Table <b>“Cart List Table”</b></li>
                    <li>Products added to cart by members(logged-in users) will be captured as soon as the product is added to cart</li>
                    <li>Products added to cart by guests will be captured when they enter their email id in checkout form</li>
                    <li>Recover Abandoned Cart uses <a href="https://code.tutsplus.com/articles/insights-into-wp-cron-an-introduction-to-scheduling-tasks-in-wordpress--wp-23119" target="_blank" >cron</a> for changing the captured cart status from <b>NEW</b> to <b>ABANDON</b> and sending automatic abandoned cart emails. For more information on cron <a href="https://code.tutsplus.com/articles/insights-into-wp-cron-an-introduction-to-scheduling-tasks-in-wordpress--wp-23119" target="_blank" >click here</a></li>
                    <li>A captured cart will be considered as abandoned only when the captured time exceeds the duration set in<b> “Abandon Cart Time for Members/Guests”</b> </li>
                    <li>You can also send abandoned cart emails to old unpaid orders by using the  <b>“Check Previous Orders”</b> option</li>
                    <li>If your users are unable to receive abandoned cart emails, then please select <b>“Exclude”</b> in<b> “MIME Version 1.0 Parameter”</b> and <b>“Reply-To Parameter”</b> options under<b> “Troubleshoot”</b> tab</li>
                    <li>When an user receives a coupon code in abandoned cart email, the same coupon code only will be sent in future abandoned cart emails until the user has used the coupon code.</li>
                </ul>
            </div> 
        </div>
    </div>

    <!--            our other plugins tab content      -->     
    <div class="con_2">
        <div class="con2_title">
            <h2>Our Other WooCommerce Plugins</h2>
        </div>
        <div class="feature">
            <div class="two_fet_img">
                <a href="https://codecanyon.net/item/sumo-reward-points-woocommerce-reward-system/7791451?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Reward_Points.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Reward Points</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Reward points</strong>  is a WooCommerce Loyalty Reward points System. Using <b>SUMO Reward points</b>, you can offer Reward points to your customers for Account Sign Up, Product Purchases, Writing Reviews etc.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>

                <a href="https://codecanyon.net/item/sumo-subscriptions-woocommerce-subscription-system/16486054?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Subscription.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Subscriptions</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Subscriptions</strong> is a subscription extension for WooCommerce. Using <b>SUMO Subscriptions</b>, you can create and sell subscription products in your existing WooCommerce shop.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="two_fet_img">


                <a href="https://codecanyon.net/item/galaxy-funder-woocommerce-crowdfunding-system/7360954?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Galaxy_Funder.png" alt=""/>
                        <div class="hide">
                            <h4>Galaxy Funder</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Galaxy Funder</strong> is a Crowdfunding Extension for WooCommerce. Using <b>Galaxy Funder</b> you can run <b>Keep What you Raise</b> Crowdfunding Campaigns in your existing WooCommerce Shop. </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
                <a href="https://codecanyon.net/item/universe-funder-woocommerce-crowdfunding-system/10283380?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Universe_Funder.png" alt=""/>
                        <div class="hide">
                            <h4>Universe Funder</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Universe Funder</strong> is a Crowdfunding Extension for WooCommerce. Using <b>Universe Funder</b> you can run <b>All or Nothing </b> Crowdfunding Campaigns in your existing WooCommerce Shop </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>


            </div>
        </div>
        <div class="feature">
            <div class="two_fet_img">
                <a href="https://codecanyon.net/item/woocommerce-pay-your-price/7000238?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Pay_Your_Price.png" alt=""/>
                        <div class="hide">
                            <h4>Pay Your Price</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>Pay Your Price</strong> is a WooCommerce Extension. Using <b>Pay Your Price</b>, Users can pay their own price for the Products. </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a> 

                <a href="https://codecanyon.net/item/woocommerce-paypal-adaptive-split-payment/7948397?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Paypal_Adaptive_Split_Payment.png" alt=""/>
                        <div class="hide">
                            <h4>PayPal Adaptive Split Payment</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>PayPal Adaptive Split Payment</strong> is a Payment Gateway Extension for WooCommerce. Using <b>PayPal Adaptive Split Payment</b>, the Order amount can be split between a maximum of six different Receivers</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="two_fet_img">

                <a href="https://codecanyon.net/item/sumo-affiliates-woocommerce-affiliate-system/18273930?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Affiliates.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Affiliates</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Affiliates</strong> is a Affiliate System for WooCommerce. Using <b>SUMO Affiliates</b> you can run Affiliate Promotions in your existing WooCommerce Shop.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>

                <a href="https://codecanyon.net/item/sumo-memberships-woocommerce-membership-system/16642362?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Membership.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Memberships</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Memberships </strong>is a membership extension for WooCommerce. Using <b>SUMO Memberships</b>, you can restrict/provide access to specific Pages, Posts, Products, URL.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="feature">
            <div class="two_fet_img">
                <a href="https://codecanyon.net/item/sumo-donations-woocommerce-donation-system/12283878?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Donation.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Donations</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Donations</strong> is a complete WooCommerce Donation System. Using <b>SUMO Donations</b>, you can provide options for your users to make donations to your site.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>

                <a href="https://codecanyon.net/item/sumo-discounts-advanced-pricing-woocommerce-discount-system/17116628?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Discount.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Discounts</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Discounts</strong> is a WooCommerce Extension Plugin. Using <b>SUMO Discounts</b> plugin you can provide discounts to your users in multiple ways.</p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="two_fet_img">
                <a href="https://codecanyon.net/item/sumo-coupons-woocommerce-coupon-system/16082070?ref=FantasticPlugins" target="_blank" >
                    <div class="Brand_1">
                        <img src="<?php echo RAC_PLUGIN_URL; ?>/assets/images/Sumo_Coupons.png" alt=""/>
                        <div class="hide">
                            <h4>SUMO Coupons</h4>
                        </div>
                        <div class="description_adv">
                            <p><strong>SUMO Coupons</strong>  is a WooCommerce Loyalty Coupon System. Using <b>SUMO Coupons</b> you can offer coupons to your customers for Account Sign Up, Product Purchases, Writing Reviews etc. </p>
                        </div>
                        <div class="buy_now">
                            <button type="button">Buy Now</button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

