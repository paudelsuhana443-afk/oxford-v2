<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_User_ID_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-user-id'; }
    public function get_title() { return esc_html__( 'Current User ID (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text', 'number' ]; }

    public function render() {
        echo esc_html( (string) get_current_user_id() );
    }
}