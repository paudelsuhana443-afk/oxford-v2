<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_Term_Name_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-term-name'; }
    public function get_title() { return esc_html__( 'Current Term Name (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-taxonomy-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        $term = get_queried_object();

        if ( ! ( $term instanceof \WP_Term ) ) {
            return;
        }

        echo esc_html( $term->name );
    }
}