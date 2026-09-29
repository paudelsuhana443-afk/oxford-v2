<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_User_Reg_Date_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-user-reg-date'; }
    public function get_title() { return esc_html__( 'Current User Registration Date (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'date_format',
            [
                'label'       => esc_html__( 'Date Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => get_option( 'date_format' ),
            ]
        );
    }

    public function render() {
        $user = wp_get_current_user();

        if ( ! $user || 0 === (int) $user->ID || empty( $user->user_registered ) ) {
            return;
        }

        $timestamp = strtotime( (string) $user->user_registered );

        if ( ! $timestamp ) {
            return;
        }

        $format = $this->get_settings( 'date_format' );
        $format = ! empty( $format ) ? $format : get_option( 'date_format' );

        echo esc_html( wp_date( $format, $timestamp ) );
    }
}