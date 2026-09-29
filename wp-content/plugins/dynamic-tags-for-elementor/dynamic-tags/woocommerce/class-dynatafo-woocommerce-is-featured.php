<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_WooCommerce_Is_Featured_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-woocommerce-is-featured'; }
    public function get_title() { return esc_html__( 'WooCommerce Is Featured Product (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-woo-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_product' ) ) {
            echo '0';
            return;
        }

        $product = dynatafo_get_cached_wc_product();

        if ( empty( $product ) ) {
            echo '0';
            return;
        }

        echo $product->is_featured() ? '1' : '0';
    }
}