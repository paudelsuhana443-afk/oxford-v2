<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Comments_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-comments-url'; }
    public function get_title() { return esc_html__( 'Comments URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-comments-group'; }
    public function get_categories() { return [ 'url' ]; }

    public function get_value( array $options = [] ) {
        $post = get_post();

        if ( ! ( $post instanceof \WP_Post ) ) {
            return '';
        }

        return esc_url_raw( get_comments_link( $post ) );
    }
}