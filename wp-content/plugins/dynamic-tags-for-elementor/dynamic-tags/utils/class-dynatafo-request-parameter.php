<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Request_Parameter_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-request-parameter'; }
    public function get_title() { return esc_html__( 'Request Parameter (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'request_type',
            [
                'label'   => esc_html__( 'Type', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'get',
                'options' => [
                    'get'       => esc_html__( 'Get', 'dynamic-tags-for-elementor' ),
                    'post'      => esc_html__( 'Post', 'dynamic-tags-for-elementor' ),
                    'query_var' => esc_html__( 'Query Var', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'param_name',
            [
                'label' => esc_html__( 'Parameter Name', 'dynamic-tags-for-elementor' ),
                'type'  => \Elementor\Controls_Manager::TEXT,
            ]
        );
    }

    public function render() {
        $request_type = strtoupper( (string) $this->get_settings( 'request_type' ) );
        $param_name   = sanitize_key( (string) $this->get_settings( 'param_name' ) );

        if ( '' === $request_type || '' === $param_name ) {
            return;
        }

        $value = '';

        if ( 'POST' === $request_type ) {
            $raw_array = filter_input( INPUT_POST, $param_name, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

            if ( is_array( $raw_array ) ) {
                $value = implode( ',', array_map( 'sanitize_text_field', $raw_array ) );
            } else {
                $raw = filter_input( INPUT_POST, $param_name, FILTER_UNSAFE_RAW );
                $value = is_string( $raw ) ? sanitize_text_field( wp_unslash( $raw ) ) : '';
            }
        } elseif ( 'GET' === $request_type ) {
            $raw_array = filter_input( INPUT_GET, $param_name, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

            if ( is_array( $raw_array ) ) {
                $value = implode( ',', array_map( 'sanitize_text_field', $raw_array ) );
            } else {
                $raw = filter_input( INPUT_GET, $param_name, FILTER_UNSAFE_RAW );
                $value = is_string( $raw ) ? sanitize_text_field( wp_unslash( $raw ) ) : '';
            }
        } elseif ( 'QUERY_VAR' === $request_type ) {
            $raw = get_query_var( $param_name );
            $value = is_scalar( $raw ) ? sanitize_text_field( (string) $raw ) : '';
        }

        if ( '' !== $value ) {
            echo esc_html( $value );
        }
    }
}