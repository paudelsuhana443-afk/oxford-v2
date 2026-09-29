<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_Image_Data_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

        public function get_name() { return 'dynatafo-scf-image-data'; }
        public function get_title() { return esc_html__( 'SCF Image Data (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
        public function get_categories() { return [ 'image' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Image Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );

            $this->add_control(
                'allow_gallery_first',
                [
                    'label'        => esc_html__( 'Allow First Gallery Item', 'dynamic-tags-for-elementor' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default'      => 'yes',
                ]
            );
        }

        private function normalize_image( $value ) {
            if ( is_numeric( $value ) ) {
                $image_id = (int) $value;
                $url      = wp_get_attachment_url( $image_id );

                if ( ! $url ) {
                    return [];
                }

                return [
                    'id'  => $image_id,
                    'url' => $url,
                ];
            }

            if ( is_array( $value ) ) {
                $image_id = 0;
                $url      = '';

                if ( isset( $value['ID'] ) ) {
                    $image_id = (int) $value['ID'];
                } elseif ( isset( $value['id'] ) ) {
                    $image_id = (int) $value['id'];
                }

                if ( isset( $value['url'] ) && is_string( $value['url'] ) ) {
                    $url = esc_url_raw( $value['url'] );
                } elseif ( $image_id > 0 ) {
                    $url = wp_get_attachment_url( $image_id );
                }

                if ( ! $url ) {
                    return [];
                }

                return [
                    'id'  => $image_id,
                    'url' => $url,
                ];
            }

            return [];
        }

        public function get_value( array $options = [] ) {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return [];
            }

            $value = dynatafo_get_cached_field( $field_name );

            if ( null === $value || '' === $value || [] === $value ) {
                return [];
            }

            $normalized = $this->normalize_image( $value );

            if ( ! empty( $normalized ) ) {
                return $normalized;
            }

            if ( 'yes' === $this->get_settings( 'allow_gallery_first' ) && is_array( $value ) ) {
                $first = reset( $value );

                if ( false !== $first ) {
                    return $this->normalize_image( $first );
                }
            }

            return [];
        }
    }
}
