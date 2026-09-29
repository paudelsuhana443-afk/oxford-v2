<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'have_rows' ) && function_exists( 'get_sub_field' ) ) {

    class DYNATAFO_ACF_Repeater_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

        public function get_name() { return 'dynatafo-acf-repeater'; }
        public function get_title() { return esc_html__( 'ACF Repeater (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-acf-group'; }
        public function get_categories() { return [ 'text', 'url', 'image' ]; }

        protected function register_controls() {
            $this->add_control(
                'repeater_field_name',
                [
                    'label'       => esc_html__( 'Repeater Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );

            $this->add_control(
                'sub_field_name',
                [
                    'label'       => esc_html__( 'Sub Field Name', 'dynamic-tags-for-elementor' ),
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
                        'text'  => esc_html__( 'Text', 'dynamic-tags-for-elementor' ),
                        'urls'  => esc_html__( 'URLs', 'dynamic-tags-for-elementor' ),
                        'ids'   => esc_html__( 'IDs', 'dynamic-tags-for-elementor' ),
                        'image' => esc_html__( 'Image URL', 'dynamic-tags-for-elementor' ),
                    ],
                ]
            );

            $this->add_control(
                'separator',
                [
                    'label'   => esc_html__( 'Separator', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::TEXT,
                    'default' => ', ',
                ]
            );

            $this->add_control(
                'max_rows',
                [
                    'label'   => esc_html__( 'Max Rows', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::NUMBER,
                    'default' => '',
                ]
            );

            $this->add_control(
                'row_offset',
                [
                    'label'   => esc_html__( 'Row Offset', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::NUMBER,
                    'default' => 0,
                    'min'     => 0,
                ]
            );
        }

        public function get_value( array $options = [] ) {
            $repeater_field_name = $this->get_settings( 'repeater_field_name' );
            $sub_field_name      = $this->get_settings( 'sub_field_name' );
            $separator           = $this->get_settings( 'separator' );
            $max_rows            = (int) $this->get_settings( 'max_rows' );
            $row_offset          = (int) $this->get_settings( 'row_offset' );

            if ( empty( $repeater_field_name ) || empty( $sub_field_name ) || ! have_rows( $repeater_field_name ) ) {
                return '';
            }

            $index       = 0;
            $taken       = 0;
            $values      = [];
            $output_mode = $this->get_settings( 'output_format' );

            while ( have_rows( $repeater_field_name ) ) {
                the_row();

                if ( $index < $row_offset ) {
                    $index++;
                    continue;
                }

                $value = get_sub_field( $sub_field_name );

                if ( null === $value || '' === $value ) {
                    $index++;
                    continue;
                }

                if ( is_array( $value ) && isset( $value['ID'] ) ) {
                    switch ( $output_mode ) {
                        case 'ids':
                            $values[] = (string) $value['ID'];
                            break;
                        case 'image':
                            $values[] = wp_get_attachment_url( $value['ID'] );
                            break;
                        case 'urls':
                            $values[] = isset( $value['url'] ) ? $value['url'] : wp_get_attachment_url( $value['ID'] );
                            break;
                        case 'text':
                        default:
                            $values[] = isset( $value['url'] ) ? $value['url'] : wp_json_encode( $value );
                            break;
                    }
                } elseif ( is_array( $value ) ) {
                    foreach ( $value as $item ) {
                        if ( is_numeric( $item ) ) {
                            $values[] = 'image' === $output_mode ? wp_get_attachment_url( (int) $item ) : (string) $item;
                        } else {
                            $values[] = (string) $item;
                        }
                    }
                } else {
                    $values[] = (string) $value;
                }

                $index++;
                $taken++;

                if ( $max_rows > 0 && $taken >= $max_rows ) {
                    break;
                }
            }

            if ( empty( $values ) ) {
                return '';
            }

            return wp_kses_post( implode( $separator, $values ) );
        }
    }
}