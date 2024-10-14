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
		$default_settings = [
			'numberOfRows'      => 5,
			'humanReadableDate' => true,
			'emails'            => [ get_option( 'mmvuejs_admin_email' ) ],
		];
		$settings = get_option( 'mmvuejs_settings', $default_settings );

		return rest_ensure_response( $settings );
	}

	public function update_settings( WP_REST_Request $request ) {
		$params = $request->get_json_params();

		// Sanitize and validate inputs
		$numberOfRows = intval( $params['numberOfRows'] );
		$humanReadableDate = filter_var( $params['humanReadableDate'], FILTER_VALIDATE_BOOLEAN );
		$emails = array_filter( $params['emails'], 'sanitize_email' );

		if ( $numberOfRows < 1 || $numberOfRows > 5 ) {
			return new WP_Error( 'invalid_number_of_rows', 'Number of rows must be between 1 and 5',
				[ 'status' => 400 ] );
		}

		if ( count( $emails ) < 1 || count( $emails ) > 5 ) {
			return new WP_Error( 'invalid_email_count', 'You must have between 1 and 5 emails', [ 'status' => 400 ] );
		}

		foreach ( $emails as $email ) {
			if ( ! is_email( $email ) ) {
				return new WP_Error( 'invalid_email', 'One or more emails are invalid', [ 'status' => 400 ] );
			}
		}

		$settings = [
			'numberOfRows'      => $numberOfRows,
			'humanReadableDate' => $humanReadableDate,
			'emails'            => $emails,
		];

		update_option( 'mmvuejs_settings', $settings );

		return rest_ensure_response( $settings );
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