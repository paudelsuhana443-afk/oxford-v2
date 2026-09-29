<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Terms_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-terms'; }
    public function get_title() { return esc_html__( 'Post Terms (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'taxonomy',
            [
                'label'   => esc_html__( 'Taxonomy', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'category',
                'options' => [
                    'category' => esc_html__( 'Categories', 'dynamic-tags-for-elementor' ),
                    'post_tag' => esc_html__( 'Tags', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'separator',
            [
                'label'   => esc_html__( 'Separator', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => ', ',
            ]
        );
    }

    public function render() {
        $taxonomy  = sanitize_key( (string) $this->get_settings( 'taxonomy' ) );
        $separator = (string) $this->get_settings( 'separator' );

        if ( '' === $taxonomy ) {
            return;
        }

        $terms = get_the_terms( get_the_ID(), $taxonomy );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return;
        }

        $names = array_map(
            static function( $term ) {
                return $term instanceof \WP_Term ? $term->name : '';
            },
            $terms
        );

        $names = array_filter( $names );

        if ( empty( $names ) ) {
            return;
        }

        echo esc_html( implode( $separator, $names ) );
    }
}