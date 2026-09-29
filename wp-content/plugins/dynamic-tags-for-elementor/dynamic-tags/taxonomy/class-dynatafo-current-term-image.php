<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_Term_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-current-term-image'; }
    public function get_title() { return esc_html__( 'Current Term Image (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-taxonomy-group'; }
    public function get_categories() { return [ 'image' ]; }

    protected function register_controls() {
        $this->add_control(
            'image_meta_key',
            [
                'label'       => esc_html__( 'Image Meta Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'thumbnail_id',
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $term = get_queried_object();

        if ( ! ( $term instanceof \WP_Term ) ) {
            return [];
        }

        $meta_key = $this->get_settings( 'image_meta_key' );
        $meta_key = ! empty( $meta_key ) ? sanitize_key( $meta_key ) : 'thumbnail_id';

        $image_meta = get_term_meta( $term->term_id, $meta_key, true );

        if ( empty( $image_meta ) && 'thumbnail_id' !== $meta_key ) {
            $image_meta = get_term_meta( $term->term_id, 'thumbnail_id', true );
        }

        if ( is_numeric( $image_meta ) ) {
            $image_id = (int) $image_meta;
            $url      = wp_get_attachment_url( $image_id );

            if ( empty( $url ) ) {
                return [];
            }

            return [
                'id'  => $image_id,
                'url' => $url,
            ];
        }

        if ( is_string( $image_meta ) && ! empty( $image_meta ) ) {
            return [
                'id'  => 0,
                'url' => esc_url_raw( $image_meta ),
            ];
        }

        return [];
    }
}