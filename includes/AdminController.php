<?php

namespace MMVUEJS;

class AdminController {
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add admin menu
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_menu_page(
			__( 'MMVUEJS Dashboard', 'mmvuejs' ),
			__( 'MMVUEJS', 'mmvuejs' ),
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