<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Lightbox_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-lightbox-url'; }
    public function get_title() { return esc_html__( 'Lightbox (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-actions-group'; }
    public function get_categories() { return [ 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'type',
            [
                'label'   => esc_html__( 'Type', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'default' => 'video',
                'options' => [
                    'video' => [
                        'title' => esc_html__( 'Video', 'dynamic-tags-for-elementor' ),
                        'icon'  => 'eicon-video-camera',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'dynamic-tags-for-elementor' ),
                        'icon'  => 'eicon-image-bold',
                    ],
                ],
            ]
        );

        $this->add_control(
            'media',
            [
                'label'     => esc_html__( 'Media', 'dynamic-tags-for-elementor' ),
                'type'      => \Elementor\Controls_Manager::MEDIA,
                'condition' => [
                    'type' => 'image',
                ],
            ]
        );

        $this->add_control(
            'video_url',
            [
                'label'       => esc_html__( 'Video URL', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'condition' => [
                    'type' => 'video',
                ],
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $settings = $this->get_settings();
        $payload  = [];

        if ( 'image' === $settings['type'] ) {
            $image_url = '';

            if ( ! empty( $settings['media']['url'] ) ) {
                $image_url = (string) $settings['media']['url'];
            } else {
                $thumbnail_id = get_post_thumbnail_id();

                if ( $thumbnail_id ) {
                    $image_url = (string) wp_get_attachment_image_url( $thumbnail_id, 'full' );
                }
            }

            if ( '' === $image_url ) {
                return '';
            }

            $payload = [
                'type' => 'image',
                'url'  => $image_url,
            ];
        } else {
            $video_url = isset( $settings['video_url'] ) ? trim( (string) $settings['video_url'] ) : '';

            if ( '' === $video_url ) {
                return '';
            }

            $payload = [
                'type'      => 'video',
                'videoType' => 'hosted',
                'url'       => $video_url,
            ];
        }

        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            $plugin = \Elementor\Plugin::instance();

            if ( isset( $plugin->frontend ) && method_exists( $plugin->frontend, 'create_action_hash' ) ) {
                return (string) $plugin->frontend->create_action_hash( 'lightbox', $payload );
            }
        }

        return isset( $payload['url'] ) ? esc_url_raw( (string) $payload['url'] ) : '';
    }
}