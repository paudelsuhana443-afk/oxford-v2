<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Author_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-author-url'; }
    public function get_title() { return esc_html__( 'Author URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'url' ]; }
    public function get_panel_template_setting_key() { return 'url'; }

    protected function register_controls() {
        $this->add_control(
            'url',
            [
                'label'   => esc_html__( 'URL', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'archive',
                'options' => [
                    'archive' => esc_html__( 'Author Archive', 'dynamic-tags-for-elementor' ),
                    'website' => esc_html__( 'Author Website', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function get_value( array $options = [] ) {
        if ( 'website' === $this->get_settings( 'url' ) ) {
            return (string) get_the_author_meta( 'url' );
        }

        global $authordata;

        if ( $authordata ) {
            $author_url = get_author_posts_url( $authordata->ID, $authordata->user_nicename );
            
            // Apply WPML language-aware URL filter for author archives
            if ( class_exists( 'DYNATAFO_WPML' ) ) {
                /* translators: Author archive URL being processed for language support */
                $author_url = DYNATAFO_WPML::apply_language_to_author_url( $author_url, $authordata->ID );
            }
            
            return (string) $author_url;
        }

        $post = get_post();

        if ( $post instanceof \WP_Post ) {
            $author_url = get_author_posts_url( (int) $post->post_author );
            
            // Apply WPML language-aware URL filter for author archives
            if ( class_exists( 'DYNATAFO_WPML' ) ) {
                /* translators: Author archive URL being processed for language support */
                $author_url = DYNATAFO_WPML::apply_language_to_author_url( $author_url, (int) $post->post_author );
            }
            
            return (string) $author_url;
        }

        return '';
    }
}