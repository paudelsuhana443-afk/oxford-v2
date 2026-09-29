<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_User_Email_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-user-email'; }
    public function get_title() { return esc_html__( 'Current User Email (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        $user = wp_get_current_user();

        if ( ! $user || empty( $user->user_email ) ) {
            return;
        }

        echo esc_html( $user->user_email );
    }
}