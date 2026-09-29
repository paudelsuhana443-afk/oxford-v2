<?php
/**
 * Notice: Pro version released and active marketing spotlight
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dynatafo_title_callback = function() {
	return esc_html__( 'Dynamic Tags for Elementor Pro Release Spotlight', 'dynamic-tags-for-elementor' );
};

$dynatafo_message_callback = function() {
	return wp_kses_post(
		'<div class="dynatafo-pro-promo-notice" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center; padding: 15px 0;">' .
			'<div class="dynatafo-pro-promo-content" style="flex: 1 1 500px;">' .
				'<h3 style="margin: 0 0 10px; color: #1d2327; font-size: 18px; font-weight: 600;">🚀 ' . esc_html__( 'Dynamic Tags for Elementor Pro — 149+ Premium Tags Available!', 'dynamic-tags-for-elementor' ) . '</h3>' .
				'<p style="font-size: 14px; line-height: 1.5; color: #50575e; margin: 0 0 15px;">' .
					esc_html__( 'Unlock the ultimate dynamic content toolkit for Elementor. The Pro version activates highly requested layout features, advanced queries, and complete WooCommerce/ACF/SCF integrations:', 'dynamic-tags-for-elementor' ) .
				'</p>' .
				'<ul style="list-style-type: none; margin: 0; padding: 0; font-size: 13px; color: #50575e; line-height: 1.7;">' .
					'<li style="margin-bottom: 8px;">📝 <strong>' . esc_html__( 'WordPress Pro Suite (47 Tags)', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'Reading Time, Related Posts count, Next/Prev post navigation, Word & Character counts, Days since published, and Days until date count-downs.', 'dynamic-tags-for-elementor' ) . '</li>' .
					'<li style="margin-bottom: 8px;">🖼️ <strong>' . esc_html__( 'Media & Image Pro (6 Tags)', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'Attachment width/height dimensions, attachment file size, file type, and Featured Image Alt, Caption, and Description metadata tags.', 'dynamic-tags-for-elementor' ) . '</li>' .
					'<li style="margin-bottom: 8px;">👤 <strong>' . esc_html__( 'Author & User Pro (12 Tags)', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'Logged-in user first/last name, comment count, custom user meta, author registration date, and social profile links.', 'dynamic-tags-for-elementor' ) . '</li>' .
					'<li style="margin-bottom: 8px;">🌐 <strong>' . esc_html__( 'Site & System Pro (13 Tags)', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'WordPress & PHP version strings, active plugin count, theme/child-theme name, PHP memory usage, and site tagline fallback.', 'dynamic-tags-for-elementor' ) . '</li>' .
					'<li style="margin-bottom: 8px;">🛒 <strong>' . esc_html__( 'WooCommerce Pro (34 Tags)', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'Add-to-cart URLs, SKU, stock status, ratings, sale prices, product galleries, and custom product fields.', 'dynamic-tags-for-elementor' ) . '</li>' .
					'<li style="margin-bottom: 8px;">⚙️ <strong>' . esc_html__( 'ACF & SCF Pro Parity (34 Tags)', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'Full custom field repeaters, relationships, gallery list items, Google maps, formatted date-times, and link attributes.', 'dynamic-tags-for-elementor' ) . '</li>' .
					'<li style="margin-bottom: 8px;">🛡️ <strong>' . esc_html__( 'VIP Grade Performance', 'dynamic-tags-for-elementor' ) . '</strong> — ' . esc_html__( 'Optimized DB queries and priority help desk email support directly from our core developers.', 'dynamic-tags-for-elementor' ) . '</li>' .
				'</ul>' .
			'</div>' .
			'<div class="dynatafo-pro-promo-pricing" style="background: #f0f6fc; border-left: 4px solid #2c5aa0; padding: 15px 20px; border-radius: 4px; min-width: 260px; max-width: 320px; box-sizing: border-box; text-align: center; margin-left: auto;">' .
				'<div style="font-weight: bold; color: #1d2327; font-size: 16px; margin-bottom: 5px;">' . esc_html__( 'GOLD PLAN', 'dynamic-tags-for-elementor' ) . '</div>' .
				'<div style="font-size: 11px; color: #646970; margin-bottom: 10px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">' . esc_html__( 'Save up to 12% on Yearly Pricing!', 'dynamic-tags-for-elementor' ) . '</div>' .
				'<div style="margin: 10px 0;">' .
					'<span style="text-decoration: line-through; color: #a7aaad; font-size: 14px; margin-right: 5px;">' . esc_html__( 'Normally $3.12 / mo', 'dynamic-tags-for-elementor' ) . '</span>' .
					'<br>' .
					'<span style="font-size: 28px; font-weight: 800; color: #2c5aa0;">$2.75</span> <span style="font-size: 14px; color: #50575e;">' . esc_html__( '/ mo', 'dynamic-tags-for-elementor' ) . '</span>' .
				'</div>' .
				'<div style="font-size: 13px; color: #50575e; margin-bottom: 15px; font-weight: 600;">' . esc_html__( 'Billed Annually ($33 / Year)', 'dynamic-tags-for-elementor' ) . '</div>' .
				'<a href="' . esc_url( admin_url( 'admin.php?page=dynatafo-info-pricing' ) ) . '" class="button button-primary button-large" style="display: block; font-weight: bold; padding: 5px 20px; text-transform: uppercase; text-align: center; text-decoration: none; background-color: #2c5aa0; border-color: #2c5aa0;">' . esc_html__( 'Upgrade Now', 'dynamic-tags-for-elementor' ) . '</a>' .
			'</div>' .
		'</div>'
	);
};

return [
	'id'       => 'pro-released',
	'type'     => 'info',
	'title'    => $dynatafo_title_callback,
	'message'  => $dynatafo_message_callback,
	'version'  => DYNATAFO_VERSION,
	'priority' => 5,
];
