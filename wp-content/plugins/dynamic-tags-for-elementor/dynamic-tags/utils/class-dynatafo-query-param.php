<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Query_Param_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-query-param'; }
    public function get_title() { return esc_html__( 'Query String Parameter (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'param_key',
            [
                'label'       => esc_html__( 'Parameter Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '',
                'description' => esc_html__( 'Example: ref, utm_source, category', 'dynamic-tags-for-elementor' ),
            ]
        );
    }

    public function render() {
        $param_key = $this->get_settings( 'param_key' );

        if ( empty( $param_key ) ) {
            return;
        }

        $safe_key = sanitize_key( $param_key );

        if ( empty( $safe_key ) ) {
            return;
        }

        $raw_value = filter_input( INPUT_GET, $safe_key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

        if ( is_array( $raw_value ) ) {
            $clean_value = implode( ',', array_map( 'sanitize_text_field', $raw_value ) );
        } else {
            $raw_value   = filter_input( INPUT_GET, $safe_key, FILTER_UNSAFE_RAW );
            $clean_value = is_string( $raw_value ) ? sanitize_text_field( wp_unslash( $raw_value ) ) : '';
        }

        if ( '' === $clean_value ) {
            return;
        }

        echo esc_html( $clean_value );
    }

    public function get_panel_template_setting_key() {
        return 'param_key';
    }
}