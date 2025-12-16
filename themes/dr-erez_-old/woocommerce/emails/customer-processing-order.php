<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 9.9.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>
<p>
<?php
if ( ! empty( $order->get_billing_first_name() ) ) {
	/* translators: %s: Customer first name */
	printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) );
} else {
	printf( esc_html__( 'Hi,', 'woocommerce' ) );
}
?>
</p>
<?php if ( $email_improvements_enabled ) : ?>
	<p><?php esc_html_e( 'Just to let you know &mdash; we’ve received your order, and it is now being processed.', 'woocommerce' ); ?></p>
	<p><?php esc_html_e( 'Here’s a reminder of what you’ve ordered:', 'woocommerce' ); ?></p>
<?php else : ?>
	<?php /* translators: %s: Order number */ ?>
	<p><?php printf( esc_html__( 'Just to let you know &mdash; we\'ve received your order #%s, and it is now being processed:', 'woocommerce' ), esc_html( $order->get_order_number() ) ); ?></p>
<?php endif; ?>
<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );


// CUSTOMIZATION: Start of custom logic for category-based email messages (from old template 2.5.0)
$cat_68 = false; // online
$cat_73 = false; // member
$cat_41 = false; // course
$cat_47 = false; // equipment

foreach ( $order->get_items() as $item ) {

	$product_id = $item->get_product_id();

	if ( has_term( 68, 'product_cat', $product_id ) ) {
		$cat_68 = true;
	}

	if ( has_term( 73, 'product_cat', $product_id ) ) {
		$cat_73 = true;
	}

	if ( has_term( 41, 'product_cat', $product_id ) ) {
		$cat_41 = true;
	}

	if ( has_term( 47, 'product_cat', $product_id ) ) {
		$cat_47 = true;
	}
}

// Helper function to display custom message if enabled
if ( ! function_exists( 'display_custom_email_message' ) ) {
	function display_custom_email_message( $category_check, $option_name_toggle, $option_name_msg ) {
		if ( $category_check && get_option( $option_name_toggle ) ) {
			$email_msg = get_option( $option_name_msg );
			if ( $email_msg ) {
				// Use wp_kses_post to allow basic HTML and nl2br to preserve line breaks
				echo wp_kses_post( nl2br( $email_msg ) );
				echo "<br><br>";
			}
		}
	}
}

display_custom_email_message( $cat_68, '_email_msg_after_online_toggle', '_email_msg_after_online' );
display_custom_email_message( $cat_73, '_email_msg_after_member_toggle', '_email_msg_after_member' );
display_custom_email_message( $cat_41, '_email_msg_after_course_toggle', '_email_msg_after_course' );
display_custom_email_message( $cat_47, '_email_msg_after_equipment_toggle', '_email_msg_after_equipment' );


// CUSTOMIZATION: Logic to display registered users data (from old template 2.5.0 and admin-new-order.php)
$order_id = $order->get_id();
$data = get_post_meta( $order_id, 'register_users_data', true );

if ( $data ) {
	$data_decoded = json_decode( $data, false );

	// Output the list header.
	echo '<h4>' . esc_html__( 'רשימת נרשמים:', 'woocommerce' ) . '</h4>';

	foreach ( $data_decoded as $key => $item ) {
		// Output Course Title, ensuring the title is escaped.
		echo '<b>' . esc_html__( 'קורס:', 'woocommerce' ) . ' </b>' . esc_html( get_the_title( $key ) ) . '<br><br>';

		foreach ( $item as $in_key => $value ) {
			if ( is_array( $item ) && count( $item ) > 0 ) {
				// Output attendee number.
				echo esc_html( sprintf( __( 'נרשם: %s', 'woocommerce' ), ( $in_key + 1 ) ) ) . '<br>';
			}

			// Output User Details, escaping dynamic data.
			echo '<b>' . esc_html__( 'שם פרטי:', 'woocommerce' ) . ' </b>' . esc_html( $value->name ) . '<br>';
			echo '<b>' . esc_html__( 'שם משפחה:', 'woocommerce' ) . ' </b>' . esc_html( $value->family ) . '<br>';

			// Email link (using wp_kses for allowed tags).
			$email_link = sprintf(
				'<b>%s:</b><a href="%s">%s</a><br>',
				esc_html__( 'דואר אלקטרוני', 'woocommerce' ),
				esc_url( 'mailto:' . $value->email ),
				esc_html( $value->email )
			);
			echo wp_kses( $email_link, array( 'b' => array(), 'a' => array( 'href' => array() ), 'br' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Phone link (using wp_kses for allowed tags).
			$tel_link = sprintf(
				'<b>%s: </b><a href="%s">%s</a><br>',
				esc_html__( 'טלפון', 'woocommerce' ),
				esc_url( 'tel:' . $value->tel ),
				esc_html( $value->tel )
			);
			echo wp_kses( $tel_link, array( 'b' => array(), 'a' => array( 'href' => array() ), 'br' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo '<br>';
		}

		echo '<hr><br>';
	}
}
// CUSTOMIZATION: End of custom logic

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
