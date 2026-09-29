<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Shortcode_URL_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-shortcode-url'; }
    public function get_title() { return esc_html__( 'Shortcode (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text', 'number', 'url', 'datetime' ]; }

    protected function register_controls() {
        $this->add_control(
            'shortcode',
            [
                'label'       => esc_html__( 'Shortcode', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
                'placeholder' => '[my_shortcode]',
                'description' => esc_html__( 'Enter any shortcode. Its rendered output will be used as the dynamic value.', 'dynamic-tags-for-elementor' ),
            ]
        );
    }

    public function render() {
        $shortcode = trim( (string) $this->get_settings( 'shortcode' ) );

        if ( '' === $shortcode ) {
            return;
        }

        $value = do_shortcode( $shortcode );
        $should_escape = true;

        $should_escape = apply_filters( 'dynatafo/dynamic_tags/shortcode/should_escape', $should_escape, $shortcode );

        if ( $should_escape ) {
            $value = wp_kses_post( $value );
        }

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value is conditionally sanitized above.
        echo $value;
    }
}