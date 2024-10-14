<?php

namespace MMVUEJS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RestApiController {
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes(): void {
		register_rest_route( 'mmvuejs/v1', '/settings', [
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_settings' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		] );

		register_rest_route( 'mmvuejs/v1', '/settings', [
			'methods'             => 'POST',
			'callback'            => array( $this, 'update_settings' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		] );

		register_rest_route( 'mmvuejs/v1', '/data', [
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_data' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		] );
	}

	public function get_settings() {
		// Your logic to get settings
	}

	public function update_settings() {
		// Your logic to update settings
	}

	public function get_data() {
		// Your logic to get data
	}
}