<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_Date_Time_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-date-time'; }
    public function get_title() { return esc_html__( 'Current Date Time (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text', 'datetime' ]; }

    protected function register_controls() {
        $this->add_control(
            'format',
            [
                'label'       => esc_html__( 'Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
            ]
        );
    }

    public function render() {
        $format = (string) $this->get_settings( 'format' );

        if ( '' === $format ) {
            $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        }

        echo esc_html( wp_date( $format, current_time( 'timestamp' ) ) );
    }
}