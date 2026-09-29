<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Post_Thumbnail_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-post-thumbnail-url'; }
    public function get_title() { return esc_html__( 'Post Thumbnail URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-post-group'; }
    public function get_categories() { return [ 'url', 'image' ]; }

    protected function register_controls() {
        $this->add_control(
            'output_format',
            [
                'label'       => esc_html__( 'Output Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => 'url',
                'options'     => [
                    'url'   => esc_html__( 'URL', 'dynamic-tags-for-elementor' ),
                    'image' => esc_html__( 'Image Object', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $thumbnail_id = get_post_thumbnail_id();

        if ( ! $thumbnail_id ) {
            return '';
        }

        $url = wp_get_attachment_url( $thumbnail_id );

        if ( empty( $url ) ) {
            return '';
        }

        if ( 'image' === $this->get_settings( 'output_format' ) ) {
            return [
                'id'  => (int) $thumbnail_id,
                'url' => esc_url_raw( $url ),
            ];
        }

        return esc_url_raw( $url );
    }

    public function get_panel_template_setting_key() {
        return 'output_format';
    }
}