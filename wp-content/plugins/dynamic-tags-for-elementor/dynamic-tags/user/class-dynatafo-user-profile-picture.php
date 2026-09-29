<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_User_Profile_Picture_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-user-profile-picture'; }
    public function get_title() { return esc_html__( 'User Profile Picture (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'url', 'image' ]; }

    public function get_value( array $options = [] ) {
        $user_id = get_current_user_id();

        if ( ! $user_id ) {
            return [
                'id'  => '',
                'url' => '',
            ];
        }

        $avatar = get_avatar_url( $user_id );

        return [
            'id'  => '',
            'url' => is_string( $avatar ) ? esc_url_raw( $avatar ) : '',
        ];
    }
}