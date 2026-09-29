<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Archive_Description_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-archive-description'; }
    public function get_title() { return esc_html__( 'Archive Description (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-archive-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo wp_kses_post( get_the_archive_description() );
    }
}