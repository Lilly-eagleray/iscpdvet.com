<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $course;
if ( $cross_sells ) : ?>
	<?php
	$current_user = wp_get_current_user();
	$days = $course->isUserSubscripationValid($current_user->ID,746);
	if($days > 335 || 0 == $current_user->ID  ){
	?>
	
	<div class="club tipso" data-tipso='מועדון CPD-Vet נותן 10% הנחה על קנייה באתר, וכמוכן מאפשר צפייה בוובינרים ובתכנים נוספים'>
	<ul class="list-group">
		<li class="list-group-item">
		<?php
		$club_post = get_post(746);
		$_product_club = wc_get_product( 746 );	
		?>
		<div class="cross-sell-image">
		<?php echo get_the_post_thumbnail( $club_post->ID, array( 50, 50) ); ?>
		</div>
		<div class="cross-sell-header">
			<?php echo $club_post->post_title; ?>
			<br>
			<?php echo $_product_club->get_price_html(); ?>
		</div>
		<div class="cross-sell-text">
		<?php if(get_field("cart_massage",$club_post->ID)){ ?>
			<i class="fa fa-info-circle"></i> <?php echo get_field("cart_massage",$club_post->ID); ?>
		<?php } ?>
		</div>
		<div class="cross-sell-price">
		<a class="button" href="<?php echo do_shortcode("[add_to_cart_url id='" . $_product_club->get_id() . "']") ?>">
			<?php _e("Add to cart","woocommerce"); ?>
		</a>
		</div>
		<div style="clear:both"></div>
		</li>
	</ul>	
	</div>
	<?php
	}
	?>
	<div class="cross-sells">

		<h2><?php _e( 'You may be interested in&hellip;', 'woocommerce' ) ?></h2>
		
		<?php woocommerce_product_loop_start(); ?>
		<ul class="list-group">
			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
				 	$post_object = get_post( $cross_sell->get_id() );
					$_product = wc_get_product( $cross_sell->get_id() );

					
					?>
					
					<li class="list-group-item">
					
						<div class="cross-sell-image">
						<?php echo get_the_post_thumbnail( $post_object->ID, array( 50, 50) ); ?>
						</div>
						<div class="cross-sell-header">
							<?php echo $post_object->post_title; ?>
							<br>
							<?php echo $_product->get_price_html(); ?>
						</div>
						<div class="cross-sell-price">
						<a class="button" href="<?php echo do_shortcode("[add_to_cart_url id='" . $cross_sell->get_id() . "']") ?>">
							<?php _e("Add to cart","woocommerce"); ?>
						</a>
						</div>
					<div style="clear:both"></div>
					</li>
					
			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>
		</ul>
	</div>

<?php endif;
