<?php

namespace MMVUEJS;

use WP_REST_Request;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RestApiController {
	private const EXTERNAL_API_URL = 'https://miusage.com/v1/challenge/2/static/';

	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register the routes for the objects of the controller.
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
	 * Get all settings from the database
	 * @return \WP_REST_Response
	 */
	public function get_all_settings(): \WP_REST_Response {
		$default_settings = [
			'numberOfRows'      => 5,
			'humanReadableDate' => true,
			'emails'            => [ get_option( 'mmvuejs_admin_email' ) ],
		];
		$settings = get_option( 'mmvuejs_settings', $default_settings );

		return rest_ensure_response( $settings );
	}

	/**
	 * Update a setting in the database
	 *
	 * @param  WP_REST_Request  $request
	 *
	 * @return \WP_REST_Response
	 */
	public function update_settings( WP_REST_Request $request ): \WP_REST_Response {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'invalid_nonce', 'Invalid nonce.', [ 'status' => 403 ] );
		}

		$setting_key = $request->get_param( 'setting_key' );
		$value = $request->get_param( 'value' );

		$allowed_settings = $this->get_allowed_settings();

		if ( ! array_key_exists( $setting_key, $allowed_settings ) ) {
			return new WP_Error( 'invalid_setting', 'Invalid setting key.', [ 'status' => 400 ] );
		}

		$validation_function = $allowed_settings[ $setting_key ];
		$valid_value = call_user_func( $validation_function, $value );

		if ( is_wp_error( $valid_value ) ) {
			return $valid_value;
		}

		$settings = get_option( 'mmvuejs_settings', [] );
		$settings[ $setting_key ] = $valid_value;
		update_option( 'mmvuejs_settings', $settings );

		return rest_ensure_response( [ $setting_key => $valid_value ] );
	}

	/**
	 * Get data from an external API
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
			return new WP_Error( 'external_api_error', 'Error fetching data from external API.', [ 'status' => 500 ] );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'json_decode_error', 'Error decoding JSON response.', [ 'status' => 500 ] );
		}

		update_option( 'mmvuejs_cached_data', $data );
		update_option( 'mmvuejs_cache_time', time() );

		return rest_ensure_response( $data );
	}

	/**
	 * Get the allowed settings and their validation functions
	 * @return array[]
	 */
	private function get_allowed_settings(): array {
		return [
			'numberOfRows'      => [ $this, 'validate_number_of_rows' ],
			'humanReadableDate' => [ $this, 'validate_human_readable_date' ],
			'emails'            => [ $this, 'validate_emails' ],
		];
	}

	/**
	 * Validate the number of rows setting
	 *
	 * @param $value
	 *
	 * @return int|WP_Error
	 */
	public function validate_number_of_rows( $value ): WP_Error|int {
		$value = (int) $value;
		if ( $value < 1 || $value > 5 ) {
			return new WP_Error( 'invalid_number_of_rows', 'Number of rows must be between 1 and 5.',
				[ 'status' => 400 ] );
		}

		return $value;
	}

	/**
	 * Validate the humanReadableDate setting
	 *
	 * @param $value
	 *
	 * @return mixed|WP_Error
	 */
	public function validate_human_readable_date( $value ): mixed {
		$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
		if ( $value === null ) {
			return new WP_Error( 'invalid_human_readable_date', 'Invalid value for humanReadableDate.',
				[ 'status' => 400 ] );
		}

		return $value;
	}

	/**
	 * Validate the emails setting
	 *
	 * @param $value
	 *
	 * @return array|WP_Error
	 */
	public function validate_emails( $value ): WP_Error|array {
		if ( ! is_array( $value ) ) {
			return new WP_Error( 'invalid_emails', 'Emails must be an array.', [ 'status' => 400 ] );
		}
		$emails = array_map( 'sanitize_email', $value );
		$emails = array_filter( $emails );

		if ( count( $emails ) < 1 || count( $emails ) > 5 ) {
			return new WP_Error( 'invalid_email_count', 'You must have between 1 and 5 valid emails.',
				[ 'status' => 400 ] );
		}

		foreach ( $emails as $email ) {
			if ( ! is_email( $email ) ) {
				return new WP_Error( 'invalid_email', 'One or more emails are invalid.', [ 'status' => 400 ] );
			}
		}

		return $emails;
	}

	/**
	 * Check if the current user has the necessary permissions
	 * @return bool
	 */
	public function permissions_check(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Validate the setting key
	 *
	 * @param $param
	 * @param $request
	 * @param $key
	 *
	 * @return bool
	 */
	public function validate_setting_key( $param, $request, $key ): bool {
		return is_string( $param ) && ! empty( $param );
	}
}