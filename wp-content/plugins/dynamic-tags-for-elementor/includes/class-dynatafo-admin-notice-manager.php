<?php
/**
 * Admin Notice Manager for Dynamic Tags for Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DYNATAFO_Admin_Notice_Manager {

    /**
     * Per-user dismissed notice state.
     *
     * @var string
     */
    private $user_meta_key = 'dynatafo_dismissed_notices';

    /**
     * Notice definitions loaded from includes/notices/*.php.
     *
     * @var array<int,array<string,mixed>>
     */
    private $notices = [];

    public function __construct() {
        $this->notices = $this->load_notices_from_files();

        add_action( 'admin_notices', [ $this, 'render_admin_notices' ], 5 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_dynatafo_dismiss_notice', [ $this, 'ajax_dismiss_notice' ] );
        add_action( 'admin_init', [ $this, 'maybe_cleanup_stale_dismissed_notices' ] );
        add_action( 'admin_post_dynatafo_dismiss_notice', [ $this, 'post_dismiss_notice' ] );
    }

    /**
     * Load notice definitions from includes/notices directory.
     *
     * Each file can return:
     * - one notice array
     * - or an array of notice arrays
     *
     * @return array<int,array<string,mixed>>
     */
    private function load_notices_from_files() {
        $notices = [];
        $dir     = DYNATAFO_PATH . 'includes/notices';

        if ( ! is_dir( $dir ) ) {
            return $notices;
        }

        $files = dynatafo_get_cached_glob( $dir . '/*.php' );

        if ( empty( $files ) || ! is_array( $files ) ) {
            return $notices;
        }

        sort( $files );

        foreach ( $files as $file ) {
            $loaded = include $file;

            if ( ! is_array( $loaded ) ) {
                continue;
            }

            // Single notice definition.
            if ( isset( $loaded['id'] ) ) {
                $notice = $this->normalize_notice( $loaded );
                if ( null !== $notice ) {
                    $notices[] = $notice;
                }
                continue;
            }

            // Multiple notice definitions.
            foreach ( $loaded as $item ) {
                if ( ! is_array( $item ) ) {
                    continue;
                }

                $notice = $this->normalize_notice( $item );

                if ( null !== $notice ) {
                    $notices[] = $notice;
                }
            }
        }

        // Sort notices by priority (default 100, lower priority numbers render first).
        usort( $notices, function( $a, $b ) {
            $prio_a = isset( $a['priority'] ) ? (int) $a['priority'] : 100;
            $prio_b = isset( $b['priority'] ) ? (int) $b['priority'] : 100;
            return $prio_a <=> $prio_b;
        } );

        return $notices;
    }

    /**
     * Validate and normalize one notice definition.
     *
     * @param array<string,mixed> $notice Raw notice definition.
     * @return array<string,mixed>|null
     */
    private function normalize_notice( $notice ) {
        $defaults = [
            'id'       => '',
            'type'     => 'info',
            'title'    => '',
            'message'  => '',
            'version'  => DYNATAFO_VERSION,
            'priority' => 100,
        ];

        $notice = wp_parse_args( $notice, $defaults );

        $notice['id']      = sanitize_key( (string) $notice['id'] );
        $notice['type']    = sanitize_key( (string) $notice['type'] );
        
        // Handle callable title (for deferred translation loading)
        if ( is_callable( $notice['title'] ) ) {
            $notice['title'] = $notice['title'];
        } else {
            $notice['title'] = (string) $notice['title'];
        }
        
        // Handle callable messages (for deferred translation loading)
        if ( is_callable( $notice['message'] ) ) {
            $notice['message'] = $notice['message'];
        } else {
            $notice['message'] = (string) $notice['message'];
        }
        
        $notice['version']  = sanitize_text_field( (string) $notice['version'] );
        $notice['priority'] = isset( $notice['priority'] ) ? (int) $notice['priority'] : 100;

        if ( empty( $notice['id'] ) ) {
            return null;
        }

        // Check if title and message are non-empty (either callable or string)
        $has_title = ( is_callable( $notice['title'] ) ) || ( ! empty( $notice['title'] ) );
        $has_message = ( is_callable( $notice['message'] ) ) || ( ! empty( $notice['message'] ) );

        if ( ! $has_title || ! $has_message ) {
            return null;
        }

        if ( ! in_array( $notice['type'], [ 'success', 'info', 'warning', 'error' ], true ) ) {
            $notice['type'] = 'info';
        }

        return $notice;
    }

    /**
     * Render all active notices.
     *
     * @return void
     */
    public function render_admin_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        foreach ( $this->notices as $notice ) {
            if ( ! $this->should_render_notice( $notice['id'], (string) $notice['version'] ) ) {
                continue;
            }

            $class = 'notice notice-' . sanitize_html_class( $notice['type'] ) . ' is-dismissible dynatafo-admin-notice';
            
            // Handle callable title and message (for deferred translation loading)
            $title = $notice['title'];
            if ( is_callable( $title ) ) {
                $title = call_user_func( $title );
            }
            
            $message = $notice['message'];
            if ( is_callable( $message ) ) {
                $message = call_user_func( $message );
            }

            // Build a server-side fallback dismiss URL (for non-JS environments).
            $dismiss_url = wp_nonce_url(
                admin_url( 'admin-post.php?action=dynatafo_dismiss_notice&notice_id=' . rawurlencode( $notice['id'] ) . '&version=' . rawurlencode( (string) $notice['version'] ) ),
                'dynatafo_dismiss_notice'
            );
            ?>
            <div class="<?php echo esc_attr( $class ); ?>"
                 data-notice-id="<?php echo esc_attr( $notice['id'] ); ?>"
                 data-notice-version="<?php echo esc_attr( (string) $notice['version'] ); ?>">
                <p><strong><?php echo esc_html( $title ); ?></strong></p>
                <p><?php echo wp_kses_post( $message ); ?></p>
                <p style="margin-top:8px;"><a class="dynatafo-server-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"><?php echo esc_html__( 'Dismiss', 'dynamic-tags-for-elementor' ); ?></a></p>
            </div>
            <?php

            // Auto dismiss 'release-live' notices immediately on load so they are only displayed once.
            if ( 'release-live' === $notice['id'] ) {
                $user_id = get_current_user_id();
                if ( $user_id ) {
                    $runtime_key = sanitize_key( (string) $notice['id'] ) . '@' . sanitize_text_field( (string) $notice['version'] );
                    $state       = get_user_meta( $user_id, $this->user_meta_key, true );
                    if ( ! is_array( $state ) ) {
                        $state = [];
                    }
                    $state[ $runtime_key ] = time();
                    update_user_meta( $user_id, $this->user_meta_key, $state );
                }
            }
        }
    }

    /**
     * Enqueue inline dismissal script.
     *
     * @return void
     */
    public function enqueue_scripts() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        wp_register_script( 'dynatafo-notice-manager', false, [ 'jquery' ], DYNATAFO_VERSION, true );
        wp_enqueue_script( 'dynatafo-notice-manager' );

        wp_localize_script(
            'dynatafo-notice-manager',
            'dynatafoNoticeManager',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'dynatafo_dismiss_notice' ),
            ]
        );

        $script = <<<'JS'
jQuery(function($){
    $(document).on('click', '.dynatafo-admin-notice .notice-dismiss', function(){
        var $notice = $(this).closest('.dynatafo-admin-notice');
        var noticeId = $notice.data('notice-id');
        var version = $notice.data('notice-version');

        if (!noticeId || !version || typeof dynatafoNoticeManager === 'undefined') {
            return;
        }

        $.post(dynatafoNoticeManager.ajaxUrl, {
            action: 'dynatafo_dismiss_notice',
            notice_id: noticeId,
            version: version,
            _ajax_nonce: dynatafoNoticeManager.nonce
        });
    });
});
JS;

        wp_add_inline_script( 'dynatafo-notice-manager', $script );
    }

    /**
     * Handle notice dismissal.
     *
     * @return void
     */
    public function ajax_dismiss_notice() {
        check_ajax_referer( 'dynatafo_dismiss_notice' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Forbidden' ], 403 );
        }

        $notice_id = isset( $_POST['notice_id'] ) ? sanitize_key( wp_unslash( $_POST['notice_id'] ) ) : '';
        $version   = isset( $_POST['version'] ) ? sanitize_text_field( wp_unslash( $_POST['version'] ) ) : '';

        if ( empty( $notice_id ) || empty( $version ) ) {
            wp_send_json_error( [ 'message' => 'Missing notice data' ], 400 );
        }

        // Normalize the runtime key the same way it's computed when rendering.
        $runtime_key = sanitize_key( (string) $notice_id ) . '@' . sanitize_text_field( (string) $version );
        $user_id     = get_current_user_id();

        if ( ! $user_id ) {
            wp_send_json_error( [ 'message' => 'Invalid user' ], 400 );
        }

        $state = get_user_meta( $user_id, $this->user_meta_key, true );

        if ( ! is_array( $state ) ) {
            $state = [];
        }

        $state[ $runtime_key ] = time();
        update_user_meta( $user_id, $this->user_meta_key, $state );

        wp_send_json_success();
    }

    /**
     * Server-side fallback for dismissing a notice via a GET/POST to admin-post.php.
     * Uses the same nonce/action as the AJAX handler so the link can be used when JS
     * is unavailable. Redirects back to the referrer after recording dismissal.
     *
     * @return void
     */
    public function post_dismiss_notice() {
        check_admin_referer( 'dynatafo_dismiss_notice' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Forbidden', 'dynamic-tags-for-elementor' ), '', [ 'response' => 403 ] );
        }

        $notice_id = isset( $_REQUEST['notice_id'] ) ? sanitize_key( wp_unslash( $_REQUEST['notice_id'] ) ) : '';
        $version   = isset( $_REQUEST['version'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['version'] ) ) : '';

        if ( empty( $notice_id ) || empty( $version ) ) {
            wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
            exit;
        }

        $runtime_key = sanitize_key( (string) $notice_id ) . '@' . sanitize_text_field( (string) $version );
        $user_id     = get_current_user_id();

        if ( ! $user_id ) {
            wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
            exit;
        }

        $state = get_user_meta( $user_id, $this->user_meta_key, true );

        if ( ! is_array( $state ) ) {
            $state = [];
        }

        $state[ $runtime_key ] = time();
        update_user_meta( $user_id, $this->user_meta_key, $state );

        wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
        exit;
    }

    /**
     * Remove stale dismissed-notice entries from user meta when plugin version changes.
     *
     * This runs once per plugin version (tracked in an option) and prunes any stored
     * dismissed keys that don't correspond to a currently defined notice. This keeps
     * the user meta tidy and prevents inconsistent state from lingering.
     *
     * @return void
     */
    public function maybe_cleanup_stale_dismissed_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $option_key = 'dynatafo_notice_cleanup_version';
        $last_run   = get_option( $option_key, '' );

        if ( $last_run === (string) DYNATAFO_VERSION ) {
            return;
        }

        // Build set of valid runtime keys from current notice definitions.
        $valid_keys = [];
        foreach ( $this->notices as $notice ) {
            $id      = isset( $notice['id'] ) ? sanitize_key( (string) $notice['id'] ) : '';
            $version = isset( $notice['version'] ) ? sanitize_text_field( (string) $notice['version'] ) : '';
            if ( $id && $version ) {
                $valid_keys[] = $id . '@' . $version;
            }
        }

        // Iterate users and prune stale keys.
        $users = get_users( [ 'fields' => 'ID' ] );

        if ( ! empty( $users ) && is_array( $users ) ) {
            foreach ( $users as $user_id ) {
                $state = get_user_meta( $user_id, $this->user_meta_key, true );

                if ( ! is_array( $state ) || empty( $state ) ) {
                    continue;
                }

                $changed = false;

                foreach ( $state as $runtime_key => $val ) {
                    if ( ! in_array( $runtime_key, $valid_keys, true ) ) {
                        unset( $state[ $runtime_key ] );
                        $changed = true;
                    }
                }

                if ( $changed ) {
                    // If the state became empty, delete the meta to keep DB small.
                    if ( empty( $state ) ) {
                        delete_user_meta( $user_id, $this->user_meta_key );
                    } else {
                        update_user_meta( $user_id, $this->user_meta_key, $state );
                    }
                }
            }
        }

        update_option( $option_key, (string) DYNATAFO_VERSION );
    }

    /**
     * Check if a notice should render for current user and plugin version.
     *
     * @param string $notice_id Notice identifier.
     * @return bool
     */
    private function should_render_notice( $notice_id, $version ) {
        $user_id = get_current_user_id();

        if ( ! $user_id ) {
            return false;
        }

        $state = get_user_meta( $user_id, $this->user_meta_key, true );

        if ( ! is_array( $state ) ) {
            return true;
        }

        $runtime_key = sanitize_key( (string) $notice_id ) . '@' . sanitize_text_field( (string) $version );

        return ! isset( $state[ $runtime_key ] );
    }
}
