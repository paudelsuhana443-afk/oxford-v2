<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Woo_SKU_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-woo-sku'; }
    public function get_title() { return esc_html__( 'Product SKU (DTE)', 'dynamic-tags-for-elementor' ); }
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

        $sku = $product->get_sku();

        if ( empty( $sku ) ) {
            return;
        }

        echo esc_html( $sku );
    }
}