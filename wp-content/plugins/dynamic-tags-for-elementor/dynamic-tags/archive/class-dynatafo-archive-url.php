<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Archive_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-archive-url'; }
    public function get_title() { return esc_html__( 'Archive URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-archive-group'; }
    public function get_categories() { return [ 'url' ]; }

    public function get_value( array $options = [] ) {
        if ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();

            if ( $term instanceof \WP_Term ) {
                $term_link = get_term_link( $term );

                if ( ! is_wp_error( $term_link ) ) {
                    // Apply WPML language-aware URL filter for taxonomy terms
                    if ( class_exists( 'DYNATAFO_WPML' ) ) {
                        /* translators: Taxonomy term URL being processed for language support */
                        $term_link = DYNATAFO_WPML::apply_language_to_term_link( $term_link, $term->term_id, $term->taxonomy );
                    }
                    return esc_url_raw( $term_link );
                }
            }
        }

        if ( is_author() ) {
            $author = get_queried_object();

            if ( $author instanceof \WP_User ) {
                $author_url = get_author_posts_url( $author->ID );
                
                // Apply WPML language-aware URL filter for author archives
                if ( class_exists( 'DYNATAFO_WPML' ) ) {
                    /* translators: Author archive URL being processed for language support */
                    $author_url = DYNATAFO_WPML::apply_language_to_author_url( $author_url, $author->ID );
                }
                return esc_url_raw( $author_url );
            }
        }

        $post_type = get_post_type();

        if ( is_string( $post_type ) && '' !== $post_type ) {
            $archive_url = get_post_type_archive_link( $post_type );

            if ( is_string( $archive_url ) && '' !== $archive_url ) {
                // Apply WPML language-aware URL filter for post type archives
                if ( class_exists( 'DYNATAFO_WPML' ) ) {
                    /* translators: Post type archive URL being processed for language support */
                    $archive_url = DYNATAFO_WPML::apply_language_to_archive_url( $archive_url );
                }
                return esc_url_raw( $archive_url );
            }
        }

        $fallback_url = get_post_type_archive_link( 'post' ) ?: home_url( '/' );
        
        // Apply WPML language filter to fallback URL
        if ( class_exists( 'DYNATAFO_WPML' ) ) {
            /* translators: Fallback archive URL being processed for language support */
            $fallback_url = DYNATAFO_WPML::apply_language_to_archive_url( $fallback_url );
        }
        
        return esc_url_raw( $fallback_url );
    }
}