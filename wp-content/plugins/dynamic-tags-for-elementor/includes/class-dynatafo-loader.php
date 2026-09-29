<?php
/**
 * DYNATAFO_Loader Class
 * Handles the registration of Dynamic Tag groups and individual tags.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once DYNATAFO_PATH . 'includes/class-dynatafo-elementor-compat.php';
require_once DYNATAFO_PATH . 'includes/class-dynatafo-wpml.php';

class DYNATAFO_Loader {

    /**
     * Core dynamic tags map: Class Name => File Path
     *
     * @var array<string, string>
     */
    private const CORE_TAGS = [
        'DYNATAFO_Post_Title_Tag'          => 'dynamic-tags/post/class-dynatafo-post-title.php',
        'DYNATAFO_Post_Date_Tag'           => 'dynamic-tags/post/class-dynatafo-post-date.php',
        'DYNATAFO_Post_Time_Tag'           => 'dynamic-tags/post/class-dynatafo-post-time.php',
        'DYNATAFO_Post_ID_Tag'             => 'dynamic-tags/post/class-dynatafo-post-id.php',
        'DYNATAFO_Page_Title_Tag'          => 'dynamic-tags/post/class-dynatafo-page-title.php',
        'DYNATAFO_Post_Excerpt_Tag'        => 'dynamic-tags/post/class-dynatafo-post-excerpt.php',
        'DYNATAFO_Post_Custom_Field_Tag'   => 'dynamic-tags/post/class-dynatafo-post-custom-field.php',
        'DYNATAFO_Post_Terms_Tag'          => 'dynamic-tags/post/class-dynatafo-post-terms.php',
        'DYNATAFO_Post_Featured_Image_Tag' => 'dynamic-tags/post/class-dynatafo-post-featured-image.php',
        'DYNATAFO_Post_Gallery_Tag'        => 'dynamic-tags/post/class-dynatafo-post-gallery.php',
        'DYNATAFO_Post_URL_Tag'            => 'dynamic-tags/post/class-dynatafo-post-url.php',
        'DYNATAFO_Archive_Description_Tag' => 'dynamic-tags/archive/class-dynatafo-archive-description.php',
        'DYNATAFO_Archive_Meta_Tag'        => 'dynamic-tags/archive/class-dynatafo-archive-meta.php',
        'DYNATAFO_Archive_URL_Tag'         => 'dynamic-tags/archive/class-dynatafo-archive-url.php',
        'DYNATAFO_Post_Thumbnail_URL_Tag'  => 'dynamic-tags/post/class-dynatafo-post-thumbnail-url.php',
        'DYNATAFO_Featured_Image_Data_Tag' => 'dynamic-tags/media/class-dynatafo-featured-image-data.php',
        'DYNATAFO_Post_Categories_Tag'     => 'dynamic-tags/post/class-dynatafo-post-categories.php',
        'DYNATAFO_Post_Tags_Tag'           => 'dynamic-tags/post/class-dynatafo-post-tags.php',
        'DYNATAFO_Post_Modified_Date_Tag'  => 'dynamic-tags/post/class-dynatafo-post-modified-date.php',
        'DYNATAFO_Post_Comment_Count_Tag'  => 'dynamic-tags/post/class-dynatafo-post-comment-count.php',
        'DYNATAFO_Comments_URL_Tag'        => 'dynamic-tags/comments/class-dynatafo-comments-url.php',
        'DYNATAFO_Comments_Number_Tag'     => 'dynamic-tags/comments/class-dynatafo-comments-number.php',
        'DYNATAFO_Query_Param_Tag'         => 'dynamic-tags/utils/class-dynatafo-query-param.php',
        'DYNATAFO_Request_Parameter_Tag'   => 'dynamic-tags/utils/class-dynatafo-request-parameter.php',
        'DYNATAFO_Post_Content_Tag'        => 'dynamic-tags/post/class-dynatafo-post-content.php',
        'DYNATAFO_Post_Parent_Tag'         => 'dynamic-tags/post/class-dynatafo-post-parent.php',
        'DYNATAFO_Post_Status_Tag'         => 'dynamic-tags/post/class-dynatafo-post-status.php',
        'DYNATAFO_Post_Type_Tag'           => 'dynamic-tags/post/class-dynatafo-post-type.php',
        'DYNATAFO_Are_Comments_Allowed_Tag' => 'dynamic-tags/comments/class-dynatafo-are-comments-allowed.php',
        'DYNATAFO_Is_404_Tag'              => 'dynamic-tags/post/class-dynatafo-is-404.php',
        'DYNATAFO_Is_Author_Of_Post_Tag'   => 'dynamic-tags/post/class-dynatafo-is-author-of-post.php',
        'DYNATAFO_Is_Feed_Tag'             => 'dynamic-tags/post/class-dynatafo-is-feed.php',
        'DYNATAFO_Is_Frontpage_Tag'        => 'dynamic-tags/post/class-dynatafo-is-frontpage.php',
        'DYNATAFO_Is_Home_Tag'             => 'dynamic-tags/post/class-dynatafo-is-home.php',
        'DYNATAFO_Is_Post_In_Category_Tag' => 'dynamic-tags/post/class-dynatafo-is-post-in-category.php',
        'DYNATAFO_Is_Post_In_List_Tag'     => 'dynamic-tags/post/class-dynatafo-is-post-in-list.php',
        'DYNATAFO_Is_Singular_Tag'         => 'dynamic-tags/post/class-dynatafo-is-singular.php',
        'DYNATAFO_Number_Posts_Query_Tag'  => 'dynamic-tags/post/class-dynatafo-number-posts-query.php',
        'DYNATAFO_Current_Term_Name_Tag'   => 'dynamic-tags/taxonomy/class-dynatafo-current-term-name.php',
        'DYNATAFO_Current_Term_Description_Tag' => 'dynamic-tags/taxonomy/class-dynatafo-current-term-description.php',
        'DYNATAFO_Current_Term_Image_Tag'  => 'dynamic-tags/taxonomy/class-dynatafo-current-term-image.php',
        'DYNATAFO_Archive_Title_Tag'       => 'dynamic-tags/archive/class-dynatafo-archive-title.php',
        'DYNATAFO_Site_Title_Tag'          => 'dynamic-tags/site/class-dynatafo-site-title.php',
        'DYNATAFO_Site_Tagline_Tag'        => 'dynamic-tags/site/class-dynatafo-site-tagline.php',
        'DYNATAFO_Site_URL_Tag'            => 'dynamic-tags/site/class-dynatafo-site-url.php',
        'DYNATAFO_Internal_URL_Tag'        => 'dynamic-tags/actions/class-dynatafo-internal-url.php',
        'DYNATAFO_Shortcode_URL_Tag'       => 'dynamic-tags/actions/class-dynatafo-shortcode-url.php',
        'DYNATAFO_Current_Date_Time_Tag'   => 'dynamic-tags/utils/class-dynatafo-current-date-time.php',
        'DYNATAFO_Contact_URL_Tag'         => 'dynamic-tags/actions/class-dynatafo-contact-url.php',
        'DYNATAFO_Reload_Page_URL_Tag'     => 'dynamic-tags/actions/class-dynatafo-reload-page-url.php',
        'DYNATAFO_Lightbox_URL_Tag'        => 'dynamic-tags/actions/class-dynatafo-lightbox-url.php',
        'DYNATAFO_Off_Canvas_URL_Tag'      => 'dynamic-tags/actions/class-dynatafo-off-canvas-url.php',
        'DYNATAFO_Current_Language_Tag'    => 'dynamic-tags/utils/class-dynatafo-current-language.php',
        'DYNATAFO_Current_URL_Tag'         => 'dynamic-tags/utils/class-dynatafo-current-url.php',
        'DYNATAFO_Current_User_Can_Tag'    => 'dynamic-tags/user/class-dynatafo-current-user-can.php',
        'DYNATAFO_Cookies_Tag'             => 'dynamic-tags/utils/class-dynatafo-cookies.php',
        'DYNATAFO_Server_Vars_Tag'         => 'dynamic-tags/utils/class-dynatafo-server-vars.php',
        'DYNATAFO_Session_Tag'             => 'dynamic-tags/utils/class-dynatafo-session.php',
        'DYNATAFO_Widget_Content_Tag'      => 'dynamic-tags/utils/class-dynatafo-widget-content.php',
        'DYNATAFO_Author_Name_Tag'         => 'dynamic-tags/author/class-dynatafo-author-name.php',
        'DYNATAFO_Author_Info_Tag'         => 'dynamic-tags/author/class-dynatafo-author-info.php',
        'DYNATAFO_Author_Meta_Tag'         => 'dynamic-tags/author/class-dynatafo-author-meta.php',
        'DYNATAFO_Author_Profile_Picture_Tag' => 'dynamic-tags/author/class-dynatafo-author-profile-picture.php',
        'DYNATAFO_Author_URL_Tag'          => 'dynamic-tags/author/class-dynatafo-author-url.php',
        'DYNATAFO_User_Info_Tag'           => 'dynamic-tags/user/class-dynatafo-user-info.php',
        'DYNATAFO_User_Profile_Picture_Tag' => 'dynamic-tags/user/class-dynatafo-user-profile-picture.php',
        'DYNATAFO_User_Author_Image_URL_Tag' => 'dynamic-tags/user/class-dynatafo-user-author-image-url.php',
        'DYNATAFO_User_Role_Tag'           => 'dynamic-tags/user/class-dynatafo-user-role.php',
        'DYNATAFO_Current_User_ID_Tag'     => 'dynamic-tags/user/class-dynatafo-current-user-id.php',
        'DYNATAFO_Current_User_Display_Name_Tag' => 'dynamic-tags/user/class-dynatafo-current-user-display-name.php',
        'DYNATAFO_Current_User_Email_Tag'  => 'dynamic-tags/user/class-dynatafo-current-user-email.php',
        'DYNATAFO_Current_User_Reg_Date_Tag' => 'dynamic-tags/user/class-dynatafo-current-user-reg-date.php',
        'DYNATAFO_Featured_Image_Tag'      => 'dynamic-tags/media/class-dynatafo-featured-image.php',
        'DYNATAFO_Site_Logo_Tag'           => 'dynamic-tags/site/class-dynatafo-site-logo.php',
        'DYNATAFO_Site_Icon_Tag'           => 'dynamic-tags/site/class-dynatafo-site-icon.php',
    ];

    /**
     * WooCommerce dynamic tags map: Class Name => File Path
     *
     * @var array<string, string>
     */
    private const WOO_TAGS = [
        'DYNATAFO_WooCommerce_Is_Featured_Tag' => 'dynamic-tags/woocommerce/class-dynatafo-woocommerce-is-featured.php',
        'DYNATAFO_Woo_Product_Price_Tag'       => 'dynamic-tags/woocommerce/class-dynatafo-woo-product-price.php',
        'DYNATAFO_Woo_Sale_Price_Tag'          => 'dynamic-tags/woocommerce/class-dynatafo-woo-sale-price.php',
        'DYNATAFO_Woo_SKU_Tag'                 => 'dynamic-tags/woocommerce/class-dynatafo-woo-sku.php',
        'DYNATAFO_Woo_Stock_Status_Tag'        => 'dynamic-tags/woocommerce/class-dynatafo-woo-stock-status.php',
        'DYNATAFO_Woo_Product_Categories_Tag'  => 'dynamic-tags/woocommerce/class-dynatafo-woo-product-categories.php',
        'DYNATAFO_Woo_Rating_Tag'              => 'dynamic-tags/woocommerce/class-dynatafo-woo-rating.php',
        'DYNATAFO_Woo_Add_To_Cart_URL_Tag'     => 'dynamic-tags/woocommerce/class-dynatafo-woo-add-to-cart-url.php',
    ];

    /**
     * ACF dynamic tags map: Class Name => File Path
     *
     * @var array<string, string>
     */
    private const ACF_TAGS = [
        'DYNATAFO_ACF_Field_Tag'                    => 'dynamic-tags/acf/class-dynatafo-acf-field.php',
        'DYNATAFO_ACF_Repeater_Tag'                 => 'dynamic-tags/acf/class-dynatafo-acf-repeater.php',
        'DYNATAFO_ACF_Gallery_First_Tag'            => 'dynamic-tags/acf/class-dynatafo-acf-gallery-first.php',
        'DYNATAFO_ACF_Relationship_First_Title_Tag' => 'dynamic-tags/acf/class-dynatafo-acf-relationship-first-title.php',
        'DYNATAFO_ACF_Taxonomy_Term_Tag'            => 'dynamic-tags/acf/class-dynatafo-acf-taxonomy-term.php',
        'DYNATAFO_ACF_Date_Formatted_Tag'           => 'dynamic-tags/acf/class-dynatafo-acf-date-formatted.php',
        'DYNATAFO_ACF_True_False_Tag'               => 'dynamic-tags/acf/class-dynatafo-acf-true-false.php',
        'DYNATAFO_ACF_Choice_Tag'                   => 'dynamic-tags/acf/class-dynatafo-acf-choice.php',
        'DYNATAFO_ACF_File_Tag'                     => 'dynamic-tags/acf/class-dynatafo-acf-file.php',
        'DYNATAFO_ACF_Link_URL_Tag'                 => 'dynamic-tags/acf/class-dynatafo-acf-link-url.php',
        'DYNATAFO_ACF_Link_Text_Tag'                => 'dynamic-tags/acf/class-dynatafo-acf-link-text.php',
        'DYNATAFO_ACF_Image_Data_Tag'               => 'dynamic-tags/acf/class-dynatafo-acf-image-data.php',
        'DYNATAFO_ACF_Link_Attribute_Tag'           => 'dynamic-tags/acf/class-dynatafo-acf-link-attribute.php',
    ];

    /**
     * SCF dynamic tags map: Class Name => File Path
     *
     * @var array<string, string>
     */
    private const SCF_TAGS = [
        'DYNATAFO_SCF_Field_Tag'                    => 'dynamic-tags/scf/class-dynatafo-scf-field.php',
        'DYNATAFO_SCF_Repeater_Tag'                 => 'dynamic-tags/scf/class-dynatafo-scf-repeater.php',
        'DYNATAFO_SCF_Gallery_First_Tag'            => 'dynamic-tags/scf/class-dynatafo-scf-gallery-first.php',
        'DYNATAFO_SCF_Relationship_First_Title_Tag' => 'dynamic-tags/scf/class-dynatafo-scf-relationship-first-title.php',
        'DYNATAFO_SCF_Taxonomy_Term_Tag'            => 'dynamic-tags/scf/class-dynatafo-scf-taxonomy-term.php',
        'DYNATAFO_SCF_Date_Formatted_Tag'           => 'dynamic-tags/scf/class-dynatafo-scf-date-formatted.php',
        'DYNATAFO_SCF_True_False_Tag'               => 'dynamic-tags/scf/class-dynatafo-scf-true-false.php',
        'DYNATAFO_SCF_Choice_Tag'                   => 'dynamic-tags/scf/class-dynatafo-scf-choice.php',
        'DYNATAFO_SCF_File_Tag'                     => 'dynamic-tags/scf/class-dynatafo-scf-file.php',
        'DYNATAFO_SCF_Link_URL_Tag'                 => 'dynamic-tags/scf/class-dynatafo-scf-link-url.php',
        'DYNATAFO_SCF_Link_Text_Tag'                => 'dynamic-tags/scf/class-dynatafo-scf-link-text.php',
        'DYNATAFO_SCF_Image_Data_Tag'               => 'dynamic-tags/scf/class-dynatafo-scf-image-data.php',
        'DYNATAFO_SCF_Link_Attribute_Tag'           => 'dynamic-tags/scf/class-dynatafo-scf-link-attribute.php',
    ];

    /**
     * Whether Elementor Pro is active.
     *
     * @var bool
     */
    private $is_elementor_pro_active = false;

    /**
     * Register groups from group map.
     *
     * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager
     * @param array                               $groups
     * @return void
     */
    private function register_groups( $dynamic_tags_manager, array $groups ) {
        foreach ( $groups as $id => $title ) {
            if ( empty( $id ) || empty( $title ) ) {
                continue;
            }

            $dynamic_tags_manager->register_group( $id, [ 'title' => $title ] );
        }
    }

    /**
     * Register tags from tag definitions.
     *
     * Supported item formats:
     * - 'Class_Name' => 'relative/path.php'
     * - 'Class_Name' => '' (autoloaded class)
     * - [ 'class' => 'Class_Name', 'file' => 'relative/path.php' ]
     *
     * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager
     * @param array                               $tag_definitions
     * @return void
     */
    private function register_tags_from_definitions( $dynamic_tags_manager, array $tag_definitions ) {
        foreach ( $tag_definitions as $key => $definition ) {
            $class_name = '';
            $file_path  = '';

            if ( is_array( $definition ) ) {
                $class_name = ! empty( $definition['class'] ) ? $definition['class'] : '';
                $file_path  = ! empty( $definition['file'] ) ? $definition['file'] : '';
            } elseif ( is_string( $definition ) ) {
                $class_name = is_string( $key ) ? $key : '';
                $file_path  = $definition;
            }

            if ( empty( $class_name ) ) {
                continue;
            }

            if ( ! empty( $file_path ) ) {
                $full_path = DYNATAFO_PATH . $file_path;

                if ( dynatafo_file_exists( $full_path ) ) {
                    require_once $full_path;
                }
            }

            if ( class_exists( $class_name ) ) {
                $dynamic_tags_manager->register( new $class_name() );
            }
        }
    }

    /**
     * Constructor
     *
     * @param bool $is_elementor_pro_active Optional Pro active state passed from bootstrap.
     */
    public function __construct( $is_elementor_pro_active = false ) {
        $this->is_elementor_pro_active = (bool) $is_elementor_pro_active;

        add_action( 'elementor/dynamic_tags/register', [ $this, 'register_dynatafo_content' ] );
    }

    /**
     * Check if Elementor Pro is active.
     *
     * @return bool
     */
    private function has_elementor_pro() {
        return DYNATAFO_Elementor_Compat::is_elementor_pro_active( $this->is_elementor_pro_active );
    }

    /**
     * Register All Dynamic Tag Content
     *
     * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags_manager
     */
    public function register_dynatafo_content( $dynamic_tags_manager ) {

        $has_woocommerce = DYNATAFO_Elementor_Compat::has_woocommerce();
        $has_acf         = DYNATAFO_Elementor_Compat::has_acf();
        $has_scf         = DYNATAFO_Elementor_Compat::has_scf();
        $has_pro         = $this->has_elementor_pro();

        $groups = [
            'dynatafo-post-group'     => esc_html__( 'Post (DTE)', 'dynamic-tags-for-elementor' ),
            'dynatafo-archive-group'  => esc_html__( 'Archive (DTE)', 'dynamic-tags-for-elementor' ),
            'dynatafo-site-group'     => esc_html__( 'Site (DTE)', 'dynamic-tags-for-elementor' ),
            'dynatafo-media-group'    => esc_html__( 'Media (DTE)', 'dynamic-tags-for-elementor' ),
            'dynatafo-author-group'   => esc_html__( 'Author (DTE)', 'dynamic-tags-for-elementor' ),
            'dynatafo-comments-group' => esc_html__( 'Comments (DTE)', 'dynamic-tags-for-elementor' ),
            'dynatafo-taxonomy-group' => esc_html__( 'Taxonomy (DTE)', 'dynamic-tags-for-elementor' ),
        ];

        if ( DYNATAFO_Elementor_Compat::should_register_group( 'dynatafo-actions-group', $has_pro ) ) {
            $groups['dynatafo-actions-group'] = esc_html__( 'Actions (DTE)', 'dynamic-tags-for-elementor' );
        }

        if ( $has_acf ) {
            $groups['dynatafo-acf-group'] = esc_html__( 'ACF (DTE)', 'dynamic-tags-for-elementor' );
        }

        if ( $has_scf ) {
            $groups['dynatafo-scf-group'] = esc_html__( 'SCF (DTE)', 'dynamic-tags-for-elementor' );
        }

        if ( $has_woocommerce ) {
            $groups['dynatafo-woo-group'] = esc_html__( 'WooCommerce (DTE)', 'dynamic-tags-for-elementor' );
        }

        $groups = apply_filters( 'dynatafo_dynamic_tag_groups', $groups, $dynamic_tags_manager, $this );

        if ( is_array( $groups ) ) {
            $this->register_groups( $dynamic_tags_manager, $groups );
        }

        do_action( 'dynatafo_register_dynamic_tag_groups', $dynamic_tags_manager, $this );

        // Build standard tags map in a single pass without array_merge
        $standard_tags = [];

        foreach ( self::CORE_TAGS as $tag_class => $file_path ) {
            if ( DYNATAFO_Elementor_Compat::should_register_tag( $tag_class, $has_pro ) ) {
                $standard_tags[ $tag_class ] = $file_path;
            }
        }

        if ( $has_woocommerce ) {
            $standard_tags += self::WOO_TAGS;
        }

        if ( $has_acf ) {
            $standard_tags += self::ACF_TAGS;
        }

        if ( $has_scf ) {
            $standard_tags += self::SCF_TAGS;
        }

        $standard_tags = apply_filters( 'dynatafo_dynamic_tag_definitions', $standard_tags, $dynamic_tags_manager, $this );

        if ( is_array( $standard_tags ) ) {
            $this->register_tags_from_definitions( $dynamic_tags_manager, $standard_tags );
        }

        do_action( 'dynatafo_register_dynamic_tags', $dynamic_tags_manager, $this );
    }
}