<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Author_Profile_Picture_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-author-profile-picture'; }
    public function get_title() { return esc_html__( 'Author Profile Picture (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'url', 'image' ]; }

    public function get_value( array $options = [] ) {
        $author_id = 0;

        if ( is_author() ) {
            $obj = get_queried_object();
            $author_id = $obj instanceof \WP_User ? (int) $obj->ID : 0;
        }

        if ( 0 === $author_id ) {
            $post = get_post();
            $author_id = $post instanceof \WP_Post ? (int) $post->post_author : 0;
        }

        if ( 0 === $author_id ) {
            return [
                'id'  => '',
                'url' => '',
            ];
        }

        $avatar = get_avatar_url( $author_id );

        return [
            'id'  => '',
            'url' => is_string( $avatar ) ? esc_url_raw( $avatar ) : '',
        ];
    }
}