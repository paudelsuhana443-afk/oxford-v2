<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_ID_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-id'; }
    public function get_title() { return esc_html__( 'Post ID (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text', 'number' ]; }

    public function render() {
        echo esc_html( (string) get_the_ID() );
    }
}