<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_Date_Formatted_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-scf-date-formatted'; }
        public function get_title() { return esc_html__( 'SCF Date Picker Formatted (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
        public function get_categories() { return [ 'text' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Date Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );

            $this->add_control(
                'date_format',
                [
                    'label'       => esc_html__( 'Date Format', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => get_option( 'date_format' ),
                    'description' => esc_html__( 'Use PHP date format, e.g. F j, Y', 'dynamic-tags-for-elementor' ),
                ]
            );
        }

        public function render() {
            $field_name  = $this->get_settings( 'field_name' );
            $date_format = $this->get_settings( 'date_format' );

            if ( empty( $field_name ) ) {
                return;
            }

            $raw_value = dynatafo_get_cached_field( $field_name );

            if ( empty( $raw_value ) ) {
                return;
            }

            $timestamp = is_numeric( $raw_value ) ? (int) $raw_value : strtotime( (string) $raw_value );

            if ( ! $timestamp ) {
                return;
            }

            if ( empty( $date_format ) ) {
                $date_format = get_option( 'date_format' );
            }

            echo esc_html( wp_date( $date_format, $timestamp ) );
        }
    }
}
