<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Modified_Date_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-modified-date'; }
    public function get_title() { return esc_html__( 'Post Modified Date (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
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
        $post = get_post();

        if ( ! $post ) {
            return;
        }

        $timestamp = get_post_modified_time( 'U', true, $post );

        if ( empty( $timestamp ) ) {
            return;
        }

        $format = $this->get_settings( 'date_format' );
        $format = ! empty( $format ) ? $format : get_option( 'date_format' );

        echo esc_html( wp_date( $format, $timestamp ) );
    }
}