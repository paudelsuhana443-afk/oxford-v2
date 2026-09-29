<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Tags_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-tags'; }
    public function get_title() { return esc_html__( 'Post Tags List (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'separator',
            [
                'label'   => esc_html__( 'Separator', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => ', ',
            ]
        );

        $this->add_control(
            'linked',
            [
                'label'   => esc_html__( 'Include Links', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
    }

    public function render() {
        $separator = $this->get_settings( 'separator' );
        $separator = ( '' !== $separator ) ? $separator : ', ';
        $linked    = ( 'yes' === $this->get_settings( 'linked' ) );

        if ( $linked ) {
            $list = get_the_tag_list( '', $separator, '' );

            if ( empty( $list ) ) {
                return;
            }

            echo wp_kses_post( $list );
            return;
        }

        $tags = get_the_tags();

        if ( empty( $tags ) || is_wp_error( $tags ) ) {
            return;
        }

        $names = [];

        foreach ( $tags as $tag ) {
            if ( ! empty( $tag->name ) ) {
                $names[] = $tag->name;
            }
        }

        if ( empty( $names ) ) {
            return;
        }

        echo esc_html( implode( $separator, $names ) );
    }
}