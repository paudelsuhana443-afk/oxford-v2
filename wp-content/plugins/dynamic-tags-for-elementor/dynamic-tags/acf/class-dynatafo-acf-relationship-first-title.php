<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_ACF_Relationship_First_Title_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-acf-relationship-first-title'; }
        public function get_title() { return esc_html__( 'ACF Relationship First Post Title (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-acf-group'; }
        public function get_categories() { return [ 'text' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Relationship Field Name', 'dynamic-tags-for-elementor' ),
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

            $first_post_id = 0;

            if ( is_array( $value ) ) {
                $first_item = reset( $value );

                if ( is_object( $first_item ) && ! empty( $first_item->ID ) ) {
                    $first_post_id = (int) $first_item->ID;
                } elseif ( is_numeric( $first_item ) ) {
                    $first_post_id = (int) $first_item;
                }
            } elseif ( is_object( $value ) && ! empty( $value->ID ) ) {
                $first_post_id = (int) $value->ID;
            } elseif ( is_numeric( $value ) ) {
                $first_post_id = (int) $value;
            }

            if ( empty( $first_post_id ) ) {
                return;
            }

            $title = get_the_title( $first_post_id );

            if ( empty( $title ) ) {
                return;
            }

            echo esc_html( $title );
        }
    }
}