<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_Link_Text_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-scf-link-text'; }
        public function get_title() { return esc_html__( 'SCF Link Text (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
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
                'fallback_to_url',
                [
                    'label'        => esc_html__( 'Fallback To URL', 'dynamic-tags-for-elementor' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default'      => 'no',
                ]
            );
        }

        private function extract_text( $value, $fallback_to_url ) {
            if ( is_array( $value ) ) {
                foreach ( [ 'title', 'label', 'text' ] as $key ) {
                    if ( isset( $value[ $key ] ) && is_scalar( $value[ $key ] ) ) {
                        return (string) $value[ $key ];
                    }
                }

                if ( 'yes' === $fallback_to_url && isset( $value['url'] ) && is_scalar( $value['url'] ) ) {
                    return (string) $value['url'];
                }

                if ( 'yes' === $fallback_to_url && isset( $value['link'] ) && is_scalar( $value['link'] ) ) {
                    return (string) $value['link'];
                }
            }

            if ( is_string( $value ) ) {
                return $value;
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

            $text = $this->extract_text( $value, $this->get_settings( 'fallback_to_url' ) );

            if ( '' === $text ) {
                return;
            }

            echo esc_html( $text );
        }
    }
}
