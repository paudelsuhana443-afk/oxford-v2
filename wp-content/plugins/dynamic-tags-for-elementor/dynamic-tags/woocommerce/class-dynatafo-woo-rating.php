<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Woo_Rating_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-woo-rating'; }
    public function get_title() { return esc_html__( 'Product Rating (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-woo-group'; }
    public function get_categories() { return [ 'text', 'number' ]; }

    public function render() {
        if ( ! function_exists( 'wc_get_product' ) ) {
            return;
        }

        $product = dynatafo_get_cached_wc_product();

        if ( ! $product ) {
            return;
        }

        $rating = $product->get_average_rating();

        if ( '' === $rating || null === $rating ) {
            echo '0';
            return;
        }

        echo esc_html( (string) $rating );
    }
}