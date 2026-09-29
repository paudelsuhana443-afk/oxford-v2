<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_Post_In_Category_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-post-in-category'; }
    public function get_title() { return esc_html__( 'Is Post In Category (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'categories',
            [
                'label'       => esc_html__( 'Categories', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'default'     => [],
                'options'     => $this->get_categories_options(),
                'multiple'    => true,
            ]
        );
    }

    public function get_panel_template_setting_key() {
        return 'categories';
    }

    public function render() {
        $settings          = $this->get_settings();
        $category_options  = $this->get_categories_options();
        $query_categories  = [];

        if ( empty( $settings['categories'] ) || ! is_array( $settings['categories'] ) ) {
            echo '0';
            return;
        }

        foreach ( $settings['categories'] as $term_id ) {
            if ( isset( $category_options[ $term_id ] ) ) {
                $query_categories[] = $category_options[ $term_id ];
            }
        }

        echo in_category( $query_categories ) ? '1' : '0';
    }

    private function get_categories_options() {
        $category_names = [];

        $categories = get_categories(
            [
                'hide_empty' => false,
                'type'       => get_post_types(),
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]
        );

        foreach ( $categories as $category ) {
            $category_names[ $category->term_id ] = $category->name;
        }

        return $category_names;
    }
}