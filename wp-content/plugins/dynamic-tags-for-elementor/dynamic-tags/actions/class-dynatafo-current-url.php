<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-current-url'; }
    public function get_title() { return esc_html__( 'Current URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text', 'url' ]; }

    public function get_value( array $options = [] ) {
        $https    = isset( $_SERVER['HTTPS'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['HTTPS'] ) ) : '';
        $is_https = ( ! empty( $https ) && 'off' !== strtolower( $https ) );
        $scheme   = $is_https ? 'https' : 'http';
        $host     = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
        $request  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REQUEST_URI'] ) ) : '/';

        return esc_url_raw( $scheme . '://' . $host . $request );
    }
}