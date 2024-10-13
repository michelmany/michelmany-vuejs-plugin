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

class MichelMoraesVueJSPlugin {
	private static $instance = null;

	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function define_constants() {
		define( 'MMVUEJS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'MMVUEJS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'MMVUEJS_VERSION', '1.0.0' );
	}

	/**
	 * Include files
	 * @return void
	 */
	private function includes(): void {
		require_once MMVUEJS_PLUGIN_PATH . 'includes/class-mmvuejs-service-provider.php';
	}

	/**
	 * Initialize hooks
	 * @return void
	 */
	private function init_hooks(): void {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'init_service_provider' ) );
	}

	public function activate() {
		// Activation code here.
	}

	public function deactivate() {
		// Deactivation code here.
	}

	/**
	 * Initialize service provider
	 * @return void
	 */
	public function init_service_provider(): void {
		$service_provider = new MMVUEJS_Service_Provider();
		$service_provider->register_services();
	}
}

MichelMoraesVueJSPlugin::instance();