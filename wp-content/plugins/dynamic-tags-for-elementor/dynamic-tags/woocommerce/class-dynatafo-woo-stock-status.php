<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Woo_Stock_Status_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-woo-stock-status'; }
    public function get_title() { return esc_html__( 'Product Stock Status (DTE)', 'dynamic-tags-for-elementor' ); }
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

        $status_text = $product->is_in_stock()
            ? esc_html__( 'In stock', 'dynamic-tags-for-elementor' )
            : esc_html__( 'Out of stock', 'dynamic-tags-for-elementor' );

        if ( $product->managing_stock() ) {
            $qty = $product->get_stock_quantity();

            if ( null !== $qty ) {
                $status_text = sprintf( '%s (%d)', $status_text, (int) $qty );
            }
        }

        echo esc_html( $status_text );
    }
}