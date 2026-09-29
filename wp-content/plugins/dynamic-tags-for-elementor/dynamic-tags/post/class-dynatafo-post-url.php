<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Post_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-post-url'; }
    public function get_title() { return esc_html__( 'Post URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'url' ]; }

    public function get_value( array $options = [] ) {
        $post_id = get_the_ID();
        $url = get_permalink( $post_id );

        // Apply WPML language-aware permalink filter if WPML is active
        if ( class_exists( 'DYNATAFO_WPML' ) ) {
            /* translators: Post URL being processed for language-aware links */
            $url = DYNATAFO_WPML::apply_language_to_permalink( $url, $post_id );
        }

        return $url;
    }
}