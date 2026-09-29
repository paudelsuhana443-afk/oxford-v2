<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Categories_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-categories'; }
    public function get_title() { return esc_html__( 'Post Categories List (DTE)', 'dynamic-tags-for-elementor' ); }
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
            $list = get_the_category_list( $separator );

            if ( empty( $list ) ) {
                return;
            }

            echo wp_kses_post( $list );
            return;
        }

        $categories = get_the_category();

        if ( empty( $categories ) || is_wp_error( $categories ) ) {
            return;
        }

        $names = [];

        foreach ( $categories as $category ) {
            if ( ! empty( $category->name ) ) {
                $names[] = $category->name;
            }
        }

        if ( empty( $names ) ) {
            return;
        }

        echo esc_html( implode( $separator, $names ) );
    }
}