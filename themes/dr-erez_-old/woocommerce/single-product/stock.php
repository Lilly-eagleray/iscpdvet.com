<?php
/**
 * Single Product stock.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	 https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// CUSTOMIZATION: Check product meta to determine if stock availability should be hidden.
// Displays stock unless '_hide_stock' meta is set and not equal to '0'.
$hide_stock_meta = get_post_meta( $product->get_id(), '_hide_stock', true );

if ( ! $hide_stock_meta || '0' === $hide_stock_meta ) {
	?>
	<p class="stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ); ?></p>
	<?php
}
?>
