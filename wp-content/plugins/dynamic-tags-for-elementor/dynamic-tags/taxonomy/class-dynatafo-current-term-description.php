<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Current_Term_Description_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-current-term-description'; }
    public function get_title() { return esc_html__( 'Current Term Description (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-taxonomy-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        $term = get_queried_object();

        if ( ! ( $term instanceof \WP_Term ) ) {
            return;
        }

        echo esc_html( wp_strip_all_tags( term_description( $term ) ) );
    }
}