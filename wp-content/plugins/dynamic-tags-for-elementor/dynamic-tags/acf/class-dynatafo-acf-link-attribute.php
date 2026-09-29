<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_ACF_Link_Attribute_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-acf-link-attribute'; }
        public function get_title() { return esc_html__( 'ACF Link Attribute (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-acf-group'; }
        public function get_categories() { return [ 'text' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Link Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );

            $this->add_control(
                'attribute',
                [
                    'label'   => esc_html__( 'Attribute', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'title',
                    'options' => [
                        'title'  => esc_html__( 'Title', 'dynamic-tags-for-elementor' ),
                        'target' => esc_html__( 'Target', 'dynamic-tags-for-elementor' ),
                        'rel'    => esc_html__( 'Rel', 'dynamic-tags-for-elementor' ),
                    ],
                ]
            );
        }

        private function get_attribute_value( $value, $attribute ) {
            if ( ! is_array( $value ) ) {
                return '';
            }

            if ( isset( $value[ $attribute ] ) && is_scalar( $value[ $attribute ] ) ) {
                return (string) $value[ $attribute ];
            }

            if ( 'title' === $attribute ) {
                if ( isset( $value['label'] ) && is_scalar( $value['label'] ) ) {
                    return (string) $value['label'];
                }

                if ( isset( $value['text'] ) && is_scalar( $value['text'] ) ) {
                    return (string) $value['text'];
                }
            }

            return '';
        }

        public function render() {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return;
            }

            $value = dynatafo_get_cached_field( $field_name );

            if ( empty( $value ) ) {
                return;
            }

            $attribute = $this->get_settings( 'attribute' );
            $output    = $this->get_attribute_value( $value, $attribute );

            if ( '' === $output ) {
                return;
            }

            echo esc_html( $output );
        }
    }
}
