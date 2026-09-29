<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Comment_Count_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-comment-count'; }
    public function get_title() { return esc_html__( 'Post Comment Count (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text', 'number' ]; }

    public function render() {
        $post = get_post();

        if ( ! $post ) {
            echo '0';
            return;
        }

        echo esc_html( (string) get_comments_number( $post ) );
    }
}