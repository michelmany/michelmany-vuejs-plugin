<?php

namespace MMVUEJS;

class AdminController {
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add admin menu
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_menu_page(
			__( 'Michel Many VueJS Plugin Dashboard', 'mmvuejs' ),
			__( 'MMVUEJS', 'mmvuejs' ),
			'manage_options',
			'mmvuejs-dashboard',
			array( $this, 'display_dashboard' ),
			'dashicons-admin-generic',
			100
		);
	}

	public function enqueue_admin_scripts( $hook ) {
		if ( $hook !== 'toplevel_page_mmvuejs-dashboard' ) {
			return;
		}

		// Enqueue the main JavaScript file
		wp_enqueue_script(
			'mmvuejs-admin',
			MMVUEJS_PLUGIN_URL . 'dist/main.js',
			array(),
			MMVUEJS_VERSION,
			true
		);

		// Enqueue the CSS file if you have one
		if ( file_exists( MMVUEJS_PLUGIN_PATH . 'dist/assets/main.css' ) ) {
			wp_enqueue_style(
				'mmvuejs-admin',
				MMVUEJS_PLUGIN_URL . 'dist/assets/main.css',
				array(),
				MMVUEJS_VERSION
			);
		}

		wp_localize_script( 'mmvuejs-admin', 'mmvuejs', array(
			'api_url' => esc_url_raw( rest_url() ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		) );
	}

	/**
	 * Display dashboard
	 * @return void
	 */
	public function display_dashboard(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap" id="mmvuejs-app"></div>';
		echo '<noscript><div class="error">JavaScript is required to use this plugin. Please enable JavaScript in your browser settings.</div></noscript>';
	}
}