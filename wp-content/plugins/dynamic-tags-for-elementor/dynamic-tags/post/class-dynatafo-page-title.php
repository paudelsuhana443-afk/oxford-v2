<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Page_Title_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-page-title'; }
    public function get_title() { return esc_html__( 'Page Title (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        if ( is_singular() ) {
            echo wp_kses_post( get_the_title() );
            return;
        }

        echo wp_kses_post( wp_get_document_title() );
    }
}