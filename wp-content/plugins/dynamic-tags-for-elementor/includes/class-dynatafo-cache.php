<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dedicated cache helper class for request-scoped runtime and persistent caching.
 */
class DYNATAFO_Cache {
    /**
     * Request-scoped runtime cache storage.
     *
     * @var array<string, array<string, mixed>>
     */
    private static $runtime_cache = [];

    /**
     * Cache version string.
     *
     * @var string
     */
    private static $cache_version = '';

    /**
     * Get a reference to the entire runtime cache store.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function &get_store() {
        return self::$runtime_cache;
    }

    /**
     * Get a value from the runtime cache.
     *
     * @param string $key
     * @param string $group
     * @return mixed|null
     */
    public static function get( $key, $group = 'default' ) {
        if ( isset( self::$runtime_cache[ $group ] ) && array_key_exists( $key, self::$runtime_cache[ $group ] ) ) {
            return self::$runtime_cache[ $group ][ $key ];
        }

        return null;
    }

    /**
     * Store a value in the runtime cache.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $group
     * @return mixed
     */
    public static function set( $key, $value, $group = 'default' ) {
        if ( ! isset( self::$runtime_cache[ $group ] ) || ! is_array( self::$runtime_cache[ $group ] ) ) {
            self::$runtime_cache[ $group ] = [];
        }

        self::$runtime_cache[ $group ][ $key ] = $value;

        return $value;
    }

    /**
     * Remember a value in runtime cache for the current request.
     *
     * @param string   $key
     * @param callable $callback
     * @param string   $group
     * @return mixed
     */
    public static function remember( $key, callable $callback, $group = 'default' ) {
        $cached = self::get( $key, $group );

        if ( null !== $cached ) {
            return $cached;
        }

        return self::set( $key, $callback(), $group );
    }

    /**
     * Delete a value from the runtime cache.
     *
     * @param string $key
     * @param string $group
     * @return bool
     */
    public static function delete( $key, $group = 'default' ) {
        if ( isset( self::$runtime_cache[ $group ] ) && array_key_exists( $key, self::$runtime_cache[ $group ] ) ) {
            unset( self::$runtime_cache[ $group ][ $key ] );
            return true;
        }

        return false;
    }

    /**
     * Clear the runtime cache. If group is provided, only clears that group.
     *
     * @param string|null $group
     */
    public static function clear( $group = null ) {
        if ( null !== $group ) {
            if ( isset( self::$runtime_cache[ $group ] ) ) {
                self::$runtime_cache[ $group ] = [];
            }
        } else {
            self::$runtime_cache = [];
        }
    }

    /**
     * Check file existence once per request.
     *
     * @param string $file_path
     * @return bool
     */
    public static function file_exists( $file_path ) {
        if ( empty( $file_path ) || ! is_string( $file_path ) ) {
            return false;
        }

        return (bool) self::remember(
            $file_path,
            static function () use ( $file_path ) {
                return file_exists( $file_path );
            },
            'dynatafo_file_exists'
        );
    }

    /**
     * Check if a file exists and is readable once per request.
     *
     * @param string $file_path
     * @return bool
     */
    public static function is_readable_file( $file_path ) {
        if ( empty( $file_path ) || ! is_string( $file_path ) ) {
            return false;
        }

        return (bool) self::remember(
            $file_path,
            static function () use ( $file_path ) {
                return file_exists( $file_path ) && is_readable( $file_path );
            },
            'dynatafo_file_readable'
        );
    }

    /**
     * Get the global cache version option from the database.
     *
     * @return string
     */
    public static function get_version() {
        if ( empty( self::$cache_version ) ) {
            $version = get_option( 'dynatafo_cache_version' );
            if ( ! $version ) {
                $version = '1';
                update_option( 'dynatafo_cache_version', $version );
            }
            self::$cache_version = (string) $version;
        }

        return self::$cache_version;
    }

    /**
     * Increment the global cache version.
     *
     * @return int New cache version number.
     */
    public static function bump_version() {
        $current = (int) self::get_version();
        $next = $current + 1;
        update_option( 'dynatafo_cache_version', (string) $next );
        self::$cache_version = (string) $next;
        return $next;
    }

    /**
     * Format transient key by prefixing with the cache version.
     *
     * @param string $key Original transient key.
     * @return string Versioned key.
     */
    public static function format_key( $key ) {
        $version = self::get_version();
        return 'dtf_v' . $version . '_' . $key;
    }

    /**
     * Get transient value wrapper.
     *
     * @param string $key
     * @return mixed
     */
    public static function get_transient( $key ) {
        return get_transient( self::format_key( $key ) );
    }

    /**
     * Set transient value wrapper.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expiration
     * @return bool
     */
    public static function set_transient( $key, $value, $expiration = 0 ) {
        return set_transient( self::format_key( $key ), $value, $expiration );
    }

    /**
     * Delete transient wrapper.
     *
     * @param string $key
     * @return bool
     */
    public static function delete_transient( $key ) {
        return delete_transient( self::format_key( $key ) );
    }

    /**
     * Set up cache invalidation hooks.
     */
    public static function init_hooks() {
        add_action( 'save_post', [ __CLASS__, 'invalidate_post_caches' ] );
        add_action( 'deleted_post', [ __CLASS__, 'invalidate_post_caches' ] );
        add_action( 'edited_term', [ __CLASS__, 'invalidate_term_caches' ] );
        add_action( 'delete_term', [ __CLASS__, 'invalidate_term_caches' ] );
        add_action( 'create_term', [ __CLASS__, 'invalidate_term_caches' ] );
    }

    /**
     * Invalidate caches related to post updates.
     *
     * @param int $post_id Post ID.
     */
    public static function invalidate_post_caches( $post_id ) {
        // Prevent looping during autosaves/revisions
        if ( ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $post_id ) ) ||
             ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) ) {
            return;
        }

        // Invalidate widget content post list cache
        wp_cache_delete( 'dynatafo_widget_content_post_list', 'dynatafo' );

        // Clear runtime cache completely
        self::clear();
    }

    /**
     * Invalidate caches related to term updates.
     *
     * @param int $term_id Term ID.
     */
    public static function invalidate_term_caches( $term_id ) {
        // Clear runtime cache completely
        self::clear();
    }
}
