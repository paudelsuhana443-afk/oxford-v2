<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Off_Canvas_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-off-canvas-url'; }
    public function get_title() { return esc_html__( 'Off-Canvas (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-actions-group'; }
    public function get_categories() { return [ 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'action',
            [
                'label'   => esc_html__( 'Action', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'open',
                'options' => [
                    'open'   => esc_html__( 'Open Off-Canvas', 'dynamic-tags-for-elementor' ),
                    'close'  => esc_html__( 'Close Off-Canvas', 'dynamic-tags-for-elementor' ),
                    'toggle' => esc_html__( 'Toggle Off-Canvas', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'target_id',
            [
                'label'       => esc_html__( 'Off-Canvas ID', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'offcanvas',
                'description' => esc_html__( 'Widget/container ID used by your off-canvas panel.', 'dynamic-tags-for-elementor' ),
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $target_id = sanitize_html_class( (string) $this->get_settings( 'target_id' ) );
        $action    = sanitize_key( (string) $this->get_settings( 'action' ) );

        if ( '' === $target_id ) {
            $target_id = 'offcanvas';
        }

        if ( ! in_array( $action, [ 'open', 'close', 'toggle' ], true ) ) {
            $action = 'open';
        }

        if ( class_exists( '\\Elementor\\Plugin' ) ) {
            $plugin = \Elementor\Plugin::instance();

            if ( isset( $plugin->frontend ) && method_exists( $plugin->frontend, 'create_action_hash' ) ) {
                return (string) $plugin->frontend->create_action_hash(
                    'off_canvas:' . $action,
                    [
                        'id'          => $target_id,
                        'displayMode' => $action,
                    ]
                );
            }
        }

        return esc_url_raw( '#' . $target_id );
    }
}