<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'ELEMENTOR_HELLO_WORLD__FILE__', __FILE__ );

/**
 * Load Hello World
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function wp_multi_store_locator_elementer() {
	// Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		if( function_exists('hello_world_fail_load') ){
			add_action( 'admin_notices', 'hello_world_fail_load' );
		}
		return;
	}
	// Check version required
	$elementor_version_required = '1.0.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		if( function_exists('hello_world_fail_load_out_of_date') ){
			add_action( 'admin_notices', 'hello_world_fail_load_out_of_date' );
		}
		return;
	}
	// Require the main plugin file
	require( __DIR__ . '/plugin.php' );
}
add_action( 'plugins_loaded', 'wp_multi_store_locator_elementer' );
