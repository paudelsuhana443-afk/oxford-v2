<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_Link_URL_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-scf-link-url'; }
        public function get_title() { return esc_html__( 'SCF Link URL (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
        public function get_categories() { return [ 'url' ]; }

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
                'fallback_to_text_url',
                [
                    'label'        => esc_html__( 'Use Raw URL If Text', 'dynamic-tags-for-elementor' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default'      => 'yes',
                ]
            );
        }

        private function extract_url( $value, $fallback_to_text_url ) {
            if ( is_array( $value ) ) {
                if ( isset( $value['url'] ) && is_string( $value['url'] ) ) {
                    return esc_url_raw( $value['url'] );
                }

                if ( isset( $value['link'] ) && is_string( $value['link'] ) ) {
                    return esc_url_raw( $value['link'] );
                }

                if ( isset( $value['value'] ) && is_string( $value['value'] ) ) {
                    return esc_url_raw( $value['value'] );
                }
            }

            if ( 'yes' === $fallback_to_text_url && is_string( $value ) ) {
                return esc_url_raw( $value );
            }

            return '';
        }

        public function render() {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return;
            }

            $value = dynatafo_get_cached_field( $field_name );

            if ( null === $value || '' === $value || [] === $value ) {
                return;
            }

            $url = $this->extract_url( $value, $this->get_settings( 'fallback_to_text_url' ) );

            if ( '' === $url ) {
                return;
            }

            echo esc_url( $url );
        }
    }
}
