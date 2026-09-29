<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_Feed_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-feed'; }
    public function get_title() { return esc_html__( 'Is Feed (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo is_feed() ? '1' : '0';
    }
}