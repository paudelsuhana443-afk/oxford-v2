<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_Author_Of_Post_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-author-of-post'; }
    public function get_title() { return esc_html__( 'Is Current User Author Of Post (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        $user_id   = get_current_user_id();
        $author_id = (int) get_the_author_meta( 'ID' );

        echo ( (int) $user_id === $author_id ) ? '1' : '0';
    }
}