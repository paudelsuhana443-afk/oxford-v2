<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_Home_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-home'; }
    public function get_title() { return esc_html__( 'Is Home (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo is_home() ? '1' : '0';
    }
}