<?php
/**
 * Plugin Name: Dynamic Tags for Elementor
 * Description: Enables dynamic tag functionality for the free version of Elementor and allows creating custom tags.
 * Version: 1.4.5
 * Author: Md Mustafa Kamal Hossain
 * Text Domain: dynamic-tags-for-elementor
 * Elementor tested up to: 4.2.4
 * Elementor Pro tested up to: 4.2.3
 * Domain Path: /languages
 * Requires Plugins: elementor
 * License: GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH'))
    exit;


define('DYNATAFO_VERSION', '1.4.5');
define('DYNATAFO_PATH', plugin_dir_path(__FILE__));
define('DYNATAFO_URL', plugin_dir_url(__FILE__));
define('DYNATAFO_BASENAME', plugin_basename(__FILE__));

require_once __DIR__ . '/vendor/autoload.php';

require_once DYNATAFO_PATH . 'includes/class-dynatafo-cache.php';
require_once DYNATAFO_PATH . 'includes/functions.php';

if (!function_exists('dtfe_fs')) {
    // Create a helper function for easy SDK access.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
    function dtfe_fs()
    {
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        global $dtfe_fs;

        if (!isset($dtfe_fs)) {
            // Activate multisite network integration.
            if (!defined('WP_FS__PRODUCT_30398_MULTISITE')) {
                define('WP_FS__PRODUCT_30398_MULTISITE', true);
            }

            // Include Freemius SDK.
            // SDK is auto-loaded through Composer

            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            $dtfe_fs = fs_dynamic_init(array(
                'id' => '30398',
                'slug' => 'dynamic-tags-for-elementor',
                'premium_slug' => 'dynamic-tags-for-elementor-pro',
                'type' => 'plugin',
                'public_key' => 'pk_c3b64923485455d61dc4cece41a13',
                'is_premium' => false,
                'premium_suffix' => 'Gold',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons' => false,
                'has_paid_plans' => true,
                'is_org_compliant' => true,

                'menu' => array(
                    'slug' => 'dynatafo-info',
                    'network' => true,
                ),
            ));
        }

        return $dtfe_fs;
    }

    // Init Freemius.
    dtfe_fs();
    // Signal that SDK was initiated.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    do_action('dtfe_fs_loaded');

    // Override the support forum URL to point directly to WordPress.org.
    add_filter('fs_support_forum_url_dynamic-tags-for-elementor', function ($url) {
        return 'https://wordpress.org/support/plugin/dynamic-tags-for-elementor/';
    });

    // Register cleanup callback on Freemius's after_uninstall hook
    dtfe_fs()->add_action('after_uninstall', 'dynatafo_uninstall_cleanup');
}

/**
 * Handle uninstallation cleanup via Freemius after_uninstall hook.
 */
function dynatafo_uninstall_cleanup()
{
    // Delete the system info we collected
    delete_option('dynatafo_activation_data');

    // Clear any other settings
    delete_option('dynatafo_settings');
}

function dynatafo_activate()
{
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-activator.php';
    DYNATAFO_Activator::activate();
}
register_activation_hook(__FILE__, 'dynatafo_activate');

function dynatafo_deactivate()
{
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-deactivator.php';
    DYNATAFO_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'dynatafo_deactivate');

add_action('plugins_loaded', 'dynatafo_init_plugin');

function dynatafo_init_plugin()
{
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'dynatafo_fail_load_out_of_elementor');
        return;
    }

    $is_pro_active = defined('ELEMENTOR_PRO_VERSION');

    // Load the Tag Loader (Frontend/Editor logic)
    require_once DYNATAFO_PATH . 'includes/class-dynatafo-loader.php';
    new DYNATAFO_Loader($is_pro_active);

    // Check if version changed to bump cache version automatically
    $db_version = get_option('dynatafo_db_version');
    if ($db_version !== DYNATAFO_VERSION) {
        if (class_exists('DYNATAFO_Cache')) {
            DYNATAFO_Cache::bump_version();
        }
        update_option('dynatafo_db_version', DYNATAFO_VERSION);
    }

    // Initialize cache invalidation hooks
    if (class_exists('DYNATAFO_Cache')) {
        DYNATAFO_Cache::init_hooks();
    }

    // Load the Admin UI (Settings/Promo Pages)
    if (is_admin()) {
        require_once DYNATAFO_PATH . 'includes/class-dynatafo-admin.php';
        require_once DYNATAFO_PATH . 'includes/class-dynatafo-admin-notice-manager.php';
        new DYNATAFO_Admin();
        new DYNATAFO_Admin_Notice_Manager();
    }
}

/**
 * Admin notice if Elementor is missing
 */
function dynatafo_fail_load_out_of_elementor()
{
    $screen = get_current_screen();
    if (isset($screen->parent_base) && 'plugins' === $screen->parent_base) {
        echo '<div class="error"><p>' . esc_html__('Dynamic Tags for Elementor requires Elementor to be installed and active.', 'dynamic-tags-for-elementor') . '</p></div>';
    }
}