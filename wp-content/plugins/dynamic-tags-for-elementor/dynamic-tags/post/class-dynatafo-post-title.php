<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Title_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'dynatafo-post-title';
    }

    public function get_title() {
        return esc_html__( 'Post Title (DTE)', 'dynamic-tags-for-elementor' );
    }

    public function get_group() {
        return 'dynatafo-post-group'; 
    }

    public function get_categories() {
        return [ 'text' ]; 
    }

    public function render() {
        echo wp_kses_post( get_the_title() );
    }
}