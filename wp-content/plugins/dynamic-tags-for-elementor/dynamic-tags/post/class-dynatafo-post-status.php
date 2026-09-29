<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Status_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-status'; }
    public function get_title() { return esc_html__( 'Post Status (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        $post = get_post();

        if ( ! $post ) {
            return;
        }

        echo esc_html( get_post_status( $post->ID ) );
    }
}