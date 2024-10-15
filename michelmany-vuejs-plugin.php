<?php
/**
 * Plugin Name: Michel Many VueJS Plugin
 * Description: A simple VueJS plugin for WordPress.
 * Version: 1.0.0
 * Author: Michel Many
 * Plugin URI: https://github.com/michelmany/michelmany-vuejs-plugin
 * Author URI: https://michelmany.com
 * Text Domain: mmvuejs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MMVUEJS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MMVUEJS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MMVUEJS_VERSION', '1.0.0' );

if ( file_exists( MMVUEJS_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once MMVUEJS_PLUGIN_PATH . 'vendor/autoload.php';
}

/**
 * Initialize the AppServiceProvider to register all services
 *
 * @return void
 */
function mmvuejs_init(): void {
	( new MMVUEJS\Providers\AppServiceProvider() )->registerServices();
}

add_action( 'plugins_loaded', 'mmvuejs_init' );
