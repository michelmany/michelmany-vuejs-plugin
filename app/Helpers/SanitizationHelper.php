<?php

namespace MMVUEJS\Helpers;

use WP_Error;

class SanitizationHelper {
	/**
	 * Validate the number of rows.
	 *
	 * @param  mixed  $value
	 *
	 * @return WP_Error|int
	 */
	public static function validate_number_of_rows( mixed $value ): WP_Error|int {
		$value = (int) $value;
		if ( $value < 1 || $value > 5 ) {
			return new WP_Error( 'invalid_number_of_rows', __( 'Number of rows must be between 1 and 5.', 'mmvuejs' ),
				[ 'status' => 400 ] );
		}

		return $value;
	}

	/**
	 * Validate the human-readable date setting.
	 *
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	public static function validate_human_readable_date( mixed $value ): mixed {
		$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
		if ( $value === null ) {
			return new WP_Error( 'invalid_human_readable_date', __( 'Invalid value for humanReadableDate.', 'mmvuejs' ),
				[ 'status' => 400 ] );
		}

		return $value;
	}

	/**
	 * Validate the emails setting.
	 *
	 * @param  mixed  $value
	 *
	 * @return WP_Error|array
	 */
	public static function validate_emails( mixed $value ): WP_Error|array {
		if ( ! is_array( $value ) ) {
			return new WP_Error( 'invalid_emails', __( 'Emails must be an array.', 'mmvuejs' ), [ 'status' => 400 ] );
		}
		$emails = array_map( 'sanitize_email', $value );
		$emails = array_filter( $emails );

		if ( count( $emails ) < 1 || count( $emails ) > 5 ) {
			return new WP_Error( 'invalid_email_count', __( 'You must have between 1 and 5 valid emails.', 'mmvuejs' ),
				[ 'status' => 400 ] );
		}

		foreach ( $emails as $email ) {
			if ( ! is_email( $email ) ) {
				return new WP_Error( 'invalid_email', __( 'One or more emails are invalid.', 'mmvuejs' ),
					[ 'status' => 400 ] );
			}
		}

		return $emails;
	}
}