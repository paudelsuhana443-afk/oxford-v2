<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Reload_Page_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-reload-page-url'; }
    public function get_title() { return esc_html__( 'Reload Page (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-actions-group'; }
    public function get_categories() { return [ 'url' ]; }

    public function get_value( array $options = [] ) {
        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            $plugin = \Elementor\Plugin::instance();

            if ( isset( $plugin->frontend ) && method_exists( $plugin->frontend, 'create_action_hash' ) ) {
                return (string) $plugin->frontend->create_action_hash( 'reload-page' );
            }
        }

        $post_id = get_the_ID();
        $url = get_permalink();

        // Apply WPML language-aware URL filter for reload URL
        if ( class_exists( 'DYNATAFO_WPML' ) && is_string( $url ) && '' !== $url ) {
            /* translators: Reload page URL being processed for language support */
            $url = DYNATAFO_WPML::apply_language_to_permalink( $url, $post_id );
        }

        if ( ! is_string( $url ) || '' === $url ) {
            $url = home_url( '/' );
            // Apply WPML filter to fallback home URL
            if ( class_exists( 'DYNATAFO_WPML' ) ) {
                /* translators: Fallback home URL being processed for language support */
                $url = DYNATAFO_WPML::apply_language_to_home_url( $url );
            }
        }

        return esc_url_raw( $url );
    }
}