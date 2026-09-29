<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Woo_Add_To_Cart_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-woo-add-to-cart-url'; }
    public function get_title() { return esc_html__( 'Product Add To Cart URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-woo-group'; }
    public function get_categories() { return [ 'url' ]; }

    public function get_value( array $options = [] ) {
        if ( ! function_exists( 'wc_get_product' ) ) {
            return '';
        }

        $product = dynatafo_get_cached_wc_product();

        if ( ! $product || ! method_exists( $product, 'add_to_cart_url' ) ) {
            return '';
        }

        return esc_url_raw( $product->add_to_cart_url() );
    }
}