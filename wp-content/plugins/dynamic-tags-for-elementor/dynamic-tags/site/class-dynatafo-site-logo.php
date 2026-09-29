<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Site_Logo_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-site-logo'; }
    public function get_title() { return esc_html__( 'Site Logo (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-media-group'; }
    public function get_categories() { return [ 'image' ]; }

    public function get_value( array $options = [] ) {
        $logo_id = get_theme_mod( 'custom_logo' );

        if ( ! $logo_id ) {
            return [];
        }

        $src = wp_get_attachment_image_src( $logo_id, 'full' );

        if ( ! $src || empty( $src[0] ) ) {
            return [];
        }

        return [
            'id'  => $logo_id,
            'url' => $src[0],
        ];
    }
}