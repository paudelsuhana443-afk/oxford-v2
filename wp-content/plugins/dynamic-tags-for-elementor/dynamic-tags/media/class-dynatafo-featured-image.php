<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Featured_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-featured-image'; }
    public function get_title() { return esc_html__( 'Featured Image (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-media-group'; }
    public function get_categories() { return [ 'image' ]; }

    public function get_value( array $options = [] ) {
        $thumbnail_id = get_post_thumbnail_id();

        if ( ! $thumbnail_id ) {
            return [];
        }

        $src = wp_get_attachment_image_src( $thumbnail_id, 'full' );

        return [
            'id'  => $thumbnail_id,
            'url' => $src[0],
        ];
    }
}