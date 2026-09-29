<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Custom_Field_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-custom-field'; }
    public function get_title() { return esc_html__( 'Post Custom Field (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text', 'number', 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'meta_key',
            [
                'label'       => esc_html__( 'Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
    }

    public function render() {
        $meta_key = sanitize_key( (string) $this->get_settings( 'meta_key' ) );

        if ( '' === $meta_key ) {
            return;
        }

        $value = get_post_meta( get_the_ID(), $meta_key, true );

        if ( is_scalar( $value ) ) {
            echo wp_kses_post( (string) $value );
        }
    }
}