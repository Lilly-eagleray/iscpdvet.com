<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>


<?php
if ( function_exists('icl_object_id') ) {
	
	 get_post_meta($post->ID,"dollar_price",true);
		if(ICL_LANGUAGE_CODE == "en"){	
		if(get_post_meta($post->ID,"dollar_price",true)){
		?>
				<p class="price"><?=get_post_meta($post->ID,"dollar_price",true)?>&#x24; (includes VAT)</p>
		<?php
		}
		if(get_post_meta($post->ID,"euro_price",true)){
		?>
		<p class="price"><?=get_post_meta($post->ID,"euro_price",true)?>&#x20AC; (includes VAT)</p>
		
		<?php
		}
	}
	else{
	?>
	<p class="price"><?php echo $product->get_price_html(); ?></p>
	<?php
	}
}
else{
	?>
	<p class="price"><?php echo $product->get_price_html(); ?></p>
	<?php
}
 ?>