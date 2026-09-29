<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Number_Posts_Query_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-number-posts-query'; }
    public function get_title() { return esc_html__( 'Number Posts Query (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text', 'number' ]; }

    protected function register_controls() {
        $this->add_control(
            'category',
            [
                'label'       => esc_html__( 'Category', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'default'     => [],
                'options'     => $this->get_categories_options(),
                'multiple'    => true,
            ]
        );

        $this->add_control(
            'posttypes',
            [
                'label'       => esc_html__( 'Post Types', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'default'     => [],
                'options'     => get_post_types(),
                'multiple'    => true,
            ]
        );

        $this->add_control(
            'custom_query',
            [
                'label'       => esc_html__( 'Custom Query', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
                'default'     => '',
                'description' => esc_html__( 'One per line, example:', 'dynamic-tags-for-elementor' ) . '<br />post_parent=17<br />post_type=page',
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $args     = [
            'category'       => ! empty( $settings['category'] ) ? implode( ',', $settings['category'] ) : '',
            'post_type'      => empty( $settings['posttypes'] ) ? 'any' : $settings['posttypes'],
            'numberposts'    => -1,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        if ( ! empty( $settings['custom_query'] ) ) {
            $lines = explode( "\n", $settings['custom_query'] );

            foreach ( $lines as $line ) {
                $parts = explode( '=', trim( $line ), 2 );

                if ( 2 !== count( $parts ) ) {
                    continue;
                }

                $args[ trim( $parts[0] ) ] = trim( $parts[1] );
            }
        }

        $posts = get_posts( $args );

        echo esc_html( (string) count( $posts ) );
    }

    private function get_categories_options() {
        $result = [];

        foreach ( get_categories() as $category ) {
            $result[ $category->term_id ] = $category->name;
        }

        return $result;
    }
}