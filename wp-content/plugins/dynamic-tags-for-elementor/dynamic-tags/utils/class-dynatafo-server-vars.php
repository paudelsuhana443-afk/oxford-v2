<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Server_Vars_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-server-vars'; }
    public function get_title() { return esc_html__( 'Server Variables (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $options = [];

        foreach ( array_keys( $_SERVER ) as $key ) {
            $safe_key          = esc_attr( $key );
            $options[ $safe_key ] = $safe_key;
        }

        if ( empty( $options['HTTP_REFERER'] ) ) {
            $options['HTTP_REFERER'] = 'HTTP_REFERER';
        }

        $this->add_control(
            'variable',
            [
                'label'       => esc_html__( 'Variable', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => '',
                'options'     => $options,
            ]
        );
    }

    public function get_panel_template_setting_key() {
        return 'variable';
    }

    public function render() {
        $key = $this->get_settings( 'variable' );

        if ( empty( $key ) ) {
            return;
        }

        $server_value = isset( $_SERVER[ $key ] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER[ $key ] ) ) : '';

        echo esc_html( $server_value );
    }
}