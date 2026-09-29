<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Are_Comments_Allowed_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-are-comments-allowed'; }
    public function get_title() { return esc_html__( 'Are Comments Allowed (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo comments_open() ? '1' : '0';
    }
}