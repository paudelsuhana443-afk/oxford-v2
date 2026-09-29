<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Author_Info_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-author-info'; }
    public function get_title() { return esc_html__( 'Author Info (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text', 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'field',
            [
                'label'   => esc_html__( 'Field', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'description',
                'options' => [
                    'display_name' => esc_html__( 'Display Name', 'dynamic-tags-for-elementor' ),
                    'description'  => esc_html__( 'Bio', 'dynamic-tags-for-elementor' ),
                    'user_email'   => esc_html__( 'Email', 'dynamic-tags-for-elementor' ),
                    'user_url'     => esc_html__( 'Website', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function render() {
        $field = sanitize_key( (string) $this->get_settings( 'field' ) );
        $user  = null;

        if ( is_author() ) {
            $obj = get_queried_object();
            $user = $obj instanceof \WP_User ? $obj : null;
        }

        if ( ! $user ) {
            $post = get_post();

            if ( $post instanceof \WP_Post ) {
                $user = get_userdata( (int) $post->post_author );
            }
        }

        if ( ! $user ) {
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