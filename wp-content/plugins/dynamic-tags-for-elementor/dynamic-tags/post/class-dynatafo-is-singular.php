<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_Singular_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-singular'; }
    public function get_title() { return esc_html__( 'Is Singular (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo is_singular() ? '1' : '0';
    }
}