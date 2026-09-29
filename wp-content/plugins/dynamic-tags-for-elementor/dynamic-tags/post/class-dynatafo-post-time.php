<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Time_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-time'; }
    public function get_title() { return esc_html__( 'Post Time (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text', 'datetime' ]; }

    protected function register_controls() {
        $this->add_control(
            'format',
            [
                'label'       => esc_html__( 'Time Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => get_option( 'time_format' ),
            ]
        );
    }

    public function render() {
        $format = (string) $this->get_settings( 'format' );

        if ( '' === $format ) {
            $format = get_option( 'time_format' );
        }

        echo esc_html( get_the_time( $format ) );
    }
}