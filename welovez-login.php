<?php
/**
 * Plugin Name:       WeLovez Social Login For WoWonder Php Social Script
 * Description:       Login and Register your users using WoWonder Api V1. Add social share button on your wordpress website.
 * Version:           2.1
 * Author:            RedLionsTech
 * Author URI:        https://www.upwork.com/freelancers/~01294a12683efe659e
 * Text Domain:       welovez
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Abort if this file is called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Plugin constants
 */

if(!defined('WELOVEZ_URL'))
	define('WELOVEZ_URL', plugin_dir_url( __FILE__ ));
if(!defined('WELOVEZ_PATH'))
	define('WELOVEZ_PATH', plugin_dir_path( __FILE__ ));

/*
 * Redirect after activation
 */
function welovez_redirect_activation($plugin) {
	$redirect_url = admin_url( 'admin.php?page=welovez_login' );
	if( $plugin == plugin_basename( __FILE__ ) ) {
		exit( wp_redirect( $redirect_url ) );
	}	
}
add_action( 'activated_plugin', 'welovez_redirect_activation', 10, 1);

/*
 * Import the plugin classes
 */
include_once WELOVEZ_PATH . '/classes/welovez.php';
include_once WELOVEZ_PATH . '/classes/welovezadmin.php';