<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dynamic_Tags_For_Elementor
 * @subpackage Dynamic_Tags_For_Elementor/includes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Activator {

	/**
	 * Run the activation logic.
	 *
	 * This method collects system data and prepares the database 
	 * for the plugin to function.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$system_info = [
			'wp_version'      => get_bloginfo( 'version' ),
			'php_version'     => PHP_VERSION,
			'mysql_version'   => $wpdb->db_version(),
			'elementor_v'     => defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : 'not_installed',
			'elementor_pro_v' => defined( 'ELEMENTOR_PRO_VERSION' ) ? ELEMENTOR_PRO_VERSION : 'not_active',
			'activation_date' => current_time( 'mysql' ),
			'site_url'        => get_site_url(),
		];

		// We use update_option so it overwrites if they reactivate later
		update_option( 'dynatafo_activation_data', $system_info );

	}
}