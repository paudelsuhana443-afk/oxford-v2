<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_ACF_Choice_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-acf-choice'; }
        public function get_title() { return esc_html__( 'ACF Choice (DTE)', 'dynamic-tags-for-elementor' ); }
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
                'output',
                [
                    'label'   => esc_html__( 'Output', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'label_or_value',
                    'options' => [
                        'label_or_value' => esc_html__( 'Label (fallback to Value)', 'dynamic-tags-for-elementor' ),
                        'value'          => esc_html__( 'Value', 'dynamic-tags-for-elementor' ),
                        'raw'            => esc_html__( 'Raw JSON', 'dynamic-tags-for-elementor' ),
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
        }

        private function normalize_item( $item, $output ) {
            if ( 'raw' === $output ) {
                if ( is_scalar( $item ) ) {
                    return (string) $item;
                }

                return wp_json_encode( $item );
            }

            if ( is_array( $item ) ) {
                if ( 'value' === $output ) {
                    if ( isset( $item['value'] ) ) {
                        return (string) $item['value'];
                    }

                    if ( isset( $item['ID'] ) ) {
                        return (string) $item['ID'];
                    }
                }

                if ( isset( $item['label'] ) ) {
                    return (string) $item['label'];
                }

                if ( isset( $item['name'] ) ) {
                    return (string) $item['name'];
                }

                if ( isset( $item['title'] ) ) {
                    return (string) $item['title'];
                }

                if ( isset( $item['value'] ) ) {
                    return (string) $item['value'];
                }

                return wp_json_encode( $item );
            }

            return (string) $item;
        }

        public function render() {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return;
            }

            $value     = dynatafo_get_cached_field( $field_name );
            $output    = $this->get_settings( 'output' );
            $separator = $this->get_settings( 'separator' );

            if ( null === $value || '' === $value || [] === $value ) {
                return;
            }

            if ( is_array( $value ) ) {
                $items = [];

                foreach ( $value as $item ) {
                    $normalized = $this->normalize_item( $item, $output );

                    if ( '' !== $normalized ) {
                        $items[] = $normalized;
                    }
                }

                if ( empty( $items ) ) {
                    return;
                }

                echo esc_html( implode( $separator, $items ) );
                return;
            }

            echo esc_html( $this->normalize_item( $value, $output ) );
        }
    }
}
