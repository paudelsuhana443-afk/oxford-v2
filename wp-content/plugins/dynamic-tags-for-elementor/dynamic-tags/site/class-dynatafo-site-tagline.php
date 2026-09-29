<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Site_Tagline_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'dynatafo-site-tagline'; }
    public function get_title() { return esc_html__( 'Site Tagline (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo wp_kses_post(get_bloginfo( 'description' ));
    }
}