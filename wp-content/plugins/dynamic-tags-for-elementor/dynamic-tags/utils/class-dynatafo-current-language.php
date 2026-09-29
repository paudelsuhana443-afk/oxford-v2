<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Current_Language_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-language'; }
    public function get_title() { return esc_html__( 'Current Language (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        if ( class_exists( 'DYNATAFO_WPML' ) ) {
            /* translators: Current language code being output, language-aware */
            echo esc_html( DYNATAFO_WPML::get_current_language() );
            return;
        }

        // Fallback to WordPress locale if WPML helper not available
        echo esc_html( get_locale() );
    }
}