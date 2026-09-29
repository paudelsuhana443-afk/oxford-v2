<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_User_Can_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-user-can'; }
    public function get_title() { return esc_html__( 'Current User Can (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'capability',
            [
                'label'       => esc_html__( 'Capability', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'default'     => '',
                'options'     => $this->get_all_capabilities(),
            ]
        );
    }

    public function render() {
        $capability = $this->get_settings( 'capability' );

        if ( empty( $capability ) ) {
            echo '0';
            return;
        }

        echo current_user_can( $capability ) ? '1' : '0';
    }

    public function get_panel_template_setting_key() {
        return 'capability';
    }

    private function get_all_capabilities() {
        global $wp_roles;

        $list = [];

        if ( empty( $wp_roles ) || empty( $wp_roles->roles ) ) {
            return $list;
        }

        foreach ( $wp_roles->roles as $group ) {
            if ( empty( $group['capabilities'] ) ) {
                continue;
            }

            foreach ( $group['capabilities'] as $capability => $is_granted ) {
                if ( isset( $list[ $capability ] ) ) {
                    continue;
                }

                $list[ $capability ] = $capability;
            }
        }

        return $list;
    }
}