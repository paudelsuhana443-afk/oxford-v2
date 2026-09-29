<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Widget_Content_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-widget-content'; }
    public function get_title() { return esc_html__( 'Widget Content (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $post_list = $this->get_elementor_posts_for_control();

        $this->add_control(
            'post_id_select',
            [
                'label'       => esc_html__( 'Post', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => false,
                'options'     => $post_list,
                'default'     => '',
                'render_type' => 'ui',
            ]
        );

        $this->add_control(
            'post_id',
            [
                'label'       => esc_html__( 'Post ID', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => false,
                'default'     => '',
            ]
        );

        $this->add_control(
            'widget_id',
            [
                'label'       => esc_html__( 'Widget ID', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => false,
                'default'     => '',
            ]
        );
    }

    private function get_elementor_posts_for_control() {
        $cache_key = 'dynatafo_widget_content_post_list';
        $post_list = wp_cache_get( $cache_key, 'dynatafo' );

        if ( false !== $post_list ) {
            return is_array( $post_list ) ? $post_list : [];
        }

        $post_list = [];

        $posts = get_posts(
            [
                'post_type'           => 'any',
                'post_status'         => [ 'publish', 'private', 'draft', 'pending', 'future' ],
                'posts_per_page'      => -1,
                'orderby'             => 'name',
                'order'               => 'ASC',
                'fields'              => 'ids',
                'no_found_rows'       => true,
                'ignore_sticky_posts' => true,
            ]
        );

        foreach ( $posts as $post_id ) {
            $elementor_data = get_post_meta( $post_id, '_elementor_data', true );

            if ( '' === $elementor_data || '[]' === $elementor_data || null === $elementor_data ) {
                continue;
            }

            $title = get_the_title( $post_id );

            if ( '' === $title ) {
                $post_object = get_post( $post_id );
                $title       = ( $post_object && ! empty( $post_object->post_name ) ) ? $post_object->post_name : '';
            }

            $post_list[ $post_id ] = $title . ' (' . $post_id . ')';
        }

        wp_cache_set( $cache_key, $post_list, 'dynatafo', 5 * MINUTE_IN_SECONDS );

        return $post_list;
    }

    public function get_value( array $options = [] ) {
        ob_start();

        if ( filter_input( INPUT_POST, 'action' ) === 'elementor_ajax' ) {
            $actions = filter_input( INPUT_POST, 'actions' );
            $actions = json_decode( $actions );

            if ( ! empty( $actions->save_builder ) ) {
                return '';
            }
        }

        $settings = $this->get_settings();

        if ( empty( $settings['widget_id'] ) ) {
            ob_clean();
            return '';
        }

        $widget_id = $settings['widget_id'];
        $post_id   = ! empty( $settings['post_id'] ) ? (int) $settings['post_id'] : get_the_ID();

        if ( empty( $post_id ) ) {
            ob_clean();
            return '';
        }

        $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $post_id );

        if ( empty( $document ) ) {
            ob_clean();
            return '';
        }

        $elementor_data = $document->get_elements_data();
        $flat_data      = [];

        $this->flatten_elementor_data( $flat_data, $elementor_data );

        if ( empty( $flat_data[ $widget_id ] ) ) {
            ob_clean();
            return '';
        }

        $flat_data[ $widget_id ]['id'] = $widget_id;

        $temp_widget = \Elementor\Plugin::instance()->elements_manager->create_element_instance(
            $flat_data[ $widget_id ],
            []
        );

        if ( empty( $temp_widget ) ) {
            ob_clean();
            return '';
        }

        $temp_widget->print_element();

        return ob_get_clean();
    }

    private function flatten_elementor_data( &$flat_data, $elements ) {
        if ( empty( $elements ) || ! is_array( $elements ) ) {
            return;
        }

        foreach ( $elements as $element ) {
            if ( ! empty( $element['id'] ) ) {
                $flat_data[ $element['id'] ] = $element;
            }

            if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
                $this->flatten_elementor_data( $flat_data, $element['elements'] );
            }
        }
    }
}