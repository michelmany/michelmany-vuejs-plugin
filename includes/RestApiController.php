<?php

namespace MMVUEJS;

use WP_REST_Request;

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

		register_rest_route( 'mmvuejs/v1', '/settings/(?P<setting_key>[a-zA-Z0-9_-]+)', [
			'methods'             => 'POST',
			'callback'            => array( $this, 'update_settings' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
			'args'                => [
				'setting_key' => [
					'required' => true,
				],
			],
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
		$default_settings = [
			'numberOfRows'      => 5,
			'humanReadableDate' => true,
			'emails'            => [ get_option( 'mmvuejs_admin_email' ) ],
		];
		$settings = get_option( 'mmvuejs_settings', $default_settings );

		return rest_ensure_response( $settings );
	}

	public function update_settings( WP_REST_Request $request ) {
		// Verify nonce
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'invalid_nonce', 'Invalid nonce.', [ 'status' => 403 ] );
		}

		$setting_key = $request->get_param( 'setting_key' );
		$value = $request->get_param( 'value' );

		// Define allowed settings and their validation callbacks
		$allowed_settings = [
			'numberOfRows'      => array( $this, 'validate_number_of_rows' ),
			'humanReadableDate' => array( $this, 'validate_human_readable_date' ),
			'emails'            => array( $this, 'validate_emails' ),
		];

		if ( ! array_key_exists( $setting_key, $allowed_settings ) ) {
			return new WP_Error( 'invalid_setting', 'Invalid setting key.', [ 'status' => 400 ] );
		}

		// Validate the value
		$validation_function = $allowed_settings[ $setting_key ];
		$valid_value = $validation_function( $value );

		if ( is_wp_error( $valid_value ) ) {
			return $valid_value; // Return the error
		}

		// Update the setting
		$settings = get_option( 'my_plugin_settings', [] );
		$settings[ $setting_key ] = $valid_value;
		update_option( 'my_plugin_settings', $settings );

		return rest_ensure_response( [ $setting_key => $valid_value ] );
	}

	public function validate_number_of_rows( $value ) {
		$value = (int) $value;
		if ( $value < 1 || $value > 5 ) {
			return new WP_Error( 'invalid_number_of_rows', 'Number of rows must be between 1 and 5.',
				[ 'status' => 400 ] );
		}

		return $value;
	}

	public function validate_human_readable_date( $value ) {
		$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
		if ( $value === null ) {
			return new WP_Error( 'invalid_human_readable_date', 'Invalid value for humanReadableDate.',
				[ 'status' => 400 ] );
		}

		return $value;
	}

	public function validate_emails( $value ) {
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

	public function get_data( WP_REST_Request $request ) {
		// Attempt to retrieve cached data
		$cached_data = get_option( 'mmvuejs_cached_data' );
		$cache_time = get_option( 'mmvuejs_cache_time' );

		if ( $cached_data && $cache_time && ( time() - $cache_time < HOUR_IN_SECONDS ) ) {
			return rest_ensure_response( $cached_data );
		}

		$response = wp_remote_get( 'https://miusage.com/v1/challenge/2/static/' );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'external_api_error', 'Error fetching data from external API.', [ 'status' => 500 ] );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'json_decode_error', 'Error decoding JSON response.', [ 'status' => 500 ] );
		}

		// Cache the data
		update_option( 'mmvuejs_cached_data', $data );
		update_option( 'mmvuejs_cache_time', time() );

		return rest_ensure_response( $data );
	}
}