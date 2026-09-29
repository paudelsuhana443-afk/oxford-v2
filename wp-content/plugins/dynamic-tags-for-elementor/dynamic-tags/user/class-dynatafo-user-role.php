<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_User_Role_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-user-role'; }
    public function get_title() { return esc_html__( 'User Role (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-author-group'; }
    public function get_categories() { return [ 'text' ]; }

    protected function register_controls() {
        $options = [
            'currentuser'   => esc_html__( 'Current User', 'dynamic-tags-for-elementor' ),
            'currentauthor' => esc_html__( 'Current Author', 'dynamic-tags-for-elementor' ),
        ];

        $users = get_users( [ 'fields' => [ 'id', 'user_login' ] ] );

        foreach ( $users as $user ) {
            $options[ $user->id ] = $user->user_login;
        }

        $this->add_control(
            'key',
            [
                'label'       => esc_html__( 'Key', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => 'currentuser',
                'options'     => $options,
            ]
        );

        $this->add_control(
            'separator',
            [
                'label'   => esc_html__( 'Separator', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => ', ',
            ]
        );

        $this->add_control(
            'format',
            [
                'label'       => esc_html__( 'Format', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'default'     => 'plain',
                'options'     => [
                    'plain'          => esc_html__( 'Plain', 'dynamic-tags-for-elementor' ),
                    'human_readable' => esc_html__( 'Human readable', 'dynamic-tags-for-elementor' ),
                    'translated'     => esc_html__( 'Translated', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'add_wrapper',
            [
                'label'   => esc_html__( 'Add wrapper around items', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
    }

    public function render() {
        $user_id = $this->get_settings( 'key' );

        if ( 'currentuser' === $user_id ) {
            $user_id = get_current_user_id();
        } elseif ( 'currentauthor' === $user_id ) {
            $user_id = get_the_author_meta( 'id' );
        }

        if ( empty( $user_id ) ) {
            return;
        }

        $separator = $this->get_settings( 'separator' );
        $user_info = get_userdata( $user_id );

        if ( ! $user_info || empty( $user_info->roles ) ) {
            return;
        }

        switch ( $this->get_settings( 'format' ) ) {
            case 'human_readable':
                global $wp_roles;
                $role_names  = $wp_roles ? $wp_roles->get_names() : [];
                $roles_array = [];
                foreach ( $user_info->roles as $role ) {
                    $roles_array[] = $role_names[ $role ] ?? $role;
                }
                break;

            case 'translated':
                global $wp_roles;
                $role_names  = $wp_roles ? $wp_roles->get_names() : [];
                $roles_array = [];
                foreach ( $user_info->roles as $role ) {
                    $roles_array[] = translate_user_role( $role_names[ $role ] ?? $role );
                }
                break;

            case 'plain':
            default:
                $roles_array = $user_info->roles;
                break;
        }

        if ( 'yes' === $this->get_settings( 'add_wrapper' ) ) {
            foreach ( $roles_array as &$role ) {
                $role = '<span class="user-role">' . esc_html( $role ) . '</span>';
            }
            unset( $role );
        } else {
            foreach ( $roles_array as &$role ) {
                $role = esc_html( $role );
            }
            unset( $role );
        }

        echo wp_kses_post( implode( $separator, $roles_array ) );
    }

    public function get_panel_template_setting_key() {
        return 'key';
    }
}