<?php
/**
 * Elementor compatibility helpers for DYNATAFO.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Elementor_Compat {

    /**
     * Tags that are Free-mode fallbacks for Elementor Pro behavior.
     * These should not register when Elementor Pro is active.
     *
     * @var array<string>
     */
    private const FALLBACK_ONLY_TAGS = [
        'DYNATAFO_Archive_URL_Tag',
        'DYNATAFO_Featured_Image_Data_Tag',
        'DYNATAFO_Comments_URL_Tag',
        'DYNATAFO_Internal_URL_Tag',
        'DYNATAFO_Shortcode_URL_Tag',
        'DYNATAFO_Contact_URL_Tag',
        'DYNATAFO_Reload_Page_URL_Tag',
        'DYNATAFO_Lightbox_URL_Tag',
        'DYNATAFO_Off_Canvas_URL_Tag',
        'DYNATAFO_Author_URL_Tag',
    ];

    /**
     * Read class constant value safely across Elementor versions.
     *
     * @param string $class_name
     * @param string $constant_name
     * @param mixed  $fallback
     * @return mixed
     */
    private static function get_class_constant_value( $class_name, $constant_name, $fallback ) {
        $constant_key = $class_name . '::' . $constant_name;

        if ( defined( $constant_key ) ) {
            return constant( $constant_key );
        }

        return $fallback;
    }

    /**
     * Check if Elementor Pro is active.
     *
     * @param bool $bootstrap_pro_state
     * @return bool
     */
    public static function is_elementor_pro_active( $bootstrap_pro_state = false ) {
        return (bool) $bootstrap_pro_state || defined( 'ELEMENTOR_PRO_VERSION' );
    }

    /**
     * Check if WooCommerce integration can be used.
     *
     * @return bool
     */
    public static function has_woocommerce() {
        return class_exists( 'WooCommerce' ) && function_exists( 'wc_get_product' );
    }

    /**
     * Check if Secure Custom Fields integration can be used.
     *
     * @return bool
     */
    public static function has_scf() {
        if ( class_exists( 'SCF\\Meta\\Post' ) ) {
            return true;
        }

        if ( function_exists( 'acf_get_setting' ) ) {
            $name = acf_get_setting( 'name' );
            $slug = acf_get_setting( 'slug' );

            if ( is_string( $name ) && false !== stripos( $name, 'Secure Custom Fields' ) ) {
                return true;
            }

            if ( is_string( $slug ) && false !== stripos( $slug, 'secure-custom-fields' ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if ACF integration can be used.
     *
     * @return bool
     */
    public static function has_acf() {
        if ( self::has_scf() ) {
            return false;
        }

        return function_exists( 'get_field' ) || function_exists( 'have_rows' );
    }

    /**
     * Decide whether a group should register for current environment.
     *
     * @param string $group_id
     * @param bool   $is_pro_active
     * @return bool
     */
    public static function should_register_group( $group_id, $is_pro_active = false ) {
        if ( 'dynatafo-actions-group' === $group_id ) {
            return ! self::is_elementor_pro_active( $is_pro_active );
        }

        return true;
    }

    /**
     * Decide whether a tag should register for current environment.
     *
     * @param string $tag_class
     * @param bool   $is_pro_active
     * @return bool
     */
    public static function should_register_tag( $tag_class, $is_pro_active = false ) {
        if ( 'DYNATAFO_Post_Parent_Tag' === $tag_class ) {
            $post_type = self::get_current_post_type();
            if ( ! empty( $post_type ) ) {
                if ( 'elementor_library' === $post_type ) {
                    return true;
                }
                return is_post_type_hierarchical( $post_type );
            }
        }

        if ( in_array( $tag_class, [ 'DYNATAFO_Is_Home_Tag', 'DYNATAFO_Is_Frontpage_Tag', 'DYNATAFO_Is_404_Tag' ], true ) ) {
            $post_type = self::get_current_post_type();
            if ( ! empty( $post_type ) ) {
                return in_array( $post_type, [ 'page', 'elementor_library' ], true );
            }
        }

        if ( 'DYNATAFO_Post_Terms_Tag' === $tag_class ) {
            $post_type = self::get_current_post_type();
            if ( ! empty( $post_type ) ) {
                if ( 'elementor_library' === $post_type ) {
                    return true;
                }
                $taxonomies = get_object_taxonomies( $post_type );
                return ! empty( $taxonomies );
            }
        }

        if ( 'DYNATAFO_Post_Categories_Tag' === $tag_class ) {
            $post_type = self::get_current_post_type();
            if ( ! empty( $post_type ) ) {
                if ( 'elementor_library' === $post_type ) {
                    return true;
                }
                $taxonomies = get_object_taxonomies( $post_type );
                return in_array( 'category', $taxonomies, true );
            }
        }

        if ( 'DYNATAFO_Post_Tags_Tag' === $tag_class ) {
            $post_type = self::get_current_post_type();
            if ( ! empty( $post_type ) ) {
                if ( 'elementor_library' === $post_type ) {
                    return true;
                }
                $taxonomies = get_object_taxonomies( $post_type );
                return in_array( 'post_tag', $taxonomies, true );
            }
        }

        if ( ! self::is_elementor_pro_active( $is_pro_active ) ) {
            return true;
        }

        return ! in_array( $tag_class, self::FALLBACK_ONLY_TAGS, true );
    }

    /**
     * Get Query Control compatibility configuration.
     *
     * @return array<string,mixed>
     */
    public static function get_query_control_config() {
        $config = [
            'has_query_control' => false,
            'control_type'      => \Elementor\Controls_Manager::SELECT2,
            'query_objects'     => [
                'post'       => 'post',
                'taxonomy'   => 'taxonomy',
                'attachment' => 'attachment',
                'author'     => 'author',
            ],
        ];

        if ( class_exists( '\\ElementorPro\\Modules\\QueryControl\\Module' ) ) {
            $class_name = '\\ElementorPro\\Modules\\QueryControl\\Module';

            $config['has_query_control'] = true;
            $config['control_type']      = self::get_class_constant_value( $class_name, 'QUERY_CONTROL_ID', \Elementor\Controls_Manager::SELECT2 );
            $config['query_objects']     = [
                'post'       => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_POST', 'post' ),
                'taxonomy'   => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_TAX', 'taxonomy' ),
                'attachment' => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_ATTACHMENT', 'attachment' ),
                'author'     => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_AUTHOR', 'author' ),
            ];

            return $config;
        }

        if ( class_exists( '\\Elementor\\Modules\\QueryControl\\Module' ) ) {
            $class_name = '\\Elementor\\Modules\\QueryControl\\Module';

            $config['has_query_control'] = true;
            $config['control_type']      = self::get_class_constant_value( $class_name, 'QUERY_CONTROL_ID', \Elementor\Controls_Manager::SELECT2 );
            $config['query_objects']     = [
                'post'       => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_POST', 'post' ),
                'taxonomy'   => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_TAX', 'taxonomy' ),
                'attachment' => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_ATTACHMENT', 'attachment' ),
                'author'     => self::get_class_constant_value( $class_name, 'QUERY_OBJECT_AUTHOR', 'author' ),
            ];
        }

        return $config;
    }

    /**
     * Get the current post type being edited or rendered.
     *
     * @return string
     */
    public static function get_current_post_type() {
        if ( is_admin() ) {
            $post_id = 0;
            // phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
            if ( isset( $_GET['post'] ) ) {
                $post_id = (int) $_GET['post'];
            } elseif ( isset( $_POST['post_id'] ) ) {
                $post_id = (int) $_POST['post_id'];
            } elseif ( isset( $_POST['editor_post_id'] ) ) {
                $post_id = (int) $_POST['editor_post_id'];
            } elseif ( isset( $_REQUEST['post_id'] ) ) {
                $post_id = (int) $_REQUEST['post_id'];
            } elseif ( isset( $_REQUEST['post'] ) ) {
                $post_id = (int) $_REQUEST['post'];
            }
            // phpcs:enable

            if ( $post_id > 0 ) {
                return get_post_type( $post_id );
            }

            if ( function_exists( 'get_current_screen' ) ) {
                $screen = get_current_screen();
                if ( $screen && ! empty( $screen->post_type ) ) {
                    return $screen->post_type;
                }
            }
        } else {
            $post_type = get_post_type();
            if ( $post_type ) {
                return $post_type;
            }

            $obj = get_queried_object();
            if ( $obj instanceof \WP_Post ) {
                return $obj->post_type;
            }
        }

        return '';
    }
}
