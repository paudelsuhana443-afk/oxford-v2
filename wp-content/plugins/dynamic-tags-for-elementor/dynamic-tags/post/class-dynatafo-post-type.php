<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Type_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-type'; }
    public function get_title() { return esc_html__( 'Post Type (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'format',
            [
                'label'       => esc_html__( 'Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => 'plain',
                'options'     => [
                    'plain'    => esc_html__( 'Plain', 'dynamic-tags-for-elementor' ),
                    'singular' => esc_html__( 'Singular', 'dynamic-tags-for-elementor' ),
                    'plural'   => esc_html__( 'Plural', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function render() {
        $post = get_post();

        if ( ! $post || empty( $post->post_type ) ) {
            return;
        }

        $post_type_object = get_post_type_object( $post->post_type );

        switch ( $this->get_settings( 'format' ) ) {
            case 'singular':
                echo ( $post_type_object && isset( $post_type_object->labels->singular_name ) )
                    ? esc_html( $post_type_object->labels->singular_name )
                    : '';
                break;
            case 'plural':
                echo ( $post_type_object && isset( $post_type_object->labels->name ) )
                    ? esc_html( $post_type_object->labels->name )
                    : '';
                break;
            case 'plain':
            default:
                echo esc_html( $post->post_type );
                break;
        }
    }

    public function get_panel_template_setting_key() {
        return 'format';
    }
}