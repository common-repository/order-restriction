<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
function ortn_disable_checkout_under_minimum() {
    // Minimum order amount get from settings
    $minimum_amount = get_option( 'ortn_minimum_amount', 150 );
    
    // Calculate total cart amount
    $cart_total = WC()->cart->get_cart_contents_total();
  
    // If cart total is less than minimum amount, redirect to custom page
    if ( $cart_total < $minimum_amount && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
        ?>
            <!-- Modal Structure -->
        <div style="background-color: rgba(0, 0, 0, 0.5);display: block; position: fixed;z-index: 1; left: 0;top: 0;text-align:center; width: 100%;height: 100%; overflow: auto;" id="alertModal" class="modal">
            <div style="background-color: #fefefe;margin: 15% auto; padding: 20px;border: 1px solid #888; width: 80%;max-width: 500px;position: relative;" class="modal-content">
                <div style="font-size:110px;color:red;">❌</div>
                <p><?php echo esc_attr('Minimum order amount is '. $minimum_amount.'. '.' Your current cart total is ' .$cart_total ); ?></p>
                <form method="post">
                <?php wp_nonce_field('redirect_cart_action', 'redirect_cart_nonce'); ?>
                    <input name="redirect_cart" type="submit" style="padding: 10px 20px;font-size: 16px;cursor: pointer;background-color: #007bff;color: white;border: none;" value="Close">
                </form>
                
            </div>
        </div>
        <?php
            if (isset($_POST['redirect_cart']) && check_admin_referer('redirect_cart_action', 'redirect_cart_nonce')) {
                // Redirect to the cart page
                wp_redirect(wc_get_cart_url());
            }
        exit;
    }
}
add_action( 'template_redirect', 'ortn_disable_checkout_under_minimum' );


?>