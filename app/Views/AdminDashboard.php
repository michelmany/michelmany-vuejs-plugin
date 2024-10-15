<?php

namespace MMVUEJS\Views;

class AdminDashboard {
	/**
	 * Render the admin dashboard.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap" id="mmvuejs-app"></div>';
		echo '<noscript><div class="error">' . esc_html__( 'JavaScript is required to use this plugin. Please enable JavaScript in your browser settings.',
				'mmvuejs' ) . '</div></noscript>';
	}
}