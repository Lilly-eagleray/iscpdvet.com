<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 10.0.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
echo $email_improvements_enabled ? '<div class="email-introduction">' : '';
/* translators: %s: Customer billing full name */
$text = __( 'You’ve received the following order from %s:', 'woocommerce' );
if ( $email_improvements_enabled ) {
	/* translators: %s: Customer billing full name */
	$text = __( 'You’ve received a new order from %s:', 'woocommerce' );
}
?>
<p><?php printf( esc_html( $text ), esc_html( $order->get_formatted_billing_full_name() ) ); ?></p>
<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

// CUSTOMIZATION: Start of custom logic to display registered users data (from old template 3.7.0)
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
