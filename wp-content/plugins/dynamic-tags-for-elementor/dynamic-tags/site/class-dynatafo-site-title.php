<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Site_Title_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'dynatafo-site-title'; }
    public function get_title() { return esc_html__( 'Site Title (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo esc_html(get_bloginfo( 'name' ));
    }
}