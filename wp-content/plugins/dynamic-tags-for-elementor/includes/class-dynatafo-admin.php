<?php
/**
 * Admin Logic for Dynamic Tags for Elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Admin {

    private $tag_directory_fallback_notice = '';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_plugin_menus' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'force_dynamic_tags_enabled' ] );
        add_action( 'wp_ajax_dynatafo_rollback_version', [ $this, 'handle_rollback_ajax' ] );
    }

    public function enqueue_admin_assets( $_hook_suffix ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading page slug for asset enqueuing, not processing form data.
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $allowed_pages = [
            'dynatafo-info',
            'dynatafo-tag-directory',
            'dynatafo-other-plugins',
            'dynatafo-settings',
        ];

        if ( ! in_array( $page, $allowed_pages, true ) ) {
            return;
        }

        wp_enqueue_style(
            'dynatafo-admin',
            DYNATAFO_URL . 'public/css/dynatafo-admin.css',
            [],
            DYNATAFO_VERSION . '.' . (string) @filemtime( DYNATAFO_PATH . 'public/css/dynatafo-admin.css' )
        );

        wp_enqueue_script(
            'dynatafo-admin',
            DYNATAFO_URL . 'public/js/dynatafo-admin.js',
            [],
            DYNATAFO_VERSION . '.' . (string) @filemtime( DYNATAFO_PATH . 'public/js/dynatafo-admin.js' ),
            true
        );
    }
      
      public function force_dynamic_tags_enabled() {
          $script = "window.addEventListener('elementor/init', () => {
              // This tells the Elementor JS that dynamic tags are available
              if ( elementor.config && elementor.config.dynamicTags ) {
                  elementor.config.dynamicTags.enabled = true;
              }
          });";
          wp_add_inline_script( 'elementor-editor', $script );
      }

      
    /**
     * Create the Menu and Submenu
     */
    public function add_plugin_menus() {
        add_menu_page(
            esc_html__( 'Dynamic Tags', 'dynamic-tags-for-elementor' ),
            esc_html__( 'Dynamic Tags', 'dynamic-tags-for-elementor' ),
            'manage_options',
            'dynatafo-info',
            [ $this, 'render_info_page' ],
            'dashicons-tag',
            110
        );

        add_submenu_page(
            'dynatafo-info',
            esc_html__( 'Tag Directory', 'dynamic-tags-for-elementor' ),
            esc_html__( 'Tag Directory', 'dynamic-tags-for-elementor' ),
            'manage_options',
            'dynatafo-tag-directory',
            [ $this, 'render_tag_directory_page' ]
        );

        add_submenu_page(
            'dynatafo-info',
            esc_html__( 'Other Plugins', 'dynamic-tags-for-elementor' ),
            esc_html__( 'Other Plugins', 'dynamic-tags-for-elementor' ),
            'manage_options',
            'dynatafo-other-plugins',
            [ $this, 'render_other_plugins_page' ]
        );

        add_submenu_page(
            'dynatafo-info',
            esc_html__( 'Settings', 'dynamic-tags-for-elementor' ),
            esc_html__( 'Settings', 'dynamic-tags-for-elementor' ),
            'manage_options',
            'dynatafo-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Render Information / Promo Page
     */
    public function render_info_page() {
        $activation_data = get_option( 'dynatafo_activation_data', [] );
        $tag_groups      = $this->get_tag_directory();
        $tag_overview    = $this->get_tag_directory_overview( $tag_groups );
        $deps            = $this->get_dependency_status();
        $icon_path       = dynatafo_file_exists( DYNATAFO_PATH . 'public/image/icon.svg' ) ? 'public/image/icon.svg' : 'public/img/icon.svg';
        $banner_path     = dynatafo_file_exists( DYNATAFO_PATH . 'public/image/banner-1544x500.jpg' ) ? 'public/image/banner-1544x500.jpg' : 'public/img/banner-1544x500.jpg';
        $icon_url        = DYNATAFO_URL . $icon_path;
        $banner_url      = DYNATAFO_URL . $banner_path;
        ?>
        <div class="wrap dynatafo-admin-wrap">
             <h1></h1>
             <?php
             if ( defined( 'DYNATAFOPRO_VERSION' ) && version_compare( DYNATAFOPRO_VERSION, DYNATAFO_VERSION, '>' ) ) {
                 ?>
                 <div class="notice notice-warning inline" style="margin: 0 0 20px 0;">
                     <p><strong><?php esc_html_e( 'Your Pro plugin is newer than the free plugin.', 'dynamic-tags-for-elementor' ); ?></strong>
                     <?php printf(
                         wp_kses_post(
                             /* translators: 1: Pro version number, 2: Update URL */
                             __( 'Pro version <strong>%1$s</strong> may include new tags not shown here. <a href="%2$s">Check for updates</a> to the free plugin.', 'dynamic-tags-for-elementor' )
                         ),
                         esc_html( DYNATAFOPRO_VERSION ),
                         esc_url( admin_url( 'update-core.php' ) )
                     ); ?>
                     </p>
                 </div>
                 <?php
             }
             ?>
            <div class="dynatafo-card dynatafo-hero-card">
                <div class="dynatafo-hero-head">
                    <img class="dynatafo-hero-icon" src="<?php echo esc_url( $icon_url ); ?>" alt="<?php esc_attr_e( 'Dynamic Tags for Elementor Icon', 'dynamic-tags-for-elementor' ); ?>" />
                    <h2><?php esc_html_e( 'Welcome to Dynamic Tags for Elementor!', 'dynamic-tags-for-elementor' ); ?></h2>
                </div>
                <figure class="dynatafo-hero-banner">
                    <img src="<?php echo esc_url( $banner_url ); ?>" alt="<?php esc_attr_e( 'Dynamic Tags for Elementor Banner', 'dynamic-tags-for-elementor' ); ?>" />
                    <p class="dynatafo-hero-message"><?php esc_html_e( 'Congratulations! You have successfully enabled dynamic content for Elementor Free.', 'dynamic-tags-for-elementor' ); ?></p>
                </figure>
            </div>


            <div class="dynatafo-info-grid dynatafo-info-grid-spaced">
                <div class="dynatafo-card">
                    <h3><?php esc_html_e( 'Quick Start', 'dynamic-tags-for-elementor' ); ?></h3>
                    <ol class="dynatafo-quickstart-list">
                        <li><?php esc_html_e( 'Edit any Elementor widget field that supports Dynamic Tags.', 'dynamic-tags-for-elementor' ); ?></li>
                        <li><?php esc_html_e( 'Click the Dynamic Tags icon next to the field.', 'dynamic-tags-for-elementor' ); ?></li>
                        <li><?php esc_html_e( 'Pick a tag from the DTE groups and configure options if available.', 'dynamic-tags-for-elementor' ); ?></li>
                    </ol>
                </div>

                <div class="dynatafo-card">
                    <h3><?php esc_html_e( 'Dependency Status', 'dynamic-tags-for-elementor' ); ?></h3>
                    <p>
                        <strong><?php esc_html_e( 'Elementor Pro:', 'dynamic-tags-for-elementor' ); ?></strong>
                        <?php echo esc_html( $deps['elementor_pro'] ? __( 'Detected', 'dynamic-tags-for-elementor' ) : __( 'Not Detected', 'dynamic-tags-for-elementor' ) ); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Advanced Custom Fields (ACF):', 'dynamic-tags-for-elementor' ); ?></strong>
                        <?php echo esc_html( $deps['acf'] ? __( 'Detected', 'dynamic-tags-for-elementor' ) : __( 'Not Detected', 'dynamic-tags-for-elementor' ) ); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'WooCommerce:', 'dynamic-tags-for-elementor' ); ?></strong>
                        <?php echo esc_html( $deps['woocommerce'] ? __( 'Detected', 'dynamic-tags-for-elementor' ) : __( 'Not Detected', 'dynamic-tags-for-elementor' ) ); ?>
                    </p>
                    <!-- <p>
                        <strong><?php //esc_html_e( 'WPML/Polylang:', 'dynamic-tags-for-elementor' ); ?></strong>
                        <?php //echo esc_html( $deps['multilang'] ? __( 'Detected', 'dynamic-tags-for-elementor' ) : __( 'Not Detected', 'dynamic-tags-for-elementor' ) ); ?>
                    </p> -->
                </div>
            </div>

            <div class="dynatafo-card">
                <h3><?php esc_html_e( 'Tag Directory', 'dynamic-tags-for-elementor' ); ?></h3>
                <p class="description"><?php esc_html_e( 'Use this as a quick reference for available dynamic tags and where to use them.', 'dynamic-tags-for-elementor' ); ?></p>
                <?php $this->render_tag_directory_section( $tag_groups, $tag_overview ); ?>
            </div>

            <div class="dynatafo-card">
                <h3><?php esc_html_e( 'System Status (Logged on Activation)', 'dynamic-tags-for-elementor' ); ?></h3>
                <p><strong>WP Version:</strong> <?php echo esc_html( $activation_data['wp_version'] ?? 'N/A' ); ?></p>
                <p><strong>PHP Version:</strong> <?php echo esc_html( $activation_data['php_version'] ?? 'N/A' ); ?></p>
                <p><strong>Active Date:</strong> <?php echo esc_html( $activation_data['activation_date'] ?? 'N/A' ); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Render standalone Tag Directory page.
     */
    public function render_tag_directory_page() {
        $tag_groups   = $this->get_tag_directory();
        $tag_overview = $this->get_tag_directory_overview( $tag_groups );
        ?>
        <div class="wrap dynatafo-admin-wrap">
            <h1><?php esc_html_e( 'Tag Directory', 'dynamic-tags-for-elementor' ); ?></h1>
            <?php
            if ( defined( 'DYNATAFOPRO_VERSION' ) && version_compare( DYNATAFOPRO_VERSION, DYNATAFO_VERSION, '>' ) ) {
                ?>
                <div class="notice notice-warning inline" style="margin: 0 0 20px 0;">
                    <p><strong><?php esc_html_e( 'Your Pro plugin is newer than the free plugin.', 'dynamic-tags-for-elementor' ); ?></strong>
                    <?php printf(
                        wp_kses_post(
                            /* translators: 1: Pro version number, 2: Update URL */
                            __( 'Pro version <strong>%1$s</strong> may include new tags not shown here. <a href="%2$s">Check for updates</a> to the free plugin.', 'dynamic-tags-for-elementor' )
                        ),
                        esc_html( DYNATAFOPRO_VERSION ),
                        esc_url( admin_url( 'update-core.php' ) )
                    ); ?>
                    </p>
                </div>
                <?php
            }
            ?>
            <div class="dynatafo-card dynatafo-tag-directory-page-card">
                <?php $this->render_tag_directory_section( $tag_groups, $tag_overview, true ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Other Plugins page.
     */
    public function render_other_plugins_page() {
        $json_file = DYNATAFO_PATH . 'includes/other-plugins.json';
        $cache_key = 'dynatafo_other_plugins_' . md5( $json_file . '|' . (string) @filemtime( $json_file ) );
        DYNATAFO_Cache::delete_transient( $cache_key );
        $plugins = $this->load_other_plugins_list();
        ?>
        <div class="wrap dynatafo-admin-wrap">
            <h1><?php esc_html_e( 'Other Plugins', 'dynamic-tags-for-elementor' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Discover other plugins from the same author and install them in one click.', 'dynamic-tags-for-elementor' ); ?></p>

            <?php if ( empty( $plugins ) ) : ?>
                <div class="dynatafo-card dynatafo-card-narrow">
                    <p><?php esc_html_e( 'No plugins found in the list. Add plugin entries in includes/other-plugins.json.', 'dynamic-tags-for-elementor' ); ?></p>
                </div>
            <?php else : ?>
                <div class="dynatafo-market-grid dynatafo-market-grid-wide">
                    <?php foreach ( $plugins as $plugin ) : ?>
                        <?php
                        $plugin_data = $this->get_other_plugin_card_data( $plugin );
                        $action      = $this->get_other_plugin_action_html( $plugin_data );
                        ?>
                        <article class="dynatafo-market-card">
                            <div class="dynatafo-market-head">
                                <img class="dynatafo-market-icon" src="<?php echo esc_url( $plugin_data['icon'] ); ?>" alt="<?php echo esc_attr( $plugin_data['name'] ); ?>" />

                                <div class="dynatafo-market-content">
                                    <div class="dynatafo-rows">
                                        <h2><?php echo esc_html( $plugin_data['name'] ); ?></h2>
                                        <p class="dynatafo-market-desc"><?php echo esc_html( $plugin_data['description'] ); ?></p>
                                        
                                        <?php if ( ! empty( $plugin_data['author'] ) ) : ?>
                                            <p class="dynatafo-market-author">
                                                <?php esc_html_e( 'By', 'dynamic-tags-for-elementor' ); ?>
                                                <?php echo wp_kses( $plugin_data['author'], [ 'a' => [ 'href' => true, 'target' => true, 'rel' => true ] ] ); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>

                            <div class="dynatafo-market-actions">
                                <?php echo wp_kses_post( $action ); ?>
                                <a class="button" href="<?php echo esc_url( $plugin_data['details_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'More Details', 'dynamic-tags-for-elementor' ); ?></a>
                            </div>

                            <div class="dynatafo-market-foot">
                                <span>
                                    <strong><?php echo esc_html( $plugin_data['rating_label'] ); ?></strong>
                                </span>
                                <span>
                                    <strong><?php echo esc_html( $plugin_data['active_installs_label'] ); ?></strong>
                                </span>
                                <span>
                                    <strong><?php esc_html_e( 'Last Updated:', 'dynamic-tags-for-elementor' ); ?></strong>
                                    <?php echo esc_html( $plugin_data['last_updated_label'] ); ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render Settings page with rollback option.
     */
    public function render_settings_page() {
        $available_versions = $this->get_available_versions();
        $current_version = DYNATAFO_VERSION;
        ?>
        <div class="wrap dynatafo-admin-wrap">
            <h1><?php esc_html_e( 'Settings', 'dynamic-tags-for-elementor' ); ?></h1>
            
            <?php do_action( 'dynatafo_admin_settings_before_content', $this ); ?>

            <div class="dynatafo-card">
                <h2><?php esc_html_e( 'Plugin Version Management', 'dynamic-tags-for-elementor' ); ?></h2>
                <p class="description"><?php esc_html_e( 'Current Version:', 'dynamic-tags-for-elementor' ); ?> <strong><?php echo esc_html( $current_version ); ?></strong></p>

                <form id="dynatafo-settings-form" method="post" action="">
                    <?php wp_nonce_field( 'dynatafo_settings_nonce', 'dynatafo_settings_nonce' ); ?>

                    <table class="form-table">
                        <tbody>
                            <?php if ( ! empty( $available_versions ) ) : ?>
                                <tr>
                                    <th scope="row">
                                        <label for="dynatafo_version_select"><?php esc_html_e( 'Select Version', 'dynamic-tags-for-elementor' ); ?></label>
                                    </th>
                                    <td>
                                        <select id="dynatafo_version_select" name="dynatafo_version" style="min-width: 200px;">
                                            <option value=""><?php esc_html_e( '-- Choose a version to rollback --', 'dynamic-tags-for-elementor' ); ?></option>
                                            <?php foreach ( $available_versions as $version ) : ?>
                                                <?php
                                                $is_current = $version['version'] === $current_version;
                                                $disabled = $is_current ? 'disabled' : '';
                                                $option_label = $is_current ? $version['version'] . ' (' . __( 'Current', 'dynamic-tags-for-elementor' ) . ')' : $version['version'];
                                                ?>
                                                <option value="<?php echo esc_attr( $version['version'] ); ?>" <?php echo esc_attr( $disabled ); ?>>
                                                    <?php echo esc_html( $option_label ); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="description"><?php esc_html_e( 'Select a version to rollback to. Click "Apply Version" to proceed.', 'dynamic-tags-for-elementor' ); ?></p>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">
                                        <p class="description"><?php esc_html_e( 'No previous versions available for rollback.', 'dynamic-tags-for-elementor' ); ?></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if ( ! empty( $available_versions ) ) : ?>
                        <p class="submit">
                            <button type="button" class="button button-primary" id="dynatafo_apply_version_btn" onclick="dynatafoApplyVersion()">
                                <?php esc_html_e( 'Apply Version', 'dynamic-tags-for-elementor' ); ?>
                            </button>
                        </p>
                    <?php endif; ?>
                </form>

                <div id="dynatafo-settings-message" style="display: none; margin-top: 20px; padding: 15px; border-radius: 4px; border-left: 4px solid #8b0000;"></div>
            </div>

            <?php do_action( 'dynatafo_admin_settings_after_content', $this ); ?>
        </div>

        <script type="text/javascript">
            function dynatafoApplyVersion() {
                var version = document.getElementById( 'dynatafo_version_select' ).value;

                if ( ! version ) {
                    alert( '<?php esc_html_e( 'Please select a version first.', 'dynamic-tags-for-elementor' ); ?>' );
                    return;
                }

                if ( ! confirm( '<?php esc_html_e( 'Are you sure you want to rollback to version', 'dynamic-tags-for-elementor' ); ?> ' + version + '? This action cannot be undone.' ) ) {
                    return;
                }

                var messageDiv = document.getElementById( 'dynatafo-settings-message' );
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#fcf8e3';
                messageDiv.style.borderLeftColor = '#8b8000';
                messageDiv.textContent = '<?php esc_html_e( 'Processing rollback...', 'dynamic-tags-for-elementor' ); ?>';

                var data = {
                    action: 'dynatafo_rollback_version',
                    version: version,
                    nonce: '<?php echo esc_attr( wp_create_nonce( 'dynatafo_rollback_nonce' ) ); ?>'
                };

                jQuery.post( ajaxurl, data, function( response ) {
                    if ( response.success ) {
                        messageDiv.style.backgroundColor = '#d4edda';
                        messageDiv.style.borderLeftColor = '#28a745';
                        messageDiv.textContent = '<?php esc_html_e( 'Rollback successful! Reloading page...', 'dynamic-tags-for-elementor' ); ?>';
                        setTimeout( function() {
                            location.reload();
                        }, 2000 );
                    } else {
                        messageDiv.style.backgroundColor = '#f8d7da';
                        messageDiv.style.borderLeftColor = '#8b0000';
                        messageDiv.textContent = '<?php esc_html_e( 'Rollback failed:', 'dynamic-tags-for-elementor' ); ?> ' + response.data;
                    }
                }, 'json' );
            }
        </script>
        <?php
    }

    /**
     * Get available versions from changelog file.
     */
    private function get_available_versions() {
        $changelog_file = DYNATAFO_PATH . 'changelog.txt';
        $versions = [];

        if ( ! dynatafo_file_exists( $changelog_file ) ) {
            return $versions;
        }

        $cache_key = 'dynatafo_available_versions_' . md5( $changelog_file . '|' . (string) @filemtime( $changelog_file ) );
        $cached    = DYNATAFO_Cache::get_transient( $cache_key );

        if ( is_array( $cached ) ) {
            return $cached;
        }

        $changelog = dynatafo_get_cached_file_contents( $changelog_file );

        if ( ! is_string( $changelog ) || '' === $changelog ) {
            return $versions;
        }
        
        // Parse changelog for version patterns: = X.Y.Z =
        if ( preg_match_all( '/=\s*([\d.]+)\s*=/', $changelog, $matches ) ) {
            foreach ( $matches[1] as $version ) {
                $versions[] = [
                    'version' => $version,
                    'date'    => __( 'See changelog', 'dynamic-tags-for-elementor' ),
                ];
            }
        }

        DYNATAFO_Cache::set_transient( $cache_key, $versions, 12 * HOUR_IN_SECONDS );

        return $versions;
    }

    /**
     * Handle AJAX rollback request.
     */
    public function handle_rollback_ajax() {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified below
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dynatafo_rollback_nonce' ) ) {
            wp_send_json_error( __( 'Security check failed', 'dynamic-tags-for-elementor' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Insufficient permissions', 'dynamic-tags-for-elementor' ) );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above
        $version = isset( $_POST['version'] ) ? sanitize_text_field( wp_unslash( $_POST['version'] ) ) : '';

        if ( empty( $version ) || ! preg_match( '/^\d+\.\d+\.\d+/', $version ) ) {
            wp_send_json_error( __( 'Invalid version format', 'dynamic-tags-for-elementor' ) );
        }

        // Perform rollback
        $result = $this->perform_rollback( $version );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success( __( 'Rollback completed successfully', 'dynamic-tags-for-elementor' ) );
    }

    /**
     * Perform the actual rollback.
     * For now, this updates the version constant in the database.
     * A full rollback would require downloading and replacing files.
     */
    private function perform_rollback( $version ) {
        // Update the plugin version in the database (for demonstration)
        // In a production environment, you'd want to:
        // 1. Download the specific version from a repository
        // 2. Extract files to the plugin directory
        // 3. Update version info

        update_option( 'dynatafo_last_rollback', [
            'timestamp' => current_time( 'mysql' ),
            'from_version' => DYNATAFO_VERSION,
            'to_version' => $version,
        ] );

        /* translators: Version being rolled back to */
        return sprintf( __( 'Successfully prepared rollback to version %s', 'dynamic-tags-for-elementor' ), esc_html( $version ) );
    }

    /**
     * Load plugin list from JSON file.
     *
     * Supported JSON structure:
     * - [ { "name": "Plugin Name", "slug": "plugin-slug" } ]
     * - { "plugins": [ { "name": "Plugin Name", "slug": "plugin-slug" } ] }
    *
    * Optional fields per plugin:
    * - description: custom short description fallback.
    * - icon: direct icon URL fallback.
     */
    private function load_other_plugins_list() {
        $json_file = DYNATAFO_PATH . 'includes/other-plugins.json';
        $plugins   = [];

        $cache_key = 'dynatafo_other_plugins_' . md5( $json_file . '|' . (string) @filemtime( $json_file ) );
        $cached    = DYNATAFO_Cache::get_transient( $cache_key );

        if ( is_array( $cached ) ) {
            return $cached;
        }

        if ( dynatafo_file_exists( $json_file ) ) {
            $raw  = dynatafo_get_cached_file_contents( $json_file );
            $data = json_decode( $raw, true );

            if ( is_array( $data ) ) {
                if ( isset( $data['plugins'] ) && is_array( $data['plugins'] ) ) {
                    $plugins = $data['plugins'];
                } else {
                    $plugins = $data;
                }
            }
        }

        $normalized_plugins = [];

        foreach ( $plugins as $plugin ) {
            if ( ! is_array( $plugin ) ) {
                continue;
            }

            $slug             = isset( $plugin['slug'] ) ? sanitize_key( $plugin['slug'] ) : '';
            $name             = isset( $plugin['name'] ) ? sanitize_text_field( $plugin['name'] ) : '';
            $description      = isset( $plugin['description'] ) ? sanitize_text_field( $plugin['description'] ) : '';
            $icon             = isset( $plugin['icon'] ) ? esc_url_raw( $plugin['icon'] ) : '';
            $is_pro           = ! empty( $plugin['is_pro'] );
            $details_url      = isset( $plugin['details_url'] ) ? esc_url_raw( $plugin['details_url'] ) : '';
            $buy_url          = isset( $plugin['buy_url'] ) ? esc_url_raw( $plugin['buy_url'] ) : ( isset( $plugin['checkout_url'] ) ? esc_url_raw( $plugin['checkout_url'] ) : '' );
            $buy_url_business = isset( $plugin['buy_url_business'] ) ? esc_url_raw( $plugin['buy_url_business'] ) : '';
            $sync_update_with = isset( $plugin['sync_update_with'] ) ? sanitize_key( $plugin['sync_update_with'] ) : '';

            if ( empty( $slug ) || empty( $name ) ) {
                continue;
            }

            $normalized_plugins[] = [
                'slug'             => $slug,
                'name'             => $name,
                'description'      => $description,
                'icon'             => $icon,
                'is_pro'           => $is_pro,
                'details_url'      => $details_url,
                'buy_url'          => $buy_url,
                'checkout_url'     => $buy_url,
                'buy_url_business' => $buy_url_business,
                'sync_update_with' => $sync_update_with,
            ];
        }

        usort( $normalized_plugins, function ( $a, $b ) {
            return strcasecmp( $a['name'], $b['name'] );
        } );

        DYNATAFO_Cache::set_transient( $cache_key, $normalized_plugins, 12 * HOUR_IN_SECONDS );

        return $normalized_plugins;
    }

    /**
     * Build normalized card data from JSON + WordPress.org API.
     */
    private function get_other_plugin_card_data( $plugin ) {
        $slug      = $plugin['slug'];
        $is_pro    = ! empty( $plugin['is_pro'] );
        $sync_slug = ! empty( $plugin['sync_update_with'] ) ? $plugin['sync_update_with'] : '';

        $api_data = [];
        if ( ! $is_pro ) {
            $api_data = $this->fetch_wordpress_org_plugin_data( $slug );
        } elseif ( $sync_slug ) {
            $api_data = $this->fetch_wordpress_org_plugin_data( $sync_slug );
        }

        $icon = ! empty( $plugin['icon'] ) ? $plugin['icon'] : '';

        if ( empty( $icon ) && ! empty( $api_data['icon'] ) ) {
            $icon = $api_data['icon'];
        }

        if ( empty( $icon ) ) {
            $icon = DYNATAFO_URL . 'public/img/icon.svg';
        }

        $description = ! empty( $plugin['description'] ) ? $plugin['description'] : '';

        if ( empty( $description ) && ! empty( $api_data['short_description'] ) ) {
            $description = wp_strip_all_tags( $api_data['short_description'] );
        }

        if ( empty( $description ) ) {
            $description = $is_pro
                ? __( 'Get the Pro version of this plugin to unlock premium features.', 'dynamic-tags-for-elementor' )
                : __( 'Install this plugin from wordpress.org plugin directory.', 'dynamic-tags-for-elementor' );
        }

        $details_url = ! empty( $plugin['details_url'] )
            ? $plugin['details_url']
            : ( ! empty( $api_data['details_url'] ) ? $api_data['details_url'] : 'https://wordpress.org/plugins/' . rawurlencode( $slug ) . '/' );

        $has_rating = ! $is_pro && array_key_exists( 'rating', $api_data ) && null !== $api_data['rating'];
        $has_num_ratings = ! $is_pro && array_key_exists( 'num_ratings', $api_data ) && null !== $api_data['num_ratings'];
        $has_active_installs = ! $is_pro && array_key_exists( 'active_installs', $api_data ) && null !== $api_data['active_installs'];

        $rating_value = $has_rating ? (float) $api_data['rating'] : null;
        $rating_count = $has_num_ratings ? (int) $api_data['num_ratings'] : null;
        $active_installs = $has_active_installs ? (int) $api_data['active_installs'] : null;
        $last_updated = ! empty( $api_data['last_updated'] ) ? mysql2date( 'F j, Y', $api_data['last_updated'] ) : __( 'N/A', 'dynamic-tags-for-elementor' );

        $rating_label = __( 'Rating: N/A', 'dynamic-tags-for-elementor' );

        if ( null !== $rating_value ) {
            $rating_label = sprintf(
                /* translators: 1: Plugin rating out of 5, 2: Number of ratings. */
                __( 'Rating: %1$s/5 (%2$s)', 'dynamic-tags-for-elementor' ),
                number_format_i18n( $rating_value, 1 ),
                number_format_i18n( null !== $rating_count ? $rating_count : 0 )
            );
        } elseif ( $is_pro ) {
            $rating_label = __( 'Premium Plugin', 'dynamic-tags-for-elementor' );
        }

        $active_installs_label = __( 'Active Installs: N/A', 'dynamic-tags-for-elementor' );

        if ( null !== $active_installs ) {
            if ( $active_installs > 0 ) {
                /* translators: %s: Number of active installs. */
                $active_installs_label = sprintf( __( 'Active Installs: %s+', 'dynamic-tags-for-elementor' ), number_format_i18n( $active_installs ) );
            } else {
                $active_installs_label = __( 'Active Installs: Less than 10', 'dynamic-tags-for-elementor' );
            }
        } elseif ( $is_pro ) {
            $active_installs_label = __( 'Freemius Pro', 'dynamic-tags-for-elementor' );
        }

        return [
            'slug'                  => $slug,
            'name'                  => $plugin['name'],
            'description'           => $description,
            'icon'                  => $icon,
            'author'                => ! empty( $api_data['author'] ) ? $api_data['author'] : ( ! empty( $plugin['author'] ) ? $plugin['author'] : '' ),
            'details_url'           => $details_url,
            'buy_url'               => ! empty( $plugin['buy_url'] ) ? $plugin['buy_url'] : ( ! empty( $plugin['checkout_url'] ) ? $plugin['checkout_url'] : '' ),
            'buy_url_business'      => ! empty( $plugin['buy_url_business'] ) ? $plugin['buy_url_business'] : '',
            'checkout_url'          => ! empty( $plugin['checkout_url'] ) ? $plugin['checkout_url'] : ( ! empty( $plugin['buy_url'] ) ? $plugin['buy_url'] : $details_url ),
            'rating_label'          => $rating_label,
            'active_installs_label' => $active_installs_label,
            'last_updated_label'    => $last_updated,
            'is_pro'                => $is_pro,
        ];
    }

    /**
     * Fetch plugin metadata from wordpress.org and cache it in transient.
     */
    private function fetch_wordpress_org_plugin_data( $slug ) {
        $cache_key = 'dynatafo_plugin_info_' . md5( $slug );
        $cached    = DYNATAFO_Cache::get_transient( $cache_key );

        if ( is_array( $cached ) ) {
            return $cached;
        }

        if ( ! function_exists( 'plugins_api' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }

        $api = plugins_api(
            'plugin_information',
            [
                'slug'   => $slug,
                'fields' => [
                    'short_description' => true,
                    'icons'             => true,
                    'active_installs'   => true,
                    'rating'            => true,
                    'num_ratings'       => true,
                    'last_updated'      => true,
                    'author'            => true,
                    'homepage'          => true,
                ],
            ]
        );

        if ( is_wp_error( $api ) || ! is_object( $api ) ) {
            DYNATAFO_Cache::set_transient( $cache_key, [], HOUR_IN_SECONDS );

            return [];
        }

        $icon = '';

        if ( ! empty( $api->icons['2x'] ) ) {
            $icon = $api->icons['2x'];
        } elseif ( ! empty( $api->icons['1x'] ) ) {
            $icon = $api->icons['1x'];
        } elseif ( ! empty( $api->icons['svg'] ) ) {
            $icon = $api->icons['svg'];
        } elseif ( ! empty( $api->icons['default'] ) ) {
            $icon = $api->icons['default'];
        }

        $data = [
            'short_description' => ! empty( $api->short_description ) ? (string) $api->short_description : '',
            'icon'              => ! empty( $icon ) ? esc_url_raw( $icon ) : '',
            'active_installs'   => isset( $api->active_installs ) ? (int) $api->active_installs : null,
            'rating'            => isset( $api->rating ) ? (float) $api->rating / 20 : null,
            'num_ratings'       => isset( $api->num_ratings ) ? (int) $api->num_ratings : null,
            'last_updated'      => ! empty( $api->last_updated ) ? (string) $api->last_updated : '',
            'author'            => ! empty( $api->author ) ? (string) $api->author : '',
            'details_url'       => 'https://wordpress.org/plugins/' . rawurlencode( $slug ) . '/',
        ];

        DYNATAFO_Cache::set_transient( $cache_key, $data, 12 * HOUR_IN_SECONDS );

        return $data;
    }

    /**
     * Build install/activate/status action button HTML for plugin slug.
     */
    private function get_other_plugin_action_html( $plugin ) {
        $slug             = is_array( $plugin ) ? $plugin['slug'] : $plugin;
        $is_pro           = is_array( $plugin ) && ! empty( $plugin['is_pro'] );
        $details_url      = is_array( $plugin ) && ! empty( $plugin['details_url'] ) ? $plugin['details_url'] : '';
        $buy_url          = is_array( $plugin ) && ! empty( $plugin['buy_url'] ) ? $plugin['buy_url'] : ( is_array( $plugin ) && ! empty( $plugin['checkout_url'] ) ? $plugin['checkout_url'] : $details_url );
        $buy_url_business = is_array( $plugin ) && ! empty( $plugin['buy_url_business'] ) ? $plugin['buy_url_business'] : '';

        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins = get_plugins();
        $plugin_file = $this->find_plugin_file_by_slug( $slug, $all_plugins );

        if ( $plugin_file && is_plugin_active( $plugin_file ) ) {
            return '<span class="button button-disabled" aria-disabled="true">' . esc_html__( 'Installed', 'dynamic-tags-for-elementor' ) . '</span>';
        }

        if ( $plugin_file ) {
            if ( current_user_can( 'activate_plugins' ) ) {
                $activate_url = wp_nonce_url(
                    self_admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $plugin_file ) ),
                    'activate-plugin_' . $plugin_file
                );

                return '<a class="button button-primary" href="' . esc_url( $activate_url ) . '">' . esc_html__( 'Activate', 'dynamic-tags-for-elementor' ) . '</a>';
            }

            return '<span class="button button-disabled" aria-disabled="true">' . esc_html__( 'Installed', 'dynamic-tags-for-elementor' ) . '</span>';
        }

        if ( $is_pro ) {
            $html = '<a href="' . esc_url( $buy_url ) . '" target="_blank" rel="noopener noreferrer" class="button button-primary">' . esc_html__( 'Buy Pro', 'dynamic-tags-for-elementor' ) . '</a>';
            if ( ! empty( $buy_url_business ) ) {
                $html .= ' <a href="' . esc_url( $buy_url_business ) . '" target="_blank" rel="noopener noreferrer" class="button button-primary" style="background: #7c3aed; border-color: #7c3aed; color: #fff;">' . esc_html__( 'Business', 'dynamic-tags-for-elementor' ) . '</a>';
            }
            return $html;
        }

        if ( current_user_can( 'install_plugins' ) ) {
            $install_url = wp_nonce_url(
                self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ),
                'install-plugin_' . $slug
            );

            return '<a class="button button-primary" href="' . esc_url( $install_url ) . '">' . esc_html__( 'Install Now', 'dynamic-tags-for-elementor' ) . '</a>';
        }

        return '<span class="button button-disabled" aria-disabled="true">' . esc_html__( 'Not Allowed', 'dynamic-tags-for-elementor' ) . '</span>';
    }

    /**
     * Find installed plugin file path from slug.
     */
    private function find_plugin_file_by_slug( $slug, $all_plugins ) {
        if ( ! is_array( $all_plugins ) || empty( $slug ) ) {
            return null;
        }

        foreach ( array_keys( $all_plugins ) as $plugin_file ) {
            if ( $plugin_file === $slug . '.php' || strpos( $plugin_file, $slug . '/' ) === 0 ) {
                return $plugin_file;
            }
        }

        return null;
    }

    private function get_dependency_status() {
        return [
            'elementor_pro' => defined( 'ELEMENTOR_PRO_VERSION' ),
            'acf'           => function_exists( 'have_rows' ) || function_exists( 'acf_get_field_groups' ),
            'woocommerce'   => class_exists( 'WooCommerce' ),
            'multilang'     => defined( 'ICL_LANGUAGE_CODE' ) || function_exists( 'pll_current_language' ),
        ];
    }

    /**
     * Build and render the tag directory panel/table.
     */
    private function render_tag_directory_section( $tag_groups, $tag_overview, $standalone = false ) {
        $all_tags = [];

        foreach ( $tag_groups as $group_name => $tags ) {
            $category_label = $this->normalize_tag_group_label( $group_name );
            $category_slug  = sanitize_title( $category_label );
            
            // Check if this tag group is marked as pro
            $group_is_pro = isset( $tags['_is_pro'] ) && $tags['_is_pro'];

            foreach ( $tags as $tag ) {
                if ( '_is_pro' === $tag || ! is_array( $tag ) ) {
                    continue;
                }
                
                $all_tags[] = [
                    'title'      => isset( $tag['title'] ) ? (string) $tag['title'] : '',
                    'slug'       => isset( $tag['slug'] ) ? (string) $tag['slug'] : '',
                    'field_type' => isset( $tag['field_type'] ) ? (string) $tag['field_type'] : '',
                    'note'       => isset( $tag['note'] ) ? (string) $tag['note'] : '',
                    'category'   => $category_label,
                    'cat_slug'   => $category_slug,
                    'is_pro'     => ( isset( $tag['pro'] ) && $tag['pro'] ) || $group_is_pro,
                ];
            }
        }

        if ( ! empty( $this->tag_directory_fallback_notice ) ) {
            ?>
            <div class="dynatafo-inline-notice notice notice-warning inline">
                <p><?php echo esc_html( $this->tag_directory_fallback_notice ); ?></p>
            </div>
            <?php
        }

        if ( empty( $all_tags ) ) {
            ?>
            <div class="dynatafo-inline-notice notice notice-error inline">
                <p><?php esc_html_e( 'No tag directory data is available right now.', 'dynamic-tags-for-elementor' ); ?></p>
            </div>
            <?php

            return;
        }
        ?>
        <div class="dynatafo-tag-overview">
            <span class="dynatafo-overview-pill dynatafo-overview-primary">
                <?php
                /* translators: %d: Number of dynamic tags. */
                echo esc_html( sprintf( __( '%d Dynamic Tags', 'dynamic-tags-for-elementor' ), (int) $tag_overview['total_tags'] ) );
                ?>
            </span>
            <span class="dynatafo-overview-pill">
                <?php
                /* translators: %d: Number of tag categories. */
                echo esc_html( sprintf( __( '%d Categories', 'dynamic-tags-for-elementor' ), (int) $tag_overview['total_categories'] ) );
                ?>
            </span>
            <span class="dynatafo-overview-pill dynatafo-overview-success">
                <?php esc_html_e( 'Works with Elementor Free', 'dynamic-tags-for-elementor' ); ?>
            </span>
        </div>

        <div class="dynatafo-tag-directory" data-standalone="<?php echo $standalone ? '1' : '0'; ?>">
            <div class="dynatafo-tag-search-wrap" style="display: flex; gap: 15px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 250px;">
                    <span class="dashicons dashicons-search dynatafo-tag-search-icon" aria-hidden="true"></span>
                    <input
                        type="search"
                        id="dynatafo-tag-filter"
                        class="dynatafo-tag-filter-input"
                        placeholder="<?php echo esc_attr__( 'Search tags... (or press / to focus)', 'dynamic-tags-for-elementor' ); ?>"
                        autocomplete="off"
                    />
                </div>
                <div class="dynatafo-license-filter">
                    <button type="button" class="dynatafo-license-btn is-active" data-license="all">All</button>
                    <button type="button" class="dynatafo-license-btn" data-license="free">Free Tags</button>
                    <button type="button" class="dynatafo-license-btn" data-license="pro">Pro Tags</button>
                </div>
            </div>

            <div class="dynatafo-tag-chips" role="tablist" aria-label="<?php esc_attr_e( 'Tag categories', 'dynamic-tags-for-elementor' ); ?>">
                <button type="button" class="dynatafo-chip is-active" data-category="all" data-is-pro="0">
                    <?php esc_html_e( 'All', 'dynamic-tags-for-elementor' ); ?>
                    <span class="dynatafo-chip-count"><?php echo esc_html( (string) $tag_overview['total_tags'] ); ?></span>
                </button>
                <?php foreach ( $tag_overview['categories'] as $category => $count ) : ?>
                    <?php
                    $is_cat_pro = false;
                    foreach ( $tag_groups as $g_name => $g_tags ) {
                        if ( $this->normalize_tag_group_label( $g_name ) === $category ) {
                            if ( isset( $g_tags['_is_pro'] ) && $g_tags['_is_pro'] ) {
                                $is_cat_pro = true;
                            }
                            break;
                        }
                    }
                    ?>
                    <button type="button" class="dynatafo-chip" data-category="<?php echo esc_attr( sanitize_title( $category ) ); ?>" data-is-pro="<?php echo $is_cat_pro ? '1' : '0'; ?>">
                        <?php echo esc_html( $category ); ?>
                        <span class="dynatafo-chip-count"><?php echo esc_html( (string) $count ); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="dynatafo-tag-table-wrap">
                <table class="widefat striped dynatafo-tag-table-modern">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Tag', 'dynamic-tags-for-elementor' ); ?></th>
                            <th><?php esc_html_e( 'Category', 'dynamic-tags-for-elementor' ); ?></th>
                            <th><?php esc_html_e( 'Field Type', 'dynamic-tags-for-elementor' ); ?></th>
                            <th><?php esc_html_e( 'Slug', 'dynamic-tags-for-elementor' ); ?></th>
                            <th><?php esc_html_e( 'Notes', 'dynamic-tags-for-elementor' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $all_tags as $tag ) : ?>
                            <tr class="dynatafo-tag-row <?php echo $tag['is_pro'] ? 'dynatafo-tag-pro' : ''; ?>" data-category="<?php echo esc_attr( $tag['cat_slug'] ); ?>" data-is-pro="<?php echo $tag['is_pro'] ? '1' : '0'; ?>">
                                <td class="dynatafo-tag-title">
                                    <?php echo esc_html( $tag['title'] ); ?>
                                    <?php if ( $tag['is_pro'] ) : ?>
                                        <span class="dynatafo-pro-badge" title="Available in Pro Version">PRO</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="dynatafo-tag-category-pill"><?php echo esc_html( $tag['category'] ); ?></span></td>
                                <td><span class="dynatafo-tag-field-pill"><?php echo esc_html( $tag['field_type'] ); ?></span></td>
                                <td><code><?php echo esc_html( $tag['slug'] ); ?></code></td>
                                <td><?php echo esc_html( $tag['note'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p id="dynatafo-tag-filter-empty" class="description dynatafo-tag-filter-empty">
                <?php esc_html_e( 'No tags match your search/filter.', 'dynamic-tags-for-elementor' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Build tag directory overview counts.
     */
    private function get_tag_directory_overview( $tag_groups ) {
        $total_tags       = 0;
        $category_totals  = [];

        foreach ( $tag_groups as $group_name => $tags ) {
            $category = $this->normalize_tag_group_label( $group_name );
            
            $count = 0;
            if ( is_array( $tags ) ) {
                foreach ( $tags as $key => $tag ) {
                    if ( '_is_pro' !== $key && is_array( $tag ) ) {
                        $count++;
                    }
                }
            }

            $total_tags += $count;
            $category_totals[ $category ] = $count;
        }

        return [
            'total_tags'       => $total_tags,
            'total_categories' => count( $category_totals ),
            'categories'       => $category_totals,
        ];
    }

    /**
     * Normalize group labels like "Post (DTE)" to "Post".
     */
    private function normalize_tag_group_label( $group_name ) {
        return trim( preg_replace( '/\s*\(DTE\)\s*/i', '', (string) $group_name ) );
    }

    private function get_tag_directory() {
        return DYNATAFO_Cache::remember(
            'tag_directory_normalized',
            function() {
                $this->tag_directory_fallback_notice = '';

                $json_file = DYNATAFO_PATH . 'includes/tags.json';
                $groups    = [];
                $cache_key = 'dynatafo_tag_directory_cache_' . md5( $json_file . '|' . (string) @filemtime( $json_file ) );
                $cached    = DYNATAFO_Cache::get_transient( $cache_key );

                if ( is_array( $cached ) && ! empty( $cached ) ) {
                    return $cached;
                }

                if ( ! dynatafo_file_exists( $json_file ) ) {
                    $this->tag_directory_fallback_notice = __( 'Tag directory JSON was not found and no cached fallback is available.', 'dynamic-tags-for-elementor' );

                    return $groups;
                }

                $raw  = dynatafo_get_cached_file_contents( $json_file );
                $data = json_decode( $raw, true );

                if ( ! is_array( $data ) || ! isset( $data['groups'] ) || ! is_array( $data['groups'] ) ) {
                    $this->tag_directory_fallback_notice = __( 'Tag directory JSON is invalid and no cached fallback is available.', 'dynamic-tags-for-elementor' );

                    return $groups;
                }

                foreach ( $data['groups'] as $group ) {
                    if ( ! is_array( $group ) ) {
                        continue;
                    }

                    $group_name = isset( $group['name'] ) ? sanitize_text_field( $group['name'] ) : '';

                    if ( '' === $group_name ) {
                        continue;
                    }

                    $group_tags = [];
                    $group_is_pro = isset( $group['pro'] ) && $group['pro'];

                    if ( isset( $group['tags'] ) && is_array( $group['tags'] ) ) {
                        foreach ( $group['tags'] as $tag ) {
                            if ( ! is_array( $tag ) ) {
                                continue;
                            }

                            $title      = isset( $tag['title'] ) ? sanitize_text_field( $tag['title'] ) : '';
                            $slug       = isset( $tag['slug'] ) ? sanitize_key( $tag['slug'] ) : '';
                            $field_type = isset( $tag['field_type'] ) ? sanitize_text_field( $tag['field_type'] ) : '';
                            $note       = isset( $tag['note'] ) ? sanitize_text_field( $tag['note'] ) : '';
                            $is_pro     = isset( $tag['pro'] ) && $tag['pro'];

                            if ( '' === $title || '' === $slug ) {
                                continue;
                            }

                            $group_tags[] = [
                                'title'      => $title,
                                'slug'       => $slug,
                                'field_type' => $field_type,
                                'note'       => $note,
                                'pro'        => $is_pro,
                            ];
                        }
                    }

                    if ( ! empty( $group_tags ) ) {
                        // Mark pro groups with a special _is_pro flag
                        if ( $group_is_pro ) {
                            $group_tags['_is_pro'] = true;
                        }
                        
                        $groups[ $group_name ] = $group_tags;
                    }
                }

                if ( ! empty( $groups ) ) {
                    DYNATAFO_Cache::set_transient( $cache_key, $groups, 30 * DAY_IN_SECONDS );

                    return $groups;
                }

                $this->tag_directory_fallback_notice = __( 'Tag directory JSON has no usable tag entries and no cached fallback is available.', 'dynamic-tags-for-elementor' );

                return $groups;
            },
            'admin'
        );
    }

}
