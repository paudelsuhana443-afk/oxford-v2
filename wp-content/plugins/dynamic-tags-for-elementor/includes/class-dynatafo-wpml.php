<?php
/**
 * WPML Integration Helper for Dynamic Tags for Elementor
 * 
 * Provides utilities for WPML/Polylang multilingual support.
 * Ensures all dynamic tag URLs and content are language-aware.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_WPML {

    /**
     * Check if WPML is active.
     */
    public static function is_wpml_active() {
        return defined( 'ICL_SITEPRESS_VERSION' );
    }

    /**
     * Check if Polylang is active.
     */
    public static function is_polylang_active() {
        return function_exists( 'pll_current_language' );
    }

    /**
     * Check if any multilingual plugin is active.
     */
    public static function is_multilingual_active() {
        return self::is_wpml_active() || self::is_polylang_active();
    }

    /**
     * Get current language code.
     * Priority: WPML > Polylang > WordPress locale
     */
    public static function get_current_language() {
        if ( self::is_wpml_active() && defined( 'ICL_LANGUAGE_CODE' ) ) {
            /* translators: Language code from WPML */
            return ICL_LANGUAGE_CODE;
        }

        if ( self::is_polylang_active() ) {
            /* translators: Language code from Polylang */
            return pll_current_language();
        }

        // Fallback to WordPress locale
        return substr( get_locale(), 0, 2 );
    }

    /**
     * Apply WPML language filter to home URL.
     * Uses WPML's wpml_home_url filter which handles language prefixes/domains.
     */
    public static function apply_language_to_home_url( $url = '' ) {
        if ( empty( $url ) ) {
            $url = home_url( '/' );
        }

        if ( self::is_wpml_active() ) {
            /* translators: Home URL being filtered by WPML for language-aware paths */
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $url = apply_filters( 'wpml_home_url', $url );
        }

        return $url;
    }

    /**
     * Apply WPML language filter to permalink.
     * Uses WPML's wpml_permalink filter which handles translated slugs.
     */
    public static function apply_language_to_permalink( $url = '', $post_id = 0 ) {
        if ( empty( $url ) ) {
            $post_id = $post_id ? (int) $post_id : get_the_ID();
            $url = get_permalink( $post_id );
        }

        if ( self::is_wpml_active() ) {
            /* translators: Post URL being filtered by WPML for translated slugs and language prefixes */
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $url = apply_filters( 'wpml_permalink', $url, self::get_current_language() );
        }

        return $url;
    }

    /**
     * Apply WPML language filter to term link.
     * Ensures taxonomy term URLs are language-aware.
     */
    public static function apply_language_to_term_link( $url = '', $term_id = 0, $taxonomy = '' ) {
        if ( empty( $url ) && 0 !== $term_id ) {
            $url = get_term_link( (int) $term_id, $taxonomy );
            if ( is_wp_error( $url ) ) {
                return '';
            }
        }

        if ( self::is_wpml_active() && is_string( $url ) ) {
            /* translators: Term URL being filtered by WPML for language-aware paths */
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $url = apply_filters( 'wpml_permalink', $url, self::get_current_language() );
        }

        return $url;
    }

    /**
     * Apply WPML language filter to author archive URL.
     * Ensures author URLs are language-aware.
     */
    public static function apply_language_to_author_url( $url = '', $author_id = 0 ) {
        if ( empty( $url ) && 0 !== $author_id ) {
            $url = get_author_posts_url( (int) $author_id );
        }

        if ( self::is_wpml_active() && is_string( $url ) ) {
            /* translators: Author archive URL being filtered by WPML for language-aware paths */
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $url = apply_filters( 'wpml_permalink', $url, self::get_current_language() );
        }

        return $url;
    }

    /**
     * Apply WPML language filter to post archive URL.
     * Ensures post type archive URLs are language-aware.
     */
    public static function apply_language_to_archive_url( $url = '' ) {
        if ( self::is_wpml_active() && is_string( $url ) && '' !== $url ) {
            /* translators: Archive URL being filtered by WPML for language-aware paths */
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $url = apply_filters( 'wpml_permalink', $url, self::get_current_language() );
        }

        return $url;
    }

    /**
     * Get translated post/post type ID.
     * Useful for getting the current language version of a post.
     * Requires WPML's 'wpml_object_id' filter or 'icl_object_id' function.
     */
    public static function get_translated_post_id( $post_id, $post_type = 'post', $language_code = '' ) {
        if ( ! self::is_wpml_active() ) {
            return (int) $post_id;
        }

        $language_code = $language_code ?: self::get_current_language();

        // Use WPML filter if available
        if ( function_exists( 'icl_object_id' ) ) {
            /* translators: Post ID being translated for specific language via WPML */
            return icl_object_id( (int) $post_id, $post_type, true, $language_code );
        }

        return (int) $post_id;
    }

    /**
     * Get translated term ID.
     * Useful for getting the current language version of a taxonomy term.
     */
    public static function get_translated_term_id( $term_id, $taxonomy = '', $language_code = '' ) {
        if ( ! self::is_wpml_active() ) {
            return (int) $term_id;
        }

        $language_code = $language_code ?: self::get_current_language();

        // Use WPML function if available
        if ( function_exists( 'icl_object_id' ) ) {
            /* translators: Term ID being translated for specific language via WPML */
            return icl_object_id( (int) $term_id, 'tax_' . $taxonomy, true, $language_code );
        }

        return (int) $term_id;
    }

    /**
     * Get available languages.
     * Returns array of language codes or empty array if no multilingual plugin active.
     */
    public static function get_available_languages() {
        if ( self::is_wpml_active() && function_exists( 'icl_get_languages' ) ) {
            $languages = icl_get_languages( [ 'skip_missing' => 0 ] );
            /* translators: List of available languages from WPML */
            return array_keys( $languages );
        }

        if ( self::is_polylang_active() && function_exists( 'pll_languages_list' ) ) {
            /* translators: List of available languages from Polylang */
            return pll_languages_list();
        }

        return [];
    }

    /**
     * Translate a string for WPML.
     * Useful for translating dynamic tag outputs.
     */
    public static function translate_string( $string, $context = 'dynamic-tags-for-elementor' ) {
        if ( self::is_wpml_active() && function_exists( 'apply_filters' ) ) {
            /* translators: String being translated via WPML */
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            return apply_filters( 'wpml_translate_single_string', $string, $context, $string );
        }

        return $string;
    }

    /**
     * Get language switcher options for Elementor controls.
     * Useful for language selection dropdowns in tag settings.
     */
    public static function get_language_options() {
        $options = [];

        if ( self::is_wpml_active() && function_exists( 'icl_get_languages' ) ) {
            $languages = icl_get_languages( [ 'skip_missing' => 0 ] );
            /* translators: Languages retrieved from WPML for selector */
            foreach ( $languages as $code => $language ) {
                $options[ $code ] = isset( $language['native_name'] ) && $language['native_name']
                    ? $language['native_name']
                    : $language['language_code'];
            }
            return $options;
        }

        if ( self::is_polylang_active() && function_exists( 'pll_languages_list' ) ) {
            $languages = pll_languages_list( [ 'fields' => 'id=>name' ] );
            /* translators: Languages retrieved from Polylang for selector */
            foreach ( $languages as $code => $name ) {
                $options[ $code ] = $name;
            }
            return $options;
        }

        return $options;
    }

    /**
     * Check if current post/page is translated.
     * Returns true if post has translations in other languages.
     */
    public static function is_post_translated( $post_id ) {
        if ( ! self::is_wpml_active() ) {
            return false;
        }

        if ( function_exists( 'wpml_get_translatable_objects_registered' ) ) {
            $post = get_post( $post_id );
            if ( ! $post ) {
                return false;
            }

            global $sitepress;
            if ( isset( $sitepress ) ) {
                $trid = $sitepress->get_element_trid( (int) $post_id, 'post_' . $post->post_type );
                if ( $trid ) {
                    $translations = $sitepress->get_element_translations( $trid, 'post_' . $post->post_type );
                    /* translators: Translation status being checked for post via WPML */
                    return count( $translations ) > 1;
                }
            }
        }

        return false;
    }
}
