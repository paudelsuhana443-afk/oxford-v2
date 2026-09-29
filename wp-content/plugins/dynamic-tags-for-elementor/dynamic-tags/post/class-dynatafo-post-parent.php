<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Parent_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-post-parent'; }
    public function get_title() { return esc_html__( 'Post Parent (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'output',
            [
                'label'       => esc_html__( 'Output', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => 'id',
                'options'     => [
                    'id'         => 'ID',
                    'post_title' => esc_html__( 'Post Title', 'dynamic-tags-for-elementor' ),
                    'post_name'  => esc_html__( 'Post Name', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function render() {
        $post        = get_post();
        $output_type = $this->get_settings( 'output' );

        if ( ! $post || empty( $post->post_parent ) ) {
            if ( 'id' === $output_type ) {
                echo '0';
            } else {
                echo esc_html__( 'No Parent', 'dynamic-tags-for-elementor' );
            }
            return;
        }

        switch ( $output_type ) {
            case 'post_title':
                echo esc_html( get_the_title( $post->post_parent ) );
                break;
            case 'post_name':
                $parent = get_post( $post->post_parent );
                echo ! empty( $parent ) ? esc_html( $parent->post_name ) : '';
                break;
            case 'id':
            default:
                echo esc_html( $post->post_parent );
                break;
        }
    }

    public function get_panel_template_setting_key() {
        return 'output';
    }
}