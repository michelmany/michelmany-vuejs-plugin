<?php
/**
 * Plugin Name: Michel Many VueJS Plugin
 * Description: A simple VueJS plugin for WordPress.
 * Version: 1.0.0
 * Author: Michel Many
 * Text Domain: mmvuejs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'MMVUEJS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MMVUEJS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( MMVUEJS_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once MMVUEJS_PLUGIN_PATH . 'vendor/autoload.php';
}

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( MMVUEJS\Init::class ) ) {
	MMVUEJS\Init::registerServices();
}