<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_404_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-404'; }
    public function get_title() { return esc_html__( 'Is 404 (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo is_404() ? '1' : '0';
    }
}