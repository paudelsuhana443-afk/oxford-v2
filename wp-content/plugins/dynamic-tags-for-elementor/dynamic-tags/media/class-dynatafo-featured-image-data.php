<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Featured_Image_Data_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-featured-image-data'; }
    public function get_title() { return esc_html__( 'Featured Image Data (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-media-group'; }
    public function get_categories() { return [ 'text', 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'attachment_data',
            [
                'label'   => esc_html__( 'Data', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'title',
                'options' => [
                    'title'       => esc_html__( 'Title', 'dynamic-tags-for-elementor' ),
                    'alt'         => esc_html__( 'Alt', 'dynamic-tags-for-elementor' ),
                    'caption'     => esc_html__( 'Caption', 'dynamic-tags-for-elementor' ),
                    'description' => esc_html__( 'Description', 'dynamic-tags-for-elementor' ),
                    'src'         => esc_html__( 'File URL', 'dynamic-tags-for-elementor' ),
                    'href'        => esc_html__( 'Attachment URL', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );
    }

    private function get_attachment() {
        $thumbnail_id = get_post_thumbnail_id();

        if ( ! $thumbnail_id ) {
            return false;
        }

        return get_post( $thumbnail_id );
    }

    public function render() {
        $settings   = $this->get_settings();
        $attachment = $this->get_attachment();

        if ( ! $attachment ) {
            return;
        }

        $data_type = isset( $settings['attachment_data'] ) ? (string) $settings['attachment_data'] : 'title';
        $value     = '';

        switch ( $data_type ) {
            case 'alt':
                $value = (string) get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
                break;
            case 'caption':
                $value = (string) $attachment->post_excerpt;
                break;
            case 'description':
                $value = (string) $attachment->post_content;
                break;
            case 'href':
                $value = (string) get_attachment_link( $attachment->ID );
                break;
            case 'src':
                $value = (string) wp_get_attachment_url( $attachment->ID );
                break;
            case 'title':
            default:
                $value = (string) $attachment->post_title;
                break;
        }

        if ( in_array( $data_type, [ 'href', 'src' ], true ) ) {
            echo esc_url_raw( $value );
            return;
        }

        echo wp_kses_post( $value );
    }
}