<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Dynamic_Tags_For_Elementor
 * @subpackage Dynamic_Tags_For_Elementor/includes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Deactivator {

	public static function deactivate() {
		delete_transient( 'dynatafo_admin_notice' );

	}
}
