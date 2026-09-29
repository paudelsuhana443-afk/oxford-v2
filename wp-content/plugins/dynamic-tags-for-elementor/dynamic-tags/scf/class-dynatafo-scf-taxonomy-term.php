<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_Taxonomy_Term_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-scf-taxonomy-term'; }
        public function get_title() { return esc_html__( 'SCF Taxonomy Term Name (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
        public function get_categories() { return [ 'text' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Taxonomy Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );
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

            $term_name = '';

            if ( is_array( $value ) ) {
                $first_item = reset( $value );

                if ( is_object( $first_item ) && isset( $first_item->name ) ) {
                    $term_name = $first_item->name;
                } elseif ( is_numeric( $first_item ) ) {
                    $term = get_term( (int) $first_item );
                    if ( $term && ! is_wp_error( $term ) ) {
                        $term_name = $term->name;
                    }
                }
            } elseif ( is_object( $value ) && isset( $value->name ) ) {
                $term_name = $value->name;
            } elseif ( is_numeric( $value ) ) {
                $term = get_term( (int) $value );
                if ( $term && ! is_wp_error( $term ) ) {
                    $term_name = $term->name;
                }
            }

            if ( '' === $term_name ) {
                return;
            }

            echo esc_html( $term_name );
        }
    }
}
