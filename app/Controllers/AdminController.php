<?php

namespace MMVUEJS\Controllers;

use MMVUEJS\Views\AdminDashboard;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AdminController {
    /**
     * Register the admin menu and scripts.
     *
     * @return void
     */
    public function register(): void {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Add admin menu.
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_menu_page(
            __('Michel Many VueJS Plugin Dashboard', 'mmvuejs'),
            __('MMVUEJS', 'mmvuejs'),
            'manage_options',
            'mmvuejs-dashboard',
            [$this, 'display_dashboard'],
            'dashicons-chart-area',
            100
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook
     * @return void
     */
    public function enqueue_admin_scripts(string $hook): void {
        if ($hook !== 'toplevel_page_mmvuejs-dashboard') {
            return;
        }

        wp_enqueue_script(
            'mmvuejs-admin',
            esc_url(MMVUEJS_PLUGIN_URL . 'dist/main.js'),
            ['wp-i18n'],
            MMVUEJS_VERSION,
            true
        );

        if (file_exists(MMVUEJS_PLUGIN_PATH . 'dist/assets/main.css')) {
            wp_enqueue_style(
                'mmvuejs-admin',
                esc_url(MMVUEJS_PLUGIN_URL . 'dist/assets/main.css'),
                [],
                MMVUEJS_VERSION
            );
        }

        wp_localize_script('mmvuejs-admin', 'mmvuejs', [
            'api_url' => esc_url_raw(rest_url()),
            'nonce'   => wp_create_nonce('wp_rest'),
        ]);

		wp_set_script_translations('mmvuejs-admin', 'mmvuejs', MMVUEJS_PLUGIN_PATH . 'languages');
    }

    /**
     * Display dashboard.
     *
     * @return void
     */
    public function display_dashboard(): void {
        $dashboard = new AdminDashboard();
        $dashboard->render();
    }
}