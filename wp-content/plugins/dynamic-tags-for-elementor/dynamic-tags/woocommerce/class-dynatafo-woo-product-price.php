<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Woo_Product_Price_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-woo-product-price'; }
    public function get_title() { return esc_html__( 'Product Price (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-woo-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        if ( ! function_exists( 'wc_get_product' ) ) {
            return;
        }

        $product = dynatafo_get_cached_wc_product();

        if ( ! $product ) {
            return;
        }

        $price = $product->get_price();

        if ( '' === $price || null === $price ) {
            return;
        }

        if ( function_exists( 'wc_price' ) ) {
            echo esc_html( wp_strip_all_tags( wc_price( (float) $price ) ) );
            return;
        }

        echo esc_html( (string) $price );
    }
}