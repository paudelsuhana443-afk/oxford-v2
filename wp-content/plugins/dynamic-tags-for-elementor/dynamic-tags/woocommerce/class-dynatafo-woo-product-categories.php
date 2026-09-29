<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Woo_Product_Categories_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() { return 'dynatafo-woo-product-categories'; }
    public function get_title() { return esc_html__( 'Product Categories (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-woo-group'; }
    public function get_categories() { return [ 'text' ]; }

    public function render() {
        $post_id = get_the_ID();

        if ( empty( $post_id ) ) {
            return;
        }

        $terms = wp_get_post_terms( $post_id, 'product_cat', [ 'fields' => 'names' ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return;
        }

        echo esc_html( implode( ', ', $terms ) );
    }
}