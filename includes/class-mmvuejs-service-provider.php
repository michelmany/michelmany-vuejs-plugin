<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MMVUEJS_Service_Provider {
	/**
	 * Register services
	 * @return void
	 */
	public function register_services(): void {
		if ( is_admin() ) {
			$this->register_admin_services();
		}
	}

	/**
	 * Register admin services
	 * @return void
	 */
	private function register_admin_services(): void {
		require_once MMVUEJS_PLUGIN_PATH . 'includes/class-mmvuejs-admin.php';
		new MMVUEJS_Admin();
	}
}