<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MMVUEJS_Admin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add admin menu
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_menu_page(
			__( 'MMVJP Dashboard', 'mmvjp' ),
			__( 'MMVJP', 'mmvjp' ),
			'manage_options',
			'mmvuejs-dashboard',
			array( $this, 'display_dashboard' ),
			'dashicons-admin-generic',
			65
		);
	}

	/**
	 * Display dashboard
	 * @return void
	 */
	public function display_dashboard(): void {
		echo '<div class="wrap"><h1>' . __( 'Michel Many Dashboard', 'mmvuejs' ) . '</h1></div>';
	}
}