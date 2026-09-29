<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_Gallery_First_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

        public function get_name() { return 'dynatafo-scf-gallery-first'; }
        public function get_title() { return esc_html__( 'SCF Gallery First Image (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
        public function get_categories() { return [ 'image' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Gallery Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );
        }

        public function get_value( array $options = [] ) {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return [];
            }

            $value = dynatafo_get_cached_field( $field_name );

            if ( empty( $value ) || ! is_array( $value ) ) {
                return [];
            }

            $first = reset( $value );

            if ( is_numeric( $first ) ) {
                $image_id = (int) $first;
                $url      = wp_get_attachment_url( $image_id );

                if ( empty( $url ) ) {
                    return [];
                }

                return [
                    'id'  => $image_id,
                    'url' => $url,
                ];
            }

            if ( is_array( $first ) ) {
                $image_id = isset( $first['ID'] ) ? (int) $first['ID'] : ( isset( $first['id'] ) ? (int) $first['id'] : 0 );
                $url      = isset( $first['url'] ) ? esc_url_raw( $first['url'] ) : ( $image_id ? wp_get_attachment_url( $image_id ) : '' );

                if ( empty( $url ) ) {
                    return [];
                }

                return [
                    'id'  => $image_id,
                    'url' => $url,
                ];
            }

            return [];
        }
    }
}
