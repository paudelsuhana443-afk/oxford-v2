<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Author_Name_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'dynatafo-author-name'; }
    public function get_title() { return esc_html__( 'Author Name (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        echo get_the_author();
    }
}