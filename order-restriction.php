<?php
/**
 * @package           Order Restriction
 * @author            Tech Nuxt <technuxt@gmail.com>
 * @license           GPL-2.0+
 * @link              https://technuxt.com  
 * @copyright         2014 Tech Nuxt
 * 
 * @wordpress-plugin
 * Plugin Name:       Order Restriction
 * Plugin URI:        https://wordpress.org/plugins/order-restriction/
 * Description:       Through this plugin woocommerce order can be restricted, you can set any minimum amount like 150$ if you want and type an alert message so customers can't order less than 150$
 * Version:           1.0.2
 * Tags:              restrict order for minimum cart amount, woocommerce order restriction
 * Requires at least: 5.7
 * Tested up to:      6.6
 * Author:            Tech Nuxt
 * Author URI:        https://technuxt.com
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       order-restriction
 * Domain Path:       /languages
 * Requires Plugin:   woocommerce
 */

 //  include css and js for user
 if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
  function ortn_enqueue_style() {
    // enqueue css
    wp_register_style( 'ortn-user-style', plugin_dir_url(__FILE__) . '/public/css/ortn-style.css', array(), '1.0.2', 'all' );
    wp_enqueue_style('ortn-user-style');
    // enqueue font awesome
    wp_register_style( 'ortn-user-font-awesome-all', plugin_dir_url( __FILE__ ) . '/public/css/ortn-all.css', array(), '6.5.2', 'all' );
    wp_enqueue_style('ortn-user-font-awesome-all');
    
    
    wp_register_script( 'ortn-user-script', plugin_dir_url( __FILE__ ) . '/public/js/ortn-main.js', array(), '1.0.2','all' );
    wp_enqueue_script('ortn-user-script');
  }
  add_action( 'wp_enqueue_scripts', 'ortn_enqueue_style' );






  //  include css and js for admin
  function ortn_enqueue_style_admin($hook) {
    // get current page url
    $current_page =sanitize_key(filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW));
    // enqueue css
    if ($current_page != 'order-restriction') {
        return;
    }
    wp_register_style( 'ortn-admin-bootstrap', plugin_dir_url( __FILE__ ) . '/admin/css/ortn-bootstrap.css', array(), '5.3.3', 'all' );
    wp_enqueue_style('ortn-admin-bootstrap');

    wp_register_style( 'ortn-admin-font-awesome-all', plugin_dir_url(__FILE__) . '/admin/css/ortn-all.css', array(), '6.5.2', 'all' );
    wp_enqueue_style('ortn-admin-font-awesome-all');
    
    wp_register_style( 'ortn-admin-style', plugin_dir_url(__FILE__) . '/admin/css/ortn-style.css', array(), '1.0.2', 'all' );
    wp_enqueue_style('ortn-admin-style');

  
    wp_register_script( 'ortn-admin-script', plugin_dir_url(__FILE__) . '/admin/js/ortn-main.js' , array(), '1.0.2', true ); 
    wp_enqueue_script('ortn-admin-script');
    
    wp_register_script( 'ortn-admin-bootstrap-js', plugin_dir_url( __FILE__ ) . '/admin/js/ortn-bootstrap.js', array(), '5.3.3', true );
    wp_enqueue_script('ortn-admin-bootstrap-js');
    
    wp_register_script( 'ortn-admin-bootstrap-js-bundle', plugin_dir_url( __FILE__ ) . '/admin/js/ortn-bootstrap.bundle.js', array(), '5.3.3', true );  
    wp_enqueue_script('ortn-admin-bootstrap-js-bundle');
  }
  add_action( 'admin_enqueue_scripts', 'ortn_enqueue_style_admin' );




  // Disable checkout page for orders under $150
  include 'includes/vat/ortn-vat-restriction.php';


  // include custom notice
  include 'includes/notice/ortn-notice.php';
  

//   // make shortcode for show notice
  add_action('init', 'ortn_register_shortcodes');
  function ortn_register_shortcodes() {
    add_shortcode('ortn_alert_message', 'ortn_alert_message_shortcode');
  }

  function ortn_alert_message_shortcode($atts) {
    // Get the options from the database
    $message = esc_html(get_option('ortn_alert_message', 'Minimum order amount is 150.'));
    $icon_class = esc_attr(get_option('ortn_icon_class', 'fa-check'));
    $notice_type = esc_attr(get_option('ortn_select_norice_type', 'ortn-message-warning'));
    
    // Generate the HTML for the alert message
    $output = '<div class="' . $notice_type . '">';
    $output .= '<i class="fa-solid ' . $icon_class . '"></i> ';
    $output .= $message;
    $output .= '</div>';
    
    $minimum_amount = get_option( 'ortn_minimum_amount', 150 );
    if(WC()->cart){
      $cart_total = WC()->cart->get_cart_contents_total();
    }else{
      $cart_total = 0;
    }
    if( $cart_total < $minimum_amount ) {
      return $output;
    }
}









  add_action('admin_init', 'ortn_register_settings');

  function ortn_register_settings() {
      register_setting('ortn_settings_group', 'ortn_alert_message', 'sanitize_text_field');
      register_setting('ortn_settings_group', 'ortn_icon_class', 'sanitize_text_field');
      register_setting('ortn_settings_group', 'ortn_select_norice_type', 'sanitize_text_field');
      register_setting('ortn_settings_group', 'ortn_minimum_amount', 'absint');
  }
  
  function ortn_sanitize_minimum_amount($input) {
      // Sanitize and validate the minimum amount to ensure it is a positive number
      $input = absint($input);
      if ($input <= 0) {
          add_settings_error(
              'ortn_minimum_amount',
              'ortn_minimum_amount_error',
              __('Minimum order amount must be a positive number.', 'order-restriction'),
              'error'
          );
          return get_option('ortn_minimum_amount');
      }
      return $input;
  }
  


  add_action('admin_post_update_options', 'ortn_handle_form_submission');
  function ortn_handle_form_submission() {

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ '_wpnonce' ] ) ), 'update-options' ) ) {
      return;
    }
  
      // Nonce verification passed
      // Sanitize and update options
      update_option('ortn_alert_message', sanitize_text_field($_POST['ortn_alert_message']));
      update_option('ortn_icon_class', sanitize_text_field($_POST['ortn_icon_class']));
      update_option('ortn_select_norice_type', sanitize_text_field($_POST['ortn_select_norice_type']));
      update_option('ortn_minimum_amount', absint($_POST['ortn_minimum_amount']));
  
      // Redirect back to the settings page
      wp_redirect(admin_url('options-general.php?page=order-restriction'));
      exit;
  }
  



  

// plugin customization setting
add_action( 'admin_menu', 'ortn_admin_menu' );
function ortn_admin_menu() {
    add_menu_page( 'Order Restriction', 'Order Restriction', 'manage_options', 'order-restriction', 'ortn_admin_page', 'dashicons-clipboard', 110 );
}

function ortn_admin_page() {
?>
  <section class="ortn-section mt-5">
    <div class="container">

      


      <div class="row">
            <!-- left side -->
          <div class="col-8">
            <!-- titlw section -->
              <div class="row">
                <div class="col-6">
                    <h1><?php echo esc_html( 'Order Restriction', 'order-restriction' ); ?></h1>
                    <p><?php echo esc_html( 'Restrict order to specific amount.', 'order-restriction' ); ?></p>
                </div>

                <div class="col-6 text-end">
                <a href="https://www.buymeacoffee.com/inzams" target="_blank"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/default-yellow.png' ); ?>" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>
                </div>
              </div> <!-- end of row -->
            <!-- titlw section -->


            <form action="options.php" method="post">
                <?php wp_nonce_field('update-options'); ?>
                <?php settings_fields('ortn_settings_group'); ?>
                <?php do_settings_sections('ortn_settings_group'); ?>

                <!-- shortcode copy area -->
                <div class="form-group bg-light p-3 mb-3">
                  <div class="row">
                    <div class="col-6">
                        <span><?php echo esc_html( 'ShortCode', 'order-restriction' ); ?></span>
                    </div>
                    <div class="col-6 text-end">
                        <div class="ortn-shortcode">
                          <code id="ortnShortcode"><?php echo esc_html( '[ortn_alert_message]', 'order-restriction' ); ?></code>
                          <button class="button button-primary" id="copyShortcodeBtn"><?php echo esc_html( 'Copy', 'order-restriction' ); ?></button>
                        </div>
                    </div>
                  </div>   
                </div>
                <!-- end of shortcode copy area -->
                

                <div class="form-group bg-light p-3 mb-3">
                    <label for="name"><?php echo esc_html( 'Alert Message', 'order-restriction' ); ?></label>
                    <input type="text" id="ortn_alert_message" name="ortn_alert_message" value="<?php echo esc_attr(get_option('ortn_alert_message', 'Minimum order amount is 150.')); ?>">
                </div>        

                <!-- notice icon -->
                <div class="form-group orth-icon bg-light p-3 mb-3"> 
                  <div class="row">
                      <div class="col-6">
                          <span><?php echo esc_html( 'Select icon', 'order-restriction' ); ?></span>
                            <i id="iconClassshow" class="fa-solid <?php echo esc_attr(get_option('ortn_icon_class', 'fa-shopping-cart')); ?>"></i>
                            <input value="<?php echo esc_attr(get_option('ortn_icon_class', 'fa-shopping-cart')); ?>" type="hidden" class="form-control" id="iconClassInput" name="ortn_icon_class">
                      </div>
                      <div class="col-6 text-end">
                            <button type="button" class="button button-primary" id="showIconsBtn"><?php echo esc_html( 'Show Icons', 'order-restriction' ); ?></button>
                        </div>
                  </div>
                </div>
                <!-- icons notice end -->

                <!-- The Modal -->
                <div style="display: none" class="modal fade show" id="iconsModal" tabindex="-1" aria-labelledby="iconsModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="iconsModalLabel"><?php echo esc_html( 'Select Icon', 'order-restriction' ); ?></h5>
                        <button type="button" class="btn-close" id="closeModal" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <div class="row" id="iconsContainer">
                                            <!-- Icons will be loaded here dynamically -->
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- modal end -->

                <div class="form-group bg-light p-3 mb-3">
                  <div class="row">
                      <div class="col-6">
                          <label for="ortn_select_norice_type"><?php echo esc_html( 'Notice Type', 'order-restriction' ); ?></label>
                        </div>
                      <div class="col-6 text-right">
                        <select id="ortn_select_norice_type" name="ortn_select_norice_type">
                            <option value="ortn-message-warning" <?php selected(get_option('ortn_select_norice_type'), 'ortn-message-warning'); ?>><?php echo esc_html( 'Warning', 'order-restriction' ); ?></option>
                            <option value="ortn-message-danger" <?php selected(get_option('ortn_select_norice_type'), 'ortn-message-danger'); ?>><?php echo esc_html( 'Danger', 'order-restriction' ); ?></option>
                            <option value="orth-default-message" <?php selected(get_option('ortn_select_norice_type'), 'orth-default-message'); ?>><?php echo esc_html( 'WooCommerce', 'order-restriction' ); ?></option>
                        </select>
                      </div>
                  </div>
                </div>

                <div class="form-group bg-light p-3 mb-3">
                    <label for="ortn_minimum_amount"><?php echo esc_html( 'Minimum Order Amount', 'order-restriction' ); ?></label>
                    <input type="number" id="ortn_minimum_amount" name="ortn_minimum_amount" value="<?php echo esc_attr(get_option('ortn_minimum_amount', 150)); ?>">
                </div>

                        <!-- update SQL function area -->
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="page_options" value="ortn_minimum_amount,ortn_alert_message,ortn_select_norice_type,ortn_icon_class">
                        <input type="submit" value="Save changes" class="button button-primary mt-2">
            </form>
          </div> <!-- col-8 -->
          <!-- end left side -->
        

          <!-- right side -->
          <div class="col-4">
              <div class="image-container mb-3">
                  <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/technuxt-logo.png' ); ?>" alt="<?php echo esc_attr( 'Order Restriction', 'order-restriction' ); ?>">
              </div>
              <p class="description"><?php echo esc_html( '🌟 Welcome to Technuxt! 🌟 At Technuxt, we are more than just a software company - we are your partners in digital innovation. 💻📱 Our mission? To craft cutting-edge web and mobile solutions that empower businesses to thrive in the digital age. With a passion for innovation and a commitment to excellence, we blend creativity with technology to bring your ideas to life. From elegant mobile apps to robust web platforms, we specialize in creating bespoke software solutions tailored to your unique needs. Join us on this exciting journey as we push the boundaries of possibility and redefine the future of technology together. Lets innovate, inspire, and transform the digital landscape, one line of code at a time. Welcome to Technuxt - where innovation knows no bounds! 🚀✨', 'order-restriction' ); ?></p>
              <div class="buttons">
                <a href="<?php echo esc_attr('https://technuxt.com') ?>" target="_blank" class="mb-2 button button-primary"><?php echo esc_html( 'Visit Website', 'order-restriction' ); ?></a>
                <a href="<?php echo esc_attr('https://www.facebook.com/technuxt24/') ?>" target="_blank" class="mb-2 button button-primary"><?php echo esc_html( 'Visit Facebook', 'order-restriction' ); ?></a>
                <a href="<?php echo esc_attr('https://www.instagram.com/technuxt/') ?>" target="_blank" class="mb-2 button button-primary"><?php echo esc_html( 'Visit Instagram', 'order-restriction' ); ?></a>
                <a href="<?php echo esc_attr('https://www.linkedin.com/company/technuxt/') ?>" target="_blank" class="mb-2 button button-primary"><?php echo esc_html( 'Visit LinkedIn', 'order-restriction' ); ?></a>
                <a href="<?php echo esc_attr('https://technuxt.com/order-restriction/user-guide') ?>" target="_blank" class="mb-2 button button-primary"><?php echo esc_html( 'User Guide', 'order-restriction' ); ?></a>
              </div>
          </div>  <!-- col-4-->
          <!-- end right side -->
            
            
      </div> <!--end row -->


    </div>  <!-- end container -->
  </section>

<?php   
} //ortn_admin_page




 // plugin redirect
 register_activation_hook( __FILE__, 'ortn_plugin_activation' );
 function ortn_plugin_activation(){
   add_option('ortn_plugin_do_active', true);
 }


 add_action('admin_init', 'ortn_plugin_redirect');

 function ortn_plugin_redirect() {
     // Check if the option is set
     if (get_option('ortn_plugin_do_active')) {
         // Delete the option to prevent repeated redirects
         delete_option('ortn_plugin_do_active');
 
         // Redirect to the plugin page
         wp_safe_redirect(admin_url('admin.php?page=order-restriction'));
         exit;
     }
 }




?>