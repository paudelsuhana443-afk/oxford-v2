<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_ACF_Field_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

        public function get_name() { return 'dynatafo-acf-field'; }
        public function get_title() { return esc_html__( 'ACF Field (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-acf-group'; }
        public function get_categories() { return [ 'text', 'url', 'image', 'number' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );

            $this->add_control(
                'output_format',
                [
                    'label'   => esc_html__( 'Output Format', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'text',
                    'options' => [
                        'text'       => esc_html__( 'Text', 'dynamic-tags-for-elementor' ),
                        'url'        => esc_html__( 'URL', 'dynamic-tags-for-elementor' ),
                        'image_url'  => esc_html__( 'Image URL', 'dynamic-tags-for-elementor' ),
                        'image_data' => esc_html__( 'Image Data', 'dynamic-tags-for-elementor' ),
                        'id'         => esc_html__( 'ID', 'dynamic-tags-for-elementor' ),
                        'raw'        => esc_html__( 'Raw', 'dynamic-tags-for-elementor' ),
                    ],
                ]
            );
        }

        private function normalize_image_data( $value ) {
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
                // If it is a list of items (a gallery), extract the first element
                if ( isset( $value[0] ) ) {
                    $value = $value[0];
                }

                // Check again in case the extracted element is a numeric ID
                if ( is_numeric( $value ) ) {
                    return $this->normalize_image_data( $value );
                }

                if ( is_array( $value ) ) {
                    $image_id = isset( $value['ID'] ) ? (int) $value['ID'] : ( isset( $value['id'] ) ? (int) $value['id'] : 0 );
                    $url      = isset( $value['url'] ) ? esc_url_raw( (string) $value['url'] ) : ( $image_id ? wp_get_attachment_url( $image_id ) : '' );

                    if ( ! $url ) {
                        return [];
                    }

                    return [
                        'id'  => $image_id,
                        'url' => $url,
                    ];
                }
            }

            return [];
        }

        public function get_value( array $options = [] ) {
            $field_name    = $this->get_settings( 'field_name' );
            $output_format = $this->get_settings( 'output_format' );

            if ( empty( $field_name ) ) {
                return 'image_data' === $output_format ? [] : '';
            }

            $value = dynatafo_get_cached_field( $field_name );

            if ( null === $value || '' === $value || [] === $value ) {
                return 'image_data' === $output_format ? [] : '';
            }

            if ( 'image_data' === $output_format ) {
                return $this->normalize_image_data( $value );
            }

            if ( 'image_url' === $output_format ) {
                $image = $this->normalize_image_data( $value );

                return isset( $image['url'] ) ? (string) $image['url'] : '';
            }

            if ( 'url' === $output_format ) {
                if ( is_array( $value ) && isset( $value['url'] ) ) {
                    return esc_url_raw( (string) $value['url'] );
                }

                if ( is_string( $value ) ) {
                    return esc_url_raw( $value );
                }

                return '';
            }

            if ( 'id' === $output_format ) {
                if ( is_numeric( $value ) ) {
                    return (string) ((int) $value);
                }

                if ( is_array( $value ) && isset( $value['ID'] ) ) {
                    return (string) ((int) $value['ID']);
                }

                return '';
            }

            if ( 'raw' === $output_format ) {
                if ( is_scalar( $value ) ) {
                    return (string) $value;
                }

                return wp_json_encode( $value );
            }

            if ( is_scalar( $value ) ) {
                return (string) $value;
            }

            if ( is_array( $value ) ) {
                if ( isset( $value['label'] ) ) {
                    return (string) $value['label'];
                }

                if ( isset( $value['title'] ) ) {
                    return (string) $value['title'];
                }

                return wp_json_encode( $value );
            }

            return '';
        }
    }
}
