<?php
/**
 * Notice: Version update is live (dynamic)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Defer translations to avoid early loading.
return [];

/*
$dynatafo_title_callback = function() {
    // translators: %s: Plugin version number
    return sprintf( esc_html__( 'Dynamic Tags for Elementor %s is live now', 'dynamic-tags-for-elementor' ), DYNATAFO_VERSION );
};

$dynatafo_message_callback = function() {
    // translators: %s: Plugin version number
    return sprintf( esc_html__( 'Version %s has been released successfully.', 'dynamic-tags-for-elementor' ), DYNATAFO_VERSION );
};

return [
	'id'       => 'release-live',
	'type'     => 'success',
	'title'    => $dynatafo_title_callback,
	'message'  => $dynatafo_message_callback,
	'version'  => DYNATAFO_VERSION,
	'priority' => 15,
];
*/
