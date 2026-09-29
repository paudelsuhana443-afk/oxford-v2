<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Session_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-session'; }
    public function get_title() { return esc_html__( 'Session (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'session_key',
            [
                'label'       => esc_html__( 'Session Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '',
            ]
        );

        $this->add_control(
            'custom_session_key',
            [
                'label'       => esc_html__( 'Custom Session Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '',
            ]
        );
    }

    public function render() {
        if ( PHP_SESSION_ACTIVE !== session_status() || empty( $_SESSION ) ) {
            return;
        }

        $custom_key = $this->get_settings( 'custom_session_key' );

        if ( ! empty( $custom_key ) ) {
            $value = filter_var( $_SESSION[ $custom_key ] ?? '' );
            echo wp_kses_post( $value );
            return;
        }

        $key = $this->get_settings( 'session_key' );

        if ( empty( $key ) || empty( $_SESSION[ $key ] ) ) {
            return;
        }

        $value = filter_var( $_SESSION[ $key ] );
        echo wp_kses_post( $value );
    }

    public function get_panel_template_setting_key() {
        return 'session_key';
    }
}