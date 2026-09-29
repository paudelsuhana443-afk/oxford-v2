<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Excerpt_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'dynatafo-post-excerpt'; }
    public function get_title() { return esc_html__( 'Post Excerpt (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo wp_kses_post(get_the_excerpt());
    }
}