<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only show when Pro is active and is a higher version than free
if ( ! defined( 'DYNATAFOPRO_VERSION' ) ) {
    return [];
}
if ( ! version_compare( DYNATAFOPRO_VERSION, DYNATAFO_VERSION, '>' ) ) {
    return [];
}

$dynatafo_pro_newer_title = function() {
    return esc_html__( 'Dynamic Tags for Elementor: Update Available', 'dynamic-tags-for-elementor' );
};

$dynatafo_pro_newer_message = function() {
    return sprintf(
        wp_kses_post(
            /* translators: 1: Pro version, 2: Free version, 3: update URL */
            __( 'Your <strong>Pro plugin (v%1$s)</strong> is newer than your free plugin (v%2$s). Some Pro tags may not work correctly until you <a href="%3$s">update the free plugin</a>.', 'dynamic-tags-for-elementor' )
        ),
        esc_html( DYNATAFOPRO_VERSION ),
        esc_html( DYNATAFO_VERSION ),
        esc_url( admin_url( 'update-core.php' ) )
    );
};

return [
    'id'       => 'pro-newer-than-free',
    'type'     => 'warning',
    'priority' => 10,
    'version'  => DYNATAFO_VERSION,
    'title'    => $dynatafo_pro_newer_title,
    'message'  => $dynatafo_pro_newer_message,
];
