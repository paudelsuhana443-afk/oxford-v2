<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add settings link to plugins page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dynatafo_add_settings_link' );
function dynatafo_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=dynatafo-info">' . __( 'Info', 'dynamic-tags-for-elementor' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

/**
 * Safe escaping for dynamic tag outputs
 * Use this in your tag render() methods for extra security
 */
function dynatafo_safe_render( $content ) {
    echo wp_kses_post( $content );
}

/**
 * Request-scoped runtime cache storage.
 *
 * @return array<string, array<string, mixed>>
 */
function &dynatafo_runtime_cache_store() {
    return DYNATAFO_Cache::get_store();
}

/**
 * Get a value from the runtime cache.
 *
 * @param string $key
 * @param string $group
 * @return mixed|null
 */
function dynatafo_runtime_cache_get( $key, $group = 'default' ) {
    return DYNATAFO_Cache::get( $key, $group );
}

/**
 * Store a value in the runtime cache.
 *
 * @param string $key
 * @param mixed  $value
 * @param string $group
 * @return mixed
 */
function dynatafo_runtime_cache_set( $key, $value, $group = 'default' ) {
    return DYNATAFO_Cache::set( $key, $value, $group );
}

/**
 * Remember a value in runtime cache for the current request.
 *
 * @param string   $key
 * @param callable $callback
 * @param string   $group
 * @return mixed
 */
function dynatafo_runtime_cache_remember( $key, callable $callback, $group = 'default' ) {
    return DYNATAFO_Cache::remember( $key, $callback, $group );
}

/**
 * Get a cached WooCommerce product for the current request.
 *
 * @param mixed $post Optional post object or ID.
 * @return \WC_Product|null
 */
function dynatafo_get_cached_wc_product( $post = null ) {
    if ( ! function_exists( 'wc_get_product' ) ) {
        return null;
    }

    if ( null === $post ) {
        $post = get_post();
    }

    $post_obj = get_post( $post );

    if ( ! $post_obj || empty( $post_obj->ID ) ) {
        return null;
    }

    $cache_key = (string) (int) $post_obj->ID;

    return dynatafo_runtime_cache_remember(
        $cache_key,
        static function () use ( $post_obj ) {
            $product = wc_get_product( $post_obj );

            return $product instanceof \WC_Product ? $product : null;
        },
        'dynatafo_wc_product'
    );
}

/**
 * Get a cached ACF/SCF field value for the current request.
 *
 * @param string     $field_name
 * @param mixed      $post_id
 * @param bool       $format_value
 * @param bool       $escape_html
 * @return mixed
 */
function dynatafo_get_cached_field( $field_name, $post_id = false, $format_value = true, $escape_html = false ) {
    if ( ! function_exists( 'get_field' ) ) {
        return null;
    }

    $effective_post_id = $post_id;
    if ( false === $effective_post_id || null === $effective_post_id || '' === $effective_post_id ) {
        $effective_post_id = get_the_ID() ?: 'current';
    }

    $cache_key = md5(
        wp_json_encode(
            [
                'field_name'   => (string) $field_name,
                'post_id'      => $effective_post_id,
                'format_value' => (bool) $format_value,
                'escape_html'  => (bool) $escape_html,
            ]
        )
    );

    return dynatafo_runtime_cache_remember(
        $cache_key,
        static function () use ( $field_name, $post_id, $format_value, $escape_html ) {
            return get_field( $field_name, $post_id, $format_value, $escape_html );
        },
        'dynatafo_field'
    );
}

/**
 * Check if a file exists once per request.
 *
 * @param string $file_path
 * @return bool
 */
function dynatafo_file_exists( $file_path ) {
    return DYNATAFO_Cache::file_exists( $file_path );
}

/**
 * Check if a file exists and is readable once per request.
 *
 * @param string $file_path
 * @return bool
 */
function dynatafo_is_readable_file( $file_path ) {
    return DYNATAFO_Cache::is_readable_file( $file_path );
}

/**
 * Read a file once per request.
 *
 * @param string $file_path
 * @return string|false
 */
function dynatafo_get_cached_file_contents( $file_path ) {
    return dynatafo_runtime_cache_remember(
        (string) $file_path,
        static function () use ( $file_path ) {
            if ( ! dynatafo_is_readable_file( $file_path ) ) {
                return false;
            }

            return file_get_contents( $file_path );
        },
        'dynatafo_file_contents'
    );
}

/**
 * Glob PHP files once per request.
 *
 * @param string $pattern
 * @return array<int, string>
 */
function dynatafo_get_cached_glob( $pattern ) {
    $result = dynatafo_runtime_cache_remember(
        (string) $pattern,
        static function () use ( $pattern ) {
            $files = glob( $pattern );

            return is_array( $files ) ? $files : [];
        },
        'dynatafo_glob'
    );

    return is_array( $result ) ? $result : [];
}