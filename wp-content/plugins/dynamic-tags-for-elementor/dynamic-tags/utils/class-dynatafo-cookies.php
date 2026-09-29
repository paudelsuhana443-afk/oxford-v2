<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Cookies_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-cookies'; }
    public function get_title() { return esc_html__( 'Cookies (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'cookie_name',
            [
                'label'       => esc_html__( 'Cookie Name', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => '',
                'options'     => $this->get_cookie_names(),
            ]
        );

        $this->add_control(
            'custom_cookie_name',
            [
                'label'       => esc_html__( 'Custom Cookie Name', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => '',
            ]
        );
    }

    public function render() {
        $custom_key = $this->get_settings( 'custom_cookie_name' );
        $key        = ! empty( $custom_key ) ? $custom_key : $this->get_settings( 'cookie_name' );

        if ( empty( $key ) ) {
            return;
        }

        $value = $this->get_cookie_value( $key );

        if ( is_array( $value ) ) {
            $value = implode( ', ', $value );
        }

        if ( ! is_string( $value ) ) {
            $value = '';
        }

        echo wp_kses_post( $value );
    }

    public function get_panel_template_setting_key() {
        return 'cookie_name';
    }

    private function get_cookie_value( $pattern ) {
        if ( empty( $pattern ) ) {
            return '';
        }

        // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        if ( strpos( $pattern, '*' ) !== false ) {
            $regex = '/^' . str_replace( '\*', '.*', preg_quote( $pattern, '/' ) ) . '$/i';
            foreach ( $_COOKIE as $cookie_key => $cookie_value ) {
                if ( preg_match( $regex, (string) $cookie_key ) ) {
                    return wp_unslash( $cookie_value );
                }
            }
            return '';
        }

        return isset( $_COOKIE[ $pattern ] ) ? wp_unslash( $_COOKIE[ $pattern ] ) : '';
        // phpcs:enable
    }

    private function get_cookie_names() {
        $names = [
            '' => esc_html__( 'Select a Cookie...', 'dynamic-tags-for-elementor' ),
            'wordpress_logged_in_*' => esc_html__( 'WordPress Logged In (wordpress_logged_in_*)', 'dynamic-tags-for-elementor' ),
            'wordpress_logout_*'    => esc_html__( 'WordPress Logout (wordpress_logout_*)', 'dynamic-tags-for-elementor' ),
            'wordpress_sec_*'       => esc_html__( 'WordPress Secure Auth (wordpress_sec_*)', 'dynamic-tags-for-elementor' ),
            'wordpress_*'           => esc_html__( 'WordPress Auth (wordpress_*)', 'dynamic-tags-for-elementor' ),
            'wordpress_postpass_*'  => esc_html__( 'WordPress Post Password (wordpress_postpass_*)', 'dynamic-tags-for-elementor' ),
            'wp-settings-*'         => esc_html__( 'WP Settings (wp-settings-*)', 'dynamic-tags-for-elementor' ),
            'wp-settings-time-*'    => esc_html__( 'WP Settings Time (wp-settings-time-*)', 'dynamic-tags-for-elementor' ),
        ];

        foreach ( $_COOKIE as $key => $value ) {
            $safe_key = esc_attr( $key );
            if ( ! isset( $names[ $safe_key ] ) ) {
                $names[ $safe_key ] = $safe_key;
            }
        }

        return $names;
    }
}