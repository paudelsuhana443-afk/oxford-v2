<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Site_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-site-url'; }
    public function get_title() { return esc_html__( 'Site URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'url' ]; }

    public function get_value( array $options = [] ) {
        $url = home_url( '/' );

        // Apply WPML language-aware URL filter if WPML is active
        if ( class_exists( 'DYNATAFO_WPML' ) ) {
            /* translators: Home URL being processed for language support */
            $url = DYNATAFO_WPML::apply_language_to_home_url( $url );
        }

        return $url;
    }
}