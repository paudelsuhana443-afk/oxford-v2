<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Date_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-date'; }
    public function get_title() { return esc_html__( 'Post Date (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text', 'datetime' ]; }

    protected function register_controls() {
        $this->add_control(
            'format',
            [
                'label'       => esc_html__( 'Date Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => get_option( 'date_format' ),
            ]
        );
    }

    public function render() {
        $format = (string) $this->get_settings( 'format' );

        if ( '' === $format ) {
            $format = get_option( 'date_format' );
        }

        echo esc_html( get_the_date( $format ) );
    }
}