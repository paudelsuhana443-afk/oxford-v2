<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Author_Meta_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-author-meta'; }
    public function get_title() { return esc_html__( 'Author Meta (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text', 'number', 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'meta_key',
            [
                'label'       => esc_html__( 'Meta Key', 'dynamic-tags-for-elementor' ),
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

        $user_id = 0;

        if ( is_author() ) {
            $obj = get_queried_object();
            $user_id = $obj instanceof \WP_User ? (int) $obj->ID : 0;
        }

        if ( 0 === $user_id ) {
            $post = get_post();
            $user_id = $post instanceof \WP_Post ? (int) $post->post_author : 0;
        }

        if ( 0 === $user_id ) {
            return;
        }

        $value = get_user_meta( $user_id, $meta_key, true );

        if ( is_scalar( $value ) ) {
            echo wp_kses_post( (string) $value );
        }
    }
}