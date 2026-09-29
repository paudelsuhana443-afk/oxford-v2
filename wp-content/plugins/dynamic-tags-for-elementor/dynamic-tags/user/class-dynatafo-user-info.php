<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_User_Info_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-user-info'; }
    public function get_title() { return esc_html__( 'User Info (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text', 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'field',
            [
                'label'   => esc_html__( 'Field', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'display_name',
                'options' => [
                    'display_name' => esc_html__( 'Display Name', 'dynamic-tags-for-elementor' ),
                    'user_email'   => esc_html__( 'Email', 'dynamic-tags-for-elementor' ),
                    'user_url'     => esc_html__( 'Website', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function render() {
        $user_id = get_current_user_id();

        if ( ! $user_id ) {
            return;
        }

        $field = sanitize_key( (string) $this->get_settings( 'field' ) );
        $user  = get_user_by( 'id', $user_id );

        if ( ! ( $user instanceof \WP_User ) ) {
            return;
        }

        $value = isset( $user->{$field} ) ? (string) $user->{$field} : '';

        if ( 'user_email' === $field ) {
            echo esc_html( sanitize_email( $value ) );
            return;
        }

        if ( 'user_url' === $field ) {
            echo esc_url_raw( $value );
            return;
        }

        echo wp_kses_post( $value );
    }
}