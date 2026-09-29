<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Is_Post_In_List_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-is-post-in-list'; }
    public function get_title() { return esc_html__( 'Is Post In List (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'ids',
            [
                'label'       => esc_html__( 'IDs', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'default'     => [],
                'options'     => $this->get_ids_options(),
                'multiple'    => true,
            ]
        );
    }

    public function get_panel_template_setting_key() {
        return 'ids';
    }

    public function render() {
        $settings = $this->get_settings();
        $ids      = [];

        if ( ! empty( $settings['ids'] ) && is_array( $settings['ids'] ) ) {
            foreach ( $settings['ids'] as $id ) {
                $ids[] = (int) $id;
            }
        }

        echo in_array( (int) get_the_ID(), $ids, true ) ? '1' : '0';
    }

    private function get_ids_options() {
        $options  = [];
        $post_ids = get_posts(
            [
                'fields'         => 'ids',
                'posts_per_page' => -1,
            ]
        );

        foreach ( $post_ids as $post_id ) {
            $options[ $post_id ] = (string) $post_id;
        }

        return $options;
    }
}