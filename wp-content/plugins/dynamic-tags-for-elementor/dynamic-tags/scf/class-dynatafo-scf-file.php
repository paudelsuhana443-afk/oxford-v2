<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'get_field' ) ) {

    class DYNATAFO_SCF_File_Tag extends \Elementor\Core\DynamicTags\Tag {

        public function get_name() { return 'dynatafo-scf-file'; }
        public function get_title() { return esc_html__( 'SCF File (DTE)', 'dynamic-tags-for-elementor' ); }
        public function get_group() { return 'dynatafo-scf-group'; }
        public function get_categories() { return [ 'text', 'url' ]; }

        protected function register_controls() {
            $this->add_control(
                'field_name',
                [
                    'label'       => esc_html__( 'Field Name', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'default'     => '',
                ]
            );

            $this->add_control(
                'output',
                [
                    'label'   => esc_html__( 'Output', 'dynamic-tags-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'url',
                    'options' => [
                        'url'      => esc_html__( 'URL', 'dynamic-tags-for-elementor' ),
                        'id'       => esc_html__( 'Attachment ID', 'dynamic-tags-for-elementor' ),
                        'title'    => esc_html__( 'Title', 'dynamic-tags-for-elementor' ),
                        'filename' => esc_html__( 'Filename', 'dynamic-tags-for-elementor' ),
                        'mime'     => esc_html__( 'MIME Type', 'dynamic-tags-for-elementor' ),
                    ],
                ]
            );
        }

        private function normalize_file( $value ) {
            $file = [
                'id'       => 0,
                'url'      => '',
                'title'    => '',
                'filename' => '',
                'mime'     => '',
            ];

            if ( is_numeric( $value ) ) {
                $file['id'] = (int) $value;
            } elseif ( is_array( $value ) ) {
                if ( isset( $value['ID'] ) ) {
                    $file['id'] = (int) $value['ID'];
                } elseif ( isset( $value['id'] ) ) {
                    $file['id'] = (int) $value['id'];
                }

                if ( isset( $value['url'] ) ) {
                    $file['url'] = esc_url_raw( (string) $value['url'] );
                }

                if ( isset( $value['title'] ) ) {
                    $file['title'] = (string) $value['title'];
                }

                if ( isset( $value['filename'] ) ) {
                    $file['filename'] = (string) $value['filename'];
                }

                if ( isset( $value['mime_type'] ) ) {
                    $file['mime'] = (string) $value['mime_type'];
                }
            } elseif ( is_string( $value ) ) {
                $file['url'] = esc_url_raw( $value );
            }

            if ( $file['id'] > 0 ) {
                if ( '' === $file['url'] ) {
                    $file['url'] = wp_get_attachment_url( $file['id'] );
                }

                if ( '' === $file['title'] ) {
                    $file['title'] = get_the_title( $file['id'] );
                }

                if ( '' === $file['mime'] ) {
                    $file['mime'] = get_post_mime_type( $file['id'] );
                }

                if ( '' === $file['filename'] ) {
                    $path = get_attached_file( $file['id'] );
                    if ( $path ) {
                        $file['filename'] = wp_basename( $path );
                    }
                }
            }

            if ( '' !== $file['url'] && '' === $file['filename'] ) {
                $parsed = wp_parse_url( $file['url'], PHP_URL_PATH );
                if ( is_string( $parsed ) && '' !== $parsed ) {
                    $file['filename'] = wp_basename( $parsed );
                }
            }

            return $file;
        }

        public function render() {
            $field_name = $this->get_settings( 'field_name' );

            if ( empty( $field_name ) ) {
                return;
            }

            $value = dynatafo_get_cached_field( $field_name );

            if ( null === $value || '' === $value || [] === $value ) {
                return;
            }

            $file   = $this->normalize_file( $value );
            $output = $this->get_settings( 'output' );

            switch ( $output ) {
                case 'id':
                    echo esc_html( (string) $file['id'] );
                    break;
                case 'title':
                    echo esc_html( $file['title'] );
                    break;
                case 'filename':
                    echo esc_html( $file['filename'] );
                    break;
                case 'mime':
                    echo esc_html( $file['mime'] );
                    break;
                case 'url':
                default:
                    echo esc_html( $file['url'] );
                    break;
            }
        }
    }
}
