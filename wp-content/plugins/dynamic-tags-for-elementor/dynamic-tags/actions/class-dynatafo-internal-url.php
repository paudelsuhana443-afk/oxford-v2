<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DYNATAFO_Elementor_Compat' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-elementor-compat.php';
}

if ( ! class_exists( 'DYNATAFO_WPML' ) && defined( 'DYNATAFO_PATH' ) ) {
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';
}

class DYNATAFO_Internal_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    private const FALLBACK_PER_PAGE = 20;

    public function get_name() { return 'dynatafo-internal-url'; }
    public function get_title() { return esc_html__( 'Internal URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-site-group'; }
    public function get_categories() { return [ 'url' ]; }

    private function get_setting_string( $key, $default = '' ) {
        // Prevent calling get_settings() when controls stack has no initialized settings data
        // (e.g. during register_controls() when Elementor Editor builds tag schema).
        // Calling get_settings() without settings data triggers Elementor's
        // Controls_Stack::sanitize_settings(null) which throws a fatal TypeError in PHP 8.4.
        $raw_data = method_exists( $this, 'get_raw_data' ) ? $this->get_raw_data() : null;
        if ( ! is_array( $raw_data ) || ! isset( $raw_data['settings'] ) || ! is_array( $raw_data['settings'] ) ) {
            return $default;
        }

        try {
            $value = $this->get_settings( $key );
        } catch ( \Throwable $e ) {
            return $default;
        }

        if ( is_string( $value ) ) {
            return $value;
        }

        if ( is_numeric( $value ) ) {
            return (string) $value;
        }

        return $default;
    }

    private function get_setting_page( $key ) {
        $page = absint( $this->get_setting_string( $key, '1' ) );

        return $page > 0 ? $page : 1;
    }

    private function get_page_info_text( $current, $max ) {
        $current = max( 1, absint( $current ) );
        $max     = max( 1, absint( $max ) );

        if ( $current > $max ) {
            $current = $max;
        }

        /* translators: 1: Current pagination page number, 2: Total number of pages. */
        return sprintf( esc_html__( 'Page %1$d of %2$d', 'dynamic-tags-for-elementor' ), $current, $max );
    }

    private function build_content_options( $search, $page, &$max_pages = 1 ) {
        $options = [];

        // Use an explicit list of public post types instead of 'any' to avoid
        // third-party plugins (e.g. EventPrime) from hijacking the WP_Query
        // via pre_get_posts hooks and pushing their CPTs to the top of results.
        $public_post_types = array_values(
            get_post_types(
                [
                    'public'             => true,
                    'exclude_from_search' => false,
                ],
                'names'
            )
        );

        // Exclude the built-in 'attachment' post type here — it has its own
        // dedicated section in this tag (Media tab).
        $public_post_types = array_values(
            array_diff( $public_post_types, [ 'attachment' ] )
        );

        if ( empty( $public_post_types ) ) {
            $public_post_types = [ 'post', 'page' ];
        }

        $query   = new \WP_Query(
            [
                'post_type'      => $public_post_types,
                'post_status'    => [ 'publish', 'private', 'draft', 'pending', 'future' ],
                'posts_per_page' => self::FALLBACK_PER_PAGE,
                'paged'          => $page,
                'orderby'        => 'date',
                'order'          => 'DESC',
                's'              => $search,
                'no_found_rows'  => false,
            ]
        );
        $max_pages = max( 1, (int) $query->max_num_pages );

        foreach ( $query->posts as $post ) {
            if ( ! ( $post instanceof \WP_Post ) ) {
                continue;
            }

            $title = '' !== $post->post_title ? $post->post_title : esc_html__( '(no title)', 'dynamic-tags-for-elementor' );
            $label = sprintf( '%1$s [%2$s] (%3$s)', $title, $post->post_type, $post->post_name );

            $options[ (string) $post->ID ] = $label;
        }

        return $options;
    }

    private function build_taxonomy_options( $search, $page, &$max_pages = 1 ) {
        $options   = [];
        $taxonomies = get_taxonomies( [ 'public' => true ], 'names' );
        $offset     = max( 0, ( $page - 1 ) * self::FALLBACK_PER_PAGE );

        $terms      = get_terms(
            [
                'taxonomy'   => $taxonomies,
                'hide_empty' => false,
                'search'     => $search,
                'number'     => self::FALLBACK_PER_PAGE,
                'offset'     => $offset,
            ]
        );

        if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
            return $options;
        }

        $total_terms = get_terms(
            [
                'taxonomy'   => $taxonomies,
                'hide_empty' => false,
                'search'     => $search,
                'fields'     => 'count',
            ]
        );

        if ( is_numeric( $total_terms ) ) {
            $max_pages = max( 1, (int) ceil( ( (int) $total_terms ) / self::FALLBACK_PER_PAGE ) );
        }

        foreach ( $terms as $term ) {
            if ( ! ( $term instanceof \WP_Term ) ) {
                continue;
            }

            $label = sprintf( '%1$s [%2$s] (%3$s)', $term->name, $term->taxonomy, $term->slug );

            $options[ (string) $term->term_id ] = $label;
        }

        return $options;
    }

    private function build_attachment_options( $search, $page, &$max_pages = 1 ) {
        $options     = [];
        $query       = new \WP_Query(
            [
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => self::FALLBACK_PER_PAGE,
                'paged'          => $page,
                'orderby'        => 'date',
                'order'          => 'DESC',
                's'              => $search,
                'no_found_rows'  => false,
            ]
        );
        $max_pages = max( 1, (int) $query->max_num_pages );

        foreach ( $query->posts as $attachment ) {
            if ( ! ( $attachment instanceof \WP_Post ) ) {
                continue;
            }

            $title = '' !== $attachment->post_title ? $attachment->post_title : esc_html__( '(untitled media)', 'dynamic-tags-for-elementor' );
            $label = sprintf( '%1$s (%2$s)', $title, $attachment->post_name );

            $options[ (string) $attachment->ID ] = $label;
        }

        return $options;
    }

    private function build_author_options( $search, $page, &$max_pages = 1 ) {
        $options = [];
        $query   = new \WP_User_Query(
            [
                'fields'      => [ 'ID', 'display_name', 'user_nicename' ],
                'number'      => self::FALLBACK_PER_PAGE,
                'offset'      => max( 0, ( $page - 1 ) * self::FALLBACK_PER_PAGE ),
                'search'      => '' !== $search ? '*' . $search . '*' : '',
                'search_columns' => [ 'user_login', 'user_nicename', 'display_name', 'user_email' ],
                'count_total' => true,
            ]
        );
        $users = $query->get_results();

        $total_users = (int) $query->get_total();
        $max_pages   = max( 1, (int) ceil( $total_users / self::FALLBACK_PER_PAGE ) );

        foreach ( $users as $user ) {
            if ( ! ( $user instanceof \WP_User ) ) {
                continue;
            }

            $label = sprintf( '%1$s (%2$s)', $user->display_name, $user->user_nicename );

            $options[ (string) $user->ID ] = $label;
        }

        return $options;
    }

    protected function register_controls() {
        $query_control      = DYNATAFO_Elementor_Compat::get_query_control_config();
        $query_control_type = $query_control['control_type'];
        $query_objects      = $query_control['query_objects'];
        $has_query_control  = (bool) $query_control['has_query_control'];

        // Read saved search/page settings so that the dropdown options reflect
        // the user's current search term and pagination state. Elementor calls
        // register_controls() both on initial render and after a settings save,
        // so the saved values are available here and will produce the correct
        // filtered/paginated option list that the user expects to see.
        $post_search       = $this->get_setting_string( 'post_search',       '' );
        $taxonomy_search   = $this->get_setting_string( 'taxonomy_search',   '' );
        $attachment_search = $this->get_setting_string( 'attachment_search', '' );
        $author_search     = $this->get_setting_string( 'author_search',     '' );

        $post_page       = $this->get_setting_page( 'post_page' );
        $taxonomy_page   = $this->get_setting_page( 'taxonomy_page' );
        $attachment_page = $this->get_setting_page( 'attachment_page' );
        $author_page     = $this->get_setting_page( 'author_page' );

        $post_max_pages       = 1;
        $taxonomy_max_pages   = 1;
        $attachment_max_pages = 1;
        $author_max_pages     = 1;

        $post_options       = $has_query_control ? [] : $this->build_content_options( $post_search, $post_page, $post_max_pages );
        $taxonomy_options   = $has_query_control ? [] : $this->build_taxonomy_options( $taxonomy_search, $taxonomy_page, $taxonomy_max_pages );
        $attachment_options = $has_query_control ? [] : $this->build_attachment_options( $attachment_search, $attachment_page, $attachment_max_pages );
        $author_options     = $has_query_control ? [] : $this->build_author_options( $author_search, $author_page, $author_max_pages );

        $this->add_control(
            'type',
            [
                'label'   => esc_html__( 'Type', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'post',
                'options' => [
                    'post'     => esc_html__( 'Content', 'dynamic-tags-for-elementor' ),
                    'taxonomy' => esc_html__( 'Taxonomy', 'dynamic-tags-for-elementor' ),
                    'attachment' => esc_html__( 'Media', 'dynamic-tags-for-elementor' ),
                    'author'   => esc_html__( 'Author', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'post_id',
            [
                'label'       => esc_html__( 'Search & Select', 'dynamic-tags-for-elementor' ),
                'type'        => $query_control_type,
                'options'     => $post_options,
                'label_block' => true,
                'multiple'    => false,
                'condition' => [
                    'type' => 'post',
                ],
            ]
        );

        if ( ! $has_query_control ) {
            $this->add_control(
                'post_search',
                [
                    'label'       => esc_html__( 'Search', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'description' => esc_html__( 'Filter results by title or slug, then reopen the selector.', 'dynamic-tags-for-elementor' ),
                    'condition'   => [
                        'type' => 'post',
                    ],
                ]
            );

            $this->add_control(
                'post_page',
                [
                    'label'     => esc_html__( 'Page', 'dynamic-tags-for-elementor' ),
                    'type'      => \Elementor\Controls_Manager::NUMBER,
                    'default'   => 1,
                    'min'       => 1,
                    'max'       => $post_max_pages,
                    'condition' => [
                        'type' => 'post',
                    ],
                ]
            );

            $this->add_control(
                'post_page_info',
                [
                    'type'      => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'       => esc_html( $this->get_page_info_text( $post_page, $post_max_pages ) ),
                    'condition' => [
                        'type' => 'post',
                    ],
                ]
            );
        }

        if ( $has_query_control ) {
            $this->update_control(
                'post_id',
                [
                    'autocomplete' => [
                            'object'  => isset( $query_objects['post'] ) ? $query_objects['post'] : 'post',
                        'display' => 'detailed',
                        'query'   => [
                            'post_type' => 'any',
                            'posts_per_page' => self::FALLBACK_PER_PAGE,
                        ],
                    ],
                ]
            );
        }

        $this->add_control(
            'taxonomy_id',
            [
                'label'       => esc_html__( 'Search & Select', 'dynamic-tags-for-elementor' ),
                'type'        => $query_control_type,
                'options'     => $taxonomy_options,
                'label_block' => true,
                'multiple'    => false,
                'condition' => [
                    'type' => 'taxonomy',
                ],
            ]
        );

        if ( ! $has_query_control ) {
            $this->add_control(
                'taxonomy_search',
                [
                    'label'       => esc_html__( 'Search', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'description' => esc_html__( 'Filter terms by name or slug, then reopen the selector.', 'dynamic-tags-for-elementor' ),
                    'condition'   => [
                        'type' => 'taxonomy',
                    ],
                ]
            );

            $this->add_control(
                'taxonomy_page',
                [
                    'label'     => esc_html__( 'Page', 'dynamic-tags-for-elementor' ),
                    'type'      => \Elementor\Controls_Manager::NUMBER,
                    'default'   => 1,
                    'min'       => 1,
                    'max'       => $taxonomy_max_pages,
                    'condition' => [
                        'type' => 'taxonomy',
                    ],
                ]
            );

            $this->add_control(
                'taxonomy_page_info',
                [
                    'type'      => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'       => esc_html( $this->get_page_info_text( $taxonomy_page, $taxonomy_max_pages ) ),
                    'condition' => [
                        'type' => 'taxonomy',
                    ],
                ]
            );
        }

        if ( $has_query_control ) {
            $this->update_control(
                'taxonomy_id',
                [
                    'autocomplete' => [
                            'object'  => isset( $query_objects['taxonomy'] ) ? $query_objects['taxonomy'] : 'taxonomy',
                        'display' => 'detailed',
                    ],
                ]
            );
        }

        $this->add_control(
            'attachment_id',
            [
                'label'       => esc_html__( 'Search & Select', 'dynamic-tags-for-elementor' ),
                'type'        => $query_control_type,
                'options'     => $attachment_options,
                'label_block' => true,
                'multiple'    => false,
                'condition' => [
                    'type' => 'attachment',
                ],
            ]
        );

        if ( ! $has_query_control ) {
            $this->add_control(
                'attachment_search',
                [
                    'label'       => esc_html__( 'Search', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'description' => esc_html__( 'Filter media by title or slug, then reopen the selector.', 'dynamic-tags-for-elementor' ),
                    'condition'   => [
                        'type' => 'attachment',
                    ],
                ]
            );

            $this->add_control(
                'attachment_page',
                [
                    'label'     => esc_html__( 'Page', 'dynamic-tags-for-elementor' ),
                    'type'      => \Elementor\Controls_Manager::NUMBER,
                    'default'   => 1,
                    'min'       => 1,
                    'max'       => $attachment_max_pages,
                    'condition' => [
                        'type' => 'attachment',
                    ],
                ]
            );

            $this->add_control(
                'attachment_page_info',
                [
                    'type'      => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'       => esc_html( $this->get_page_info_text( $attachment_page, $attachment_max_pages ) ),
                    'condition' => [
                        'type' => 'attachment',
                    ],
                ]
            );
        }

        if ( $has_query_control ) {
            $this->update_control(
                'attachment_id',
                [
                    'autocomplete' => [
                            'object'  => isset( $query_objects['attachment'] ) ? $query_objects['attachment'] : 'attachment',
                        'display' => 'detailed',
                    ],
                ]
            );
        }

        $this->add_control(
            'author_id',
            [
                'label'       => esc_html__( 'Search & Select', 'dynamic-tags-for-elementor' ),
                'type'        => $query_control_type,
                'options'     => $author_options,
                'label_block' => true,
                'multiple'    => false,
                'condition' => [
                    'type' => 'author',
                ],
            ]
        );

        if ( ! $has_query_control ) {
            $this->add_control(
                'author_search',
                [
                    'label'       => esc_html__( 'Search', 'dynamic-tags-for-elementor' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'label_block' => true,
                    'description' => esc_html__( 'Filter authors by name, nicename, login, or email.', 'dynamic-tags-for-elementor' ),
                    'condition'   => [
                        'type' => 'author',
                    ],
                ]
            );

            $this->add_control(
                'author_page',
                [
                    'label'     => esc_html__( 'Page', 'dynamic-tags-for-elementor' ),
                    'type'      => \Elementor\Controls_Manager::NUMBER,
                    'default'   => 1,
                    'min'       => 1,
                    'max'       => $author_max_pages,
                    'condition' => [
                        'type' => 'author',
                    ],
                ]
            );

            $this->add_control(
                'author_page_info',
                [
                    'type'      => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'       => esc_html( $this->get_page_info_text( $author_page, $author_max_pages ) ),
                    'condition' => [
                        'type' => 'author',
                    ],
                ]
            );
        }

        if ( $has_query_control ) {
            $this->update_control(
                'author_id',
                [
                    'autocomplete' => [
                            'object'  => isset( $query_objects['author'] ) ? $query_objects['author'] : 'author',
                        'display' => 'detailed',
                    ],
                ]
            );
        }
    }

    private function normalize_selected_id( $value ) {
        if ( is_numeric( $value ) ) {
            return absint( $value );
        }

        if ( is_array( $value ) ) {
            $first = reset( $value );

            if ( is_numeric( $first ) ) {
                return absint( $first );
            }

            if ( is_string( $first ) && preg_match( '/(\\d+)/', $first, $matches ) ) {
                return absint( $matches[1] );
            }

            return 0;
        }

        if ( is_string( $value ) && preg_match( '/(\\d+)/', $value, $matches ) ) {
            return absint( $matches[1] );
        }

        return 0;
    }

    public function get_value( array $options = [] ) {
        $type = (string) $this->get_settings( 'type' );

        if ( 'post' === $type ) {
            $post_id = $this->normalize_selected_id( $this->get_settings( 'post_id' ) );

            if ( $post_id > 0 ) {
                $url = get_permalink( $post_id );
                
                // Apply WPML language-aware URL filter for posts
                if ( class_exists( 'DYNATAFO_WPML' ) ) {
                    /* translators: Post URL being processed for language support */
                    $url = DYNATAFO_WPML::apply_language_to_permalink( $url, $post_id );
                }
                
                return (string) $url;
            }

            return '';
        }

        if ( 'taxonomy' === $type ) {
            $term_id = $this->normalize_selected_id( $this->get_settings( 'taxonomy_id' ) );

            if ( $term_id > 0 ) {
                // Get term to access taxonomy info
                $term = get_term( $term_id );
                if ( $term instanceof \WP_Term ) {
                    $term_link = get_term_link( $term_id );

                    if ( ! is_wp_error( $term_link ) ) {
                        // Apply WPML language-aware URL filter for taxonomy terms
                        if ( class_exists( 'DYNATAFO_WPML' ) ) {
                            /* translators: Taxonomy term URL being processed for language support */
                            $term_link = DYNATAFO_WPML::apply_language_to_term_link( $term_link, $term_id, $term->taxonomy );
                        }
                        return (string) $term_link;
                    }
                }
            }

            return '';
        }

        if ( 'attachment' === $type ) {
            $media_id = $this->normalize_selected_id( $this->get_settings( 'attachment_id' ) );

            if ( $media_id > 0 ) {
                return (string) get_attachment_link( $media_id );
            }

            return '';
        }

        if ( 'author' === $type ) {
            $author_id = $this->normalize_selected_id( $this->get_settings( 'author_id' ) );

            if ( $author_id > 0 ) {
                $url = get_author_posts_url( $author_id );
                
                // Apply WPML language-aware URL filter for author archives
                if ( class_exists( 'DYNATAFO_WPML' ) ) {
                    /* translators: Author archive URL being processed for language support */
                    $url = DYNATAFO_WPML::apply_language_to_author_url( $url, $author_id );
                }
                
                return (string) $url;
            }

            return '';
        }

        return '';
    }

    public function get_panel_template_setting_key() {
        return 'post_id';
    }
}