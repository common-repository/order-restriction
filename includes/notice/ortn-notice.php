<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
// Add custom notice before cart contents in WooCommerce
add_action('woocommerce_before_cart', 'ortn_custom_cart_notice', 10);

function ortn_custom_cart_notice() {
    // Get the minimum order amount from settings, default to 150 if not set
    $minimum_amount = absint(get_option('ortn_minimum_amount', 150));

    // Calculate total cart amount
    $cart_total = WC()->cart->get_cart_contents_total();
    
    // Check if cart total is less than the minimum amount
    if ($cart_total < $minimum_amount) {
        // Retrieve options with default values
        $notice_type = sanitize_html_class(get_option('ortn_select_norice_type', 'woocommerce-message'));
        $icon_class = sanitize_html_class(get_option('ortn_icon_class', 'fa-shopping-cart'));
        $alert_message = esc_html(get_option('ortn_alert_message', 'Minimum order amount is 150.'));

        // Output the custom notice
        ?>
        <div class="<?php echo esc_attr($notice_type); ?>">
            <i class="fa-solid <?php echo esc_attr($icon_class); ?>"></i>
            <?php echo esc_attr( $alert_message );?>
        </div>
        <?php
    }
}
?>
