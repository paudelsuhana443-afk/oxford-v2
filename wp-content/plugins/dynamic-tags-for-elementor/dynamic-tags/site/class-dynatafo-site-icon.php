<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Site_Icon_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-site-icon'; }
    public function get_title() { return esc_html__( 'Site Icon (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-media-group'; }
    public function get_categories() { return [ 'image' ]; }

    public function get_value( array $options = [] ) {
        $icon_id = get_option( 'site_icon' );

        if ( ! $icon_id ) {
            return [];
        }

        $src = wp_get_attachment_image_src( $icon_id, 'full' );

        if ( ! $src || empty( $src[0] ) ) {
            return [];
        }

        return [
            'id'  => $icon_id,
            'url' => $src[0],
        ];
    }
}