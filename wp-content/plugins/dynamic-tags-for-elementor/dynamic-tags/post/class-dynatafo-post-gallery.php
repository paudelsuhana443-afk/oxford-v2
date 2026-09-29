<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Gallery_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-post-gallery'; }
    public function get_title() { return esc_html__( 'Post Gallery (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'gallery' ]; }

    public function get_value( array $options = [] ) {
        $post_id = get_the_ID();

        if ( ! $post_id ) {
            return [];
        }

        $ids = [];
        $gallery = get_post_gallery( $post_id, false );

        if ( is_array( $gallery ) && ! empty( $gallery['ids'] ) ) {
            $ids = array_map( 'absint', explode( ',', (string) $gallery['ids'] ) );
        }

        if ( empty( $ids ) ) {
            $attachments = get_attached_media( 'image', $post_id );
            $ids = array_map(
                static function( $attachment ) {
                    return $attachment instanceof \WP_Post ? (int) $attachment->ID : 0;
                },
                $attachments
            );
        }

        $ids = array_values( array_filter( $ids ) );

        return $ids;
    }
}