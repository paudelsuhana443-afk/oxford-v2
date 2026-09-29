<?php
/**
 * Notice: What's new (dynamic version)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Defer translations to avoid early loading.
$dynatafo_title_callback = function() {
	/* translators: %s: Plugin version number */
	return sprintf( esc_html__( "What's New in v%s", 'dynamic-tags-for-elementor' ), DYNATAFO_VERSION );
};

$dynatafo_message_callback = function() {
	/* translators: %s: Plugin version number */
	$whats_new_title = sprintf( esc_html__( 'What’s new in v%s', 'dynamic-tags-for-elementor' ), DYNATAFO_VERSION );

	return wp_kses_post(
		'<div style="line-height: 1.6; max-width: 900px; padding: 5px 0;">' .
			'<p><strong style="color: #2c5aa0; font-size: 16px;">' . $whats_new_title . '</strong></p>' .
			'<p>' . esc_html__( 'This release introduces compatibility updates and ecosystem enhancements.', 'dynamic-tags-for-elementor' ) . '</p>' .

			'<h4 style="margin: 14px 0 6px; color: #1d2327;">⚡ ' . esc_html__( 'Compatibility & Ecosystem Updates', 'dynamic-tags-for-elementor' ) . '</h4>' .
			'<ul style="margin: 6px 0 12px 20px; list-style-type: disc;">' .
				'<li>' . esc_html__( 'Pro Ecosystem: Aligned with Dynamic Tags for Elementor Pro v1.4.5 featuring the new PublishPress Authors Pro integration.', 'dynamic-tags-for-elementor' ) . '</li>' .
				'<li>' . esc_html__( 'Elementor Compatibility: Verified compatibility with Elementor 4.2.4 and Elementor Pro 4.2.3.', 'dynamic-tags-for-elementor' ) . '</li>' .
				'<li>' . esc_html__( 'WordPress Compatibility: Verified compatibility up to WordPress 7.1.', 'dynamic-tags-for-elementor' ) . '</li>' .
			'</ul>' .

			'<p style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #e5e7eb;">' .
				'<strong>' . esc_html__( 'All changes are fully backward compatible!', 'dynamic-tags-for-elementor' ) . '</strong> ' .
				esc_html__( 'Thank you for choosing Dynamic Tags for Elementor — enjoy the update.', 'dynamic-tags-for-elementor' ) .
			'</p>' .
		'</div>'
	);
};

return [
	'id'       => 'whats-new',
	'type'     => 'info',
	'title'    => $dynatafo_title_callback,
	'message'  => $dynatafo_message_callback,
	'version'  => DYNATAFO_VERSION,
	'priority' => 25,
];
