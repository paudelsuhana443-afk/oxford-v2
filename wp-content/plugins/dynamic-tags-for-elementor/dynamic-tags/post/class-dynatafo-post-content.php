<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Content_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-content'; }
    public function get_title() { return esc_html__( 'Post Content (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'without_filters',
            [
                'label' => esc_html__( 'Without filters', 'dynamic-tags-for-elementor' ),
                'type'  => \Elementor\Controls_Manager::SWITCHER,
            ]
        );
    }

    public function render() {
        $without_filters = $this->get_settings( 'without_filters' );
        $post_content    = get_the_content();

        $content = wp_kses_post( $post_content );

        if ( empty( $without_filters ) ) {
            $has_elementor = class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->frontend );

            if ( $has_elementor ) {
                \Elementor\Plugin::$instance->frontend->remove_content_filter();
            }

            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress filter hook.
            $content = apply_filters( 'the_content', $content );

            if ( $has_elementor ) {
                \Elementor\Plugin::$instance->frontend->add_content_filter();
            }
        }

        if ( class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $content = strip_tags( $content, '<br><p><a>' );
        }

        echo wp_kses_post( $content );
    }
}