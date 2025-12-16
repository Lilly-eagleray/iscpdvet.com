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
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

// CUSTOMIZATION: Global variable required for custom subscription check
global $course;

if ( $cross_sells ) :

	// CUSTOMIZATION: Start of custom CPD-Vet Club promotion logic (from old template 3.0.0)
	$current_user = wp_get_current_user();
	$club_product_id = 746;
	$days = 0; // Default days if $course is not set

	if (isset($course) && method_exists($course, 'isUserSubscripationValid')) {
		$days = $course->isUserSubscripationValid($current_user->ID, $club_product_id);
	}

	if ( $days > 335 || 0 == $current_user->ID ) {
		$club_post = get_post( $club_product_id );
		$_product_club = wc_get_product( $club_product_id );
		if ( $club_post && $_product_club ) :
	?>
		<div class="club tipso" data-tipso='מועדון CPD-Vet נותן 10% הנחה על קנייה באתר, וכמוכן מאפשר צפייה בוובינרים ובתכנים נוספים'>
			<ul class="list-group">
				<li class="list-group-item">
					<div class="cross-sell-image">
						<?php echo get_the_post_thumbnail( $club_post->ID, array( 50, 50) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="cross-sell-header">
						<?php echo esc_html( $club_post->post_title ); ?>
						<br>
						<?php echo $_product_club->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="cross-sell-text">
					<?php if( function_exists('get_field') && get_field("cart_massage", $club_post->ID) ){ ?>
						<i class="fa fa-info-circle"></i> <?php echo esc_html( get_field("cart_massage", $club_post->ID) ); ?>
					<?php } ?>
					</div>
					<div class="cross-sell-price">
						<a class="button" href="<?php echo esc_url( do_shortcode( "[add_to_cart_url id='" . $club_product_id . "']" ) ); ?>">
							<?php esc_html_e( "Add to cart", "woocommerce" ); ?>
						</a>
					</div>
					<div style="clear:both"></div>
				</li>
			</ul>
		</div>
	<?php
		endif;
	}
	// CUSTOMIZATION: End of custom CPD-Vet Club promotion logic
	?>

	<div class="cross-sells">
		<?php
		// Use modern heading structure which supports filtering
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

		<ul class="list-group">
			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
					// CUSTOMIZATION: Using custom markup instead of wc_get_template_part( 'content', 'product' )
				 	$post_object = get_post( $cross_sell->get_id() );
					$_product = wc_get_product( $cross_sell->get_id() );
				?>

				<li class="list-group-item">

					<div class="cross-sell-image">
						<?php echo get_the_post_thumbnail( $post_object->ID, array( 50, 50) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="cross-sell-header">
						<?php echo esc_html( $post_object->post_title ); ?>
						<br>
						<?php echo $_product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="cross-sell-price">
						<a class="button" href="<?php echo esc_url( do_shortcode( "[add_to_cart_url id='" . $cross_sell->get_id() . "']" ) ); ?>">
							<?php esc_html_e( "Add to cart", "woocommerce" ); ?>
						</a>
					</div>
					<div style="clear:both"></div>
				</li>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>
		</ul>

	</div>
	<?php
endif;

wp_reset_postdata();
?>
