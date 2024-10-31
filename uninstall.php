<?php
/**
* Fired when the plugin is uninstalled.
*
* @package      Order Restriction
* @author       Tech Nuxt <technuxt@gmail.com>
* @license      GPL-2.0+
* @link         https://technuxt.com
* @copyright    2024 Tech Nuxt
*/

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}
$option_name = 'ortn_minimum_amount';

delete_option( $option_name );

// for site options in Multisite
delete_site_option( $option_name );

