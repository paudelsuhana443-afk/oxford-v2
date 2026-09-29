<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_User_Author_Image_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-user-author-image-url'; }
    public function get_title() { return esc_html__( 'User/Author Image URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text', 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'author_or_user',
            [
                'label'       => esc_html__( 'Author or User', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => 'user',
                'options'     => [
                    'user'   => esc_html__( 'User', 'dynamic-tags-for-elementor' ),
                    'author' => esc_html__( 'Author', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $source  = $this->get_settings( 'author_or_user' );
        $user_id = 'user' === $source ? get_current_user_id() : (int) get_post_field( 'post_author', get_the_ID() );

        if ( empty( $user_id ) ) {
            return '';
        }

        $image_data = get_avatar_data(
            $user_id,
            [
                'default' => '404',
            ]
        );

        if ( empty( $image_data ) || empty( $image_data['url'] ) ) {
            return '';
        }

        $response = wp_remote_head( $image_data['url'] );

        if ( is_wp_error( $response ) || ( isset( $response['response']['code'] ) && 404 === (int) $response['response']['code'] ) ) {
            return '';
        }

        return esc_url_raw( $image_data['url'] );
    }

    public function get_panel_template_setting_key() {
        return 'author_or_user';
    }
}