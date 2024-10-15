<?php

namespace MMVUEJS\Models;

class SettingsModel {
	/**
	 * Get all settings.
	 *
	 * @return array
	 */
	public static function get_all_settings(): array {
		$default_settings = [
			'numberOfRows'      => 5,
			'humanReadableDate' => true,
			'emails'            => [ sanitize_email( get_option( 'mmvuejs_admin_email' ) ) ],
		];

		return get_option( 'mmvuejs_settings', $default_settings );
	}

	/**
	 * Update a specific setting.
	 *
	 * @param  string  $setting_key
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public static function update_settings( string $setting_key, mixed $value ): bool {
		$settings = get_option( 'mmvuejs_settings', [] );
		$settings[ $setting_key ] = $value;

		return update_option( 'mmvuejs_settings', $settings );
	}
}