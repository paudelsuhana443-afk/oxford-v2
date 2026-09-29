<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Archive_Meta_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-archive-meta'; }
    public function get_title() { return esc_html__( 'Archive Meta (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-archive-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $this->add_control(
            'meta_key',
            [
                'label'       => esc_html__( 'Term Meta Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'description' => esc_html__( 'Used for taxonomy archives. Leave empty to show a default archive summary.', 'dynamic-tags-for-elementor' ),
            ]
        );
    }

    public function render() {
        $object = get_queried_object();

        if ( $object instanceof \WP_Term ) {
            $meta_key = sanitize_key( (string) $this->get_settings( 'meta_key' ) );

            if ( '' !== $meta_key ) {
                $value = get_term_meta( $object->term_id, $meta_key, true );

                if ( is_scalar( $value ) && '' !== (string) $value ) {
                    echo wp_kses_post( (string) $value );
                    return;
                }
            }

            echo esc_html( sprintf( '%s (%d)', $object->taxonomy, (int) $object->count ) );
            return;
        }

        if ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            $post_type = is_array( $post_type ) ? reset( $post_type ) : $post_type;
            $obj       = is_string( $post_type ) ? get_post_type_object( $post_type ) : null;

            if ( $obj && ! empty( $obj->labels->name ) ) {
                echo esc_html( (string) $obj->labels->name );
            }
        }
    }
}