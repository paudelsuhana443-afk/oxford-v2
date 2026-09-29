<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Comments_Number_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-comments-number'; }
    public function get_title() { return esc_html__( 'Comments Number (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-comments-group'; }
    public function get_categories() { return [ 'text', 'number' ]; }

    public function render() {
        echo esc_html( (string) get_comments_number() );
    }
}