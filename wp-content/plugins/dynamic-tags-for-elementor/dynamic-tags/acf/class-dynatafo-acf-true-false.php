<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_ACF_True_False_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-acf-true-false'; }
        public function get_title() { return esc_html__( 'ACF True/False (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-acf-group'; }
        public function get_categories() { return [ 'text' ]; }

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
                'true_label',
                [
                    'label'       => esc_html__( 'True Output', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => esc_html__( 'Yes', 'dynamic-tags-for-elementor' ),
                    'label_block' => true,
                ]
            );

            $this->add_control(
                'false_label',
                [
                    'label'       => esc_html__( 'False Output', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => esc_html__( 'No', 'dynamic-tags-for-elementor' ),
                    'label_block' => true,
                ]
            );
        }

        private function to_bool( $value ) {
            if ( is_bool( $value ) ) {
                return $value;
            }

            if ( is_numeric( $value ) ) {
                return (int) $value === 1;
            }

            if ( is_string( $value ) ) {
                $normalized = strtolower( trim( $value ) );
                return in_array( $normalized, [ '1', 'true', 'yes', 'on' ], true );
            }

            return ! empty( $value );
        }

        public function render() {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return;
            }

            $value      = dynatafo_get_cached_field( $field_name );
            $is_true    = $this->to_bool( $value );
            $true_label = $this->get_settings( 'true_label' );
            $false_label = $this->get_settings( 'false_label' );

            echo esc_html( $is_true ? $true_label : $false_label );
        }
    }
}
