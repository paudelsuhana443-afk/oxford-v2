<?php
/**
 * Notice: Review / Rate Us — persists across versions, re-shows after 15 days on dismiss,
 * permanently hidden once the user clicks the review button, and tracks impression count.
 *
 * This file intentionally returns an EMPTY array because the review notice is
 * handled directly by the notice manager via its own dedicated hook registration
 * inside this file. The file is loaded by the notice-file-loader, but the actual
 * notice is rendered through dedicated callbacks added here.
 *
 * Key behaviours:
 *  - NOT tied to a specific plugin version (persists across all versions).
 *  - First shown only AFTER the What's New notice for the current version has been dismissed.
 *  - On dismiss → snooze for 15 days, then re-appear.
 *  - On "Leave a Review" click → permanently hidden (never shows again).
 *  - Impression count incremented each time the notice is displayed.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ─── Constants ───────────────────────────────────────────────────────────────
 */
if ( ! defined( 'DYNATAFO_REVIEW_META_KEY' ) ) {
    define( 'DYNATAFO_REVIEW_META_KEY', 'dynatafo_review_notice' );
}
if ( ! defined( 'DYNATAFO_REVIEW_SNOOZE_DAYS' ) ) {
    define( 'DYNATAFO_REVIEW_SNOOZE_DAYS', 15 );
}

/**
 * ─── Helper: Get review notice state for a user ──────────────────────────────
 *
 * Returns an array with keys:
 *   'reviewed'    => bool   – user already clicked the review button.
 *   'snoozed_at'  => int    – Unix timestamp of last dismiss (0 = never dismissed).
 *   'impressions' => int    – total times the notice has been shown.
 *
 * @param int $user_id
 * @return array{reviewed: bool, snoozed_at: int, impressions: int}
 */
function dynatafo_get_review_notice_state( $user_id ) {
    $default = [
        'reviewed'    => false,
        'snoozed_at'  => 0,
        'impressions' => 0,
    ];

    $state = get_user_meta( (int) $user_id, DYNATAFO_REVIEW_META_KEY, true );

    if ( ! is_array( $state ) ) {
        return $default;
    }

    return array_merge( $default, $state );
}

/**
 * ─── Helper: Save review notice state for a user ─────────────────────────────
 *
 * @param int   $user_id
 * @param array $state
 * @return void
 */
function dynatafo_save_review_notice_state( $user_id, array $state ) {
    update_user_meta( (int) $user_id, DYNATAFO_REVIEW_META_KEY, $state );
}

/**
 * ─── Helper: Check if What's New notice is still active for current user ──────
 *
 * We consider it "active" if it has NOT been dismissed yet by the user.
 *
 * @param int $user_id
 * @return bool
 */
function dynatafo_is_whats_new_active( $user_id ) {
    $dismissed_meta = get_user_meta( (int) $user_id, 'dynatafo_dismissed_notices', true );
    if ( ! is_array( $dismissed_meta ) ) {
        return true; // Not dismissed yet.
    }
    $runtime_key = 'whats-new@' . DYNATAFO_VERSION;
    return ! isset( $dismissed_meta[ $runtime_key ] );
}

/**
 * ─── Helper: Decide whether to render the review notice for current user ──────
 *
 * @param int $user_id
 * @return bool
 */
function dynatafo_should_show_review_notice( $user_id ) {
    // Never show while What's New is still active.
    if ( dynatafo_is_whats_new_active( $user_id ) ) {
        return false;
    }

    $state = dynatafo_get_review_notice_state( $user_id );

    // Permanently hidden once reviewed.
    if ( ! empty( $state['reviewed'] ) ) {
        return false;
    }

    // Not yet snoozed → show immediately.
    if ( empty( $state['snoozed_at'] ) ) {
        return true;
    }

    // Snoozed → only re-show after the configured number of days.
    $snooze_seconds = DYNATAFO_REVIEW_SNOOZE_DAYS * DAY_IN_SECONDS;
    return ( time() - (int) $state['snoozed_at'] ) >= $snooze_seconds;
}

/**
 * ─── Render ──────────────────────────────────────────────────────────────────
 */
add_action(
    'admin_notices',
    function () {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $user_id = get_current_user_id();

        if ( ! $user_id || ! dynatafo_should_show_review_notice( $user_id ) ) {
            return;
        }

        // Increment impression counter.
        $state               = dynatafo_get_review_notice_state( $user_id );
        $state['impressions'] = (int) $state['impressions'] + 1;
        dynatafo_save_review_notice_state( $user_id, $state );

        $review_url   = 'https://wordpress.org/support/plugin/dynamic-tags-for-elementor/reviews/#new-post';
        $impressions  = (int) $state['impressions'];

        // Build nonces.
        $snooze_nonce  = wp_create_nonce( 'dynatafo_review_snooze' );
        $reviewed_nonce = wp_create_nonce( 'dynatafo_review_done' );
        $ajax_url      = admin_url( 'admin-ajax.php' );

        // Server-side snooze fallback URL.
        $snooze_url = wp_nonce_url(
            $ajax_url . '?action=dynatafo_review_snooze',
            'dynatafo_review_snooze'
        );
        ?>
        <div id="dynatafo-review-notice"
             class="notice dynatafo-review-notice"
             role="alert"
             data-impressions="<?php echo esc_attr( $impressions ); ?>"
             style="border: none; padding: 0; background: transparent; box-shadow: none; margin: 5px 0 15px;">

            <div style="
                display: flex;
                align-items: stretch;
                background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 24px rgba(99,102,241,0.25), 0 1px 4px rgba(0,0,0,0.15);
                max-width: 780px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            ">
                <!-- Accent bar -->
                <div style="width: 5px; flex-shrink: 0; background: linear-gradient(180deg, #a78bfa, #7c3aed, #6d28d9);"></div>

                <!-- Star column -->
                <div style="
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px 18px;
                    background: rgba(255,255,255,0.05);
                    flex-shrink: 0;
                ">
                    <span style="font-size: 34px; line-height: 1; filter: drop-shadow(0 2px 6px rgba(250,204,21,0.6));" aria-hidden="true">⭐</span>
                </div>

                <!-- Main content -->
                <div style="padding: 18px 16px 18px 4px; flex: 1; min-width: 0;">
                    <p style="margin: 0 0 4px; font-size: 15px; font-weight: 700; color: #f5f3ff; line-height: 1.3;">
                        <?php esc_html_e( 'Loving Dynamic Tags for Elementor?', 'dynamic-tags-for-elementor' ); ?>
                    </p>
                    <p style="margin: 0 0 14px; font-size: 13px; color: #c4b5fd; line-height: 1.55;">
                        <?php esc_html_e( 'You\'ve been using the plugin for a while — a quick review on WordPress.org takes less than a minute and helps thousands of other designers discover it. It means the world to us! 🙏', 'dynamic-tags-for-elementor' ); ?>
                    </p>

                    <!-- Action row -->
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">

                        <!-- Primary CTA: Leave a Review -->
                        <a id="dynatafo-review-btn"
                           href="<?php echo esc_url( $review_url ); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           data-nonce="<?php echo esc_attr( $reviewed_nonce ); ?>"
                           data-ajax="<?php echo esc_url( $ajax_url ); ?>"
                           style="
                               display: inline-flex;
                               align-items: center;
                               gap: 7px;
                               padding: 9px 20px;
                               background: linear-gradient(135deg, #f59e0b, #d97706);
                               color: #fff;
                               font-size: 13px;
                               font-weight: 700;
                               text-decoration: none;
                               border-radius: 6px;
                               box-shadow: 0 2px 8px rgba(245,158,11,0.4);
                               transition: filter .2s;
                               letter-spacing: .3px;
                           "
                           onmouseover="this.style.filter='brightness(1.12)'"
                           onmouseout="this.style.filter='brightness(1)'">
                            <span aria-hidden="true" style="font-size:15px;">🌟</span>
                            <?php esc_html_e( 'Leave a Review', 'dynamic-tags-for-elementor' ); ?>
                        </a>

                        <!-- Secondary: Maybe Later (snooze) -->
                        <button id="dynatafo-review-snooze"
                                type="button"
                                data-nonce="<?php echo esc_attr( $snooze_nonce ); ?>"
                                data-ajax="<?php echo esc_url( $ajax_url ); ?>"
                                style="
                                    background: rgba(255,255,255,0.08);
                                    border: 1px solid rgba(255,255,255,0.18);
                                    color: #c4b5fd;
                                    font-size: 12px;
                                    padding: 8px 16px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    transition: background .2s;
                                    font-family: inherit;
                                "
                                onmouseover="this.style.background='rgba(255,255,255,0.14)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                            <?php
                            printf(
                                /* translators: %d: number of days */
                                esc_html__( 'Maybe Later (remind me in %d days)', 'dynamic-tags-for-elementor' ),
                                absint( DYNATAFO_REVIEW_SNOOZE_DAYS )
                            );
                            ?>
                        </button>
                    </div>
                </div><!-- /.main content -->

                <!-- Dismiss (×) -->
                <div style="padding: 14px 14px 0 4px; flex-shrink: 0;">
                    <button id="dynatafo-review-close"
                            type="button"
                            data-nonce="<?php echo esc_attr( $snooze_nonce ); ?>"
                            data-ajax="<?php echo esc_url( $ajax_url ); ?>"
                            aria-label="<?php esc_attr_e( 'Dismiss notice', 'dynamic-tags-for-elementor' ); ?>"
                            style="
                                background: none;
                                border: none;
                                color: rgba(196,181,253,0.6);
                                font-size: 20px;
                                cursor: pointer;
                                line-height: 1;
                                padding: 2px 4px;
                                border-radius: 4px;
                                transition: color .2s;
                                font-family: inherit;
                            "
                            onmouseover="this.style.color='#f5f3ff'"
                            onmouseout="this.style.color='rgba(196,181,253,0.6)'">
                        &times;
                    </button>
                </div>
            </div><!-- /.card -->
        </div>
        <?php
    },
    30 // Priority 30 — renders after What's New (priority 5 via admin_notices hook in manager).
);

/**
 * ─── Scripts ─────────────────────────────────────────────────────────────────
 */
add_action(
    'admin_enqueue_scripts',
    function () {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $user_id = get_current_user_id();
        if ( ! $user_id || ! dynatafo_should_show_review_notice( $user_id ) ) {
            return;
        }

        $script = <<<'JS'
(function(){
    // ── Snooze (dismiss / maybe later / × close) ──────────────────────────
    function dynatafoSnoozeReview(btn) {
        var ajaxUrl = btn.dataset.ajax;
        var nonce   = btn.dataset.nonce;
        if (!ajaxUrl || !nonce) return;

        var notice = document.getElementById('dynatafo-review-notice');
        if (notice) {
            notice.style.transition = 'opacity .35s';
            notice.style.opacity    = '0';
            setTimeout(function(){ notice.style.display = 'none'; }, 380);
        }

        fetch(ajaxUrl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    'action=dynatafo_review_snooze&_ajax_nonce=' + encodeURIComponent(nonce)
        });
    }

    // ── Mark as reviewed (primary CTA click) ──────────────────────────────
    function dynatafoMarkReviewed(btn) {
        var ajaxUrl = btn.dataset.ajax;
        var nonce   = btn.dataset.nonce;
        if (!ajaxUrl || !nonce) return;

        fetch(ajaxUrl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    'action=dynatafo_review_done&_ajax_nonce=' + encodeURIComponent(nonce)
        }).then(function(){
            var notice = document.getElementById('dynatafo-review-notice');
            if (notice) {
                notice.style.transition = 'opacity .35s';
                notice.style.opacity    = '0';
                setTimeout(function(){ notice.style.display = 'none'; }, 380);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        // Snooze button
        var snoozeBtn = document.getElementById('dynatafo-review-snooze');
        if (snoozeBtn) {
            snoozeBtn.addEventListener('click', function(){ dynatafoSnoozeReview(this); });
        }

        // × close button
        var closeBtn = document.getElementById('dynatafo-review-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(){ dynatafoSnoozeReview(this); });
        }

        // Review CTA — still open the link but also call the handler.
        var reviewBtn = document.getElementById('dynatafo-review-btn');
        if (reviewBtn) {
            reviewBtn.addEventListener('click', function(){ dynatafoMarkReviewed(this); });
        }
    });
})();
JS;

        wp_register_script( 'dynatafo-review-notice', false, [], DYNATAFO_VERSION, true );
        wp_enqueue_script( 'dynatafo-review-notice' );
        wp_add_inline_script( 'dynatafo-review-notice', $script );
    }
);

/**
 * ─── AJAX: Snooze ────────────────────────────────────────────────────────────
 */
add_action(
    'wp_ajax_dynatafo_review_snooze',
    function () {
        check_ajax_referer( 'dynatafo_review_snooze' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Forbidden' ], 403 );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( [ 'message' => 'Invalid user' ], 400 );
        }

        $state               = dynatafo_get_review_notice_state( $user_id );
        $state['snoozed_at'] = time();
        dynatafo_save_review_notice_state( $user_id, $state );

        wp_send_json_success( [ 'snoozed_at' => $state['snoozed_at'] ] );
    }
);

/**
 * ─── AJAX: Mark as reviewed (permanently hide) ───────────────────────────────
 */
add_action(
    'wp_ajax_dynatafo_review_done',
    function () {
        check_ajax_referer( 'dynatafo_review_done' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Forbidden' ], 403 );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( [ 'message' => 'Invalid user' ], 400 );
        }

        $state             = dynatafo_get_review_notice_state( $user_id );
        $state['reviewed'] = true;
        dynatafo_save_review_notice_state( $user_id, $state );

        wp_send_json_success();
    }
);

// This file does NOT return a notice definition consumed by the notice manager.
// All rendering is done directly via action hooks above.
return [];
