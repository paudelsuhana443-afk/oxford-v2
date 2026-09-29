<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_User_Display_Name_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-user-display-name'; }
    public function get_title() { return esc_html__( 'Current User Display Name (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        $user = wp_get_current_user();

        if ( ! $user || 0 === (int) $user->ID ) {
            return;
        }

        echo esc_html( $user->display_name );
    }
}