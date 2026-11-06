<?php
/*
    Plugin Name: Simple-JWT-Login
    Plugin URI: https://wordpress.org/plugins/simple-jwt-login/
    Description: Simple-JWT-Login REST API Plugin. Allows you to login / register to WordPress using JWT.
    Author: Nicu Micle
    Author URI: https://profiles.wordpress.org/nicu_m/
    Text Domain: simple-jwt-login
    Domain Path: /i18n
    Version: 1.5.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

include_once 'src/autoload.php';

// it inserts the entry in the admin menu
add_action( 'admin_menu', 'simple_jwt_login_plugin_create_menu_entry' );
add_action( 'plugins_loaded', 'simple_jwt_login_plugin_load_translations' );

// creating the menu entries
function simple_jwt_login_plugin_create_menu_entry() {
	// icon image path that will appear in the menu
	$icon = plugins_url( '/images/simple-jwt-login-16x16.png', __FILE__ );
	// adding the main menu entry
	add_menu_page( 'Simple-JWT-Login Plugin', 'Simple JWT Login', 'manage_options', 'main-page-simple-jwt-login-plugin',
		'simple_jwt_login_plugin_show_main_page', $icon );
}

/**
 * Load translations
 * @since  1.3
 */
function simple_jwt_login_plugin_load_translations() {
	load_plugin_textdomain(
		'simple-jwt-login',
		false,
		plugin_basename( dirname( __FILE__ ) ) . '/i18n/'
	);
}

// function triggered in add_menu_page
function simple_jwt_login_plugin_show_main_page() {
	require_once( 'src/views/simple-jwt-login-main-page.php' );
}

// plugin deactivation
register_uninstall_hook( __FILE__, 'simple_jwt_plugin_uninstall' );

/**
 * Delete options on plugin uninstall
 * @since 1.3
 */
function simple_jwt_plugin_uninstall() {
	delete_option( SimpleJWTLogin\Modules\SimpleJWTLoginSettings::OPTIONS_KEY );
}

//REST API ROUTES
include_once 'routes.php';