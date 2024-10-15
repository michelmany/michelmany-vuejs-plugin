<?php

namespace MMVUEJS\Controllers;

use MMVUEJS\Helpers\SanitizationHelper;
use MMVUEJS\Models\SettingsModel;
use WP_Error;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RestApiController {
	private const EXTERNAL_API_URL = 'https://miusage.com/v1/challenge/2/static/';

	/**
	 * Register the REST API routes.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Define the REST API routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route( 'mmvuejs/v1', '/settings', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_all_settings' ],
			'permission_callback' => [ $this, 'permissions_check' ],
		] );

		register_rest_route( 'mmvuejs/v1', '/settings/(?P<setting_key>[a-zA-Z0-9_-]+)', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'update_settings' ],
			'permission_callback' => [ $this, 'permissions_check' ],
			'args'                => [
				'setting_key' => [
					'required'          => true,
					'validate_callback' => [ $this, 'validate_setting_key' ],
				],
			],
		] );

		register_rest_route( 'mmvuejs/v1', '/data', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_data' ],
			'permission_callback' => [ $this, 'permissions_check' ],
		] );
	}

	/**
	 * Get all settings.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_all_settings(): \WP_REST_Response {
		$settings = SettingsModel::get_all_settings();

		return rest_ensure_response( $settings );
	}

	/**
	 * Update a specific setting.
	 *
	 * @param  WP_REST_Request  $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_settings( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'invalid_nonce', __( 'Invalid nonce.', 'mmvuejs' ), [ 'status' => 403 ] );
		}

		$setting_key = sanitize_text_field( $request->get_param( 'setting_key' ) );
		$value = $request->get_param( 'value' );

		$allowed_settings = $this->get_allowed_settings();

		if ( ! array_key_exists( $setting_key, $allowed_settings ) ) {
			return new WP_Error( 'invalid_setting', __( 'Invalid setting key.', 'mmvuejs' ), [ 'status' => 400 ] );
		}

		$validation_function = $allowed_settings[ $setting_key ];
		$valid_value = $validation_function( $value );

		if ( is_wp_error( $valid_value ) ) {
			return $valid_value;
		}

		SettingsModel::update_settings( $setting_key, $valid_value );

		return rest_ensure_response( [ $setting_key => $valid_value ] );
	}

	/**
	 * Get data from the external API.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_data(): \WP_REST_Response {
		$cached_data = get_option( 'mmvuejs_cached_data' );
		$cache_time = get_option( 'mmvuejs_cache_time' );

		if ( $cached_data && $cache_time && ( time() - $cache_time < HOUR_IN_SECONDS ) ) {
			return rest_ensure_response( $cached_data );
		}

		$response = wp_remote_get( self::EXTERNAL_API_URL );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'external_api_error', __( 'Error fetching data from external API.', 'mmvuejs' ),
				[ 'status' => 500 ] );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'json_decode_error', __( 'Error decoding JSON response.', 'mmvuejs' ),
				[ 'status' => 500 ] );
		}

		update_option( 'mmvuejs_cached_data', $data );
		update_option( 'mmvuejs_cache_time', time() );

		return rest_ensure_response( $data );
	}

	/**
	 * Get the allowed settings and their validation functions.
	 *
	 * @return array
	 */
	private function get_allowed_settings(): array {
		return [
			'numberOfRows'      => [ SanitizationHelper::class, 'validate_number_of_rows' ],
			'humanReadableDate' => [ SanitizationHelper::class, 'validate_human_readable_date' ],
			'emails'            => [ SanitizationHelper::class, 'validate_emails' ],
		];
	}

	/**
	 * Check if the current user has the required permissions.
	 *
	 * @return bool
	 */
	public function permissions_check(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Validate the setting key.
	 *
	 * @param  mixed  $param
	 * @param  WP_REST_Request  $request
	 * @param  string  $key
	 *
	 * @return WP_Error|bool
	 */
	public function validate_setting_key( mixed $param, WP_REST_Request $request, string $key ): WP_Error|bool {
		if ( ! is_string( $param ) || empty( $param ) ) {
			return new WP_Error( 'invalid_setting_key', __( 'Invalid setting key.', 'mmvuejs' ), [ 'status' => 400 ] );
		}

		return true;
	}
}