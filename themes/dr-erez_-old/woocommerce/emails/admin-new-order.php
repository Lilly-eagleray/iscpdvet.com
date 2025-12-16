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
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer billing full name */ ?>
<p><?php printf( esc_html__( 'You’ve received the following order from %s:', 'woocommerce' ), $order->get_formatted_billing_full_name() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
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


$order_id = $order->get_id();

$data = get_post_meta( $order_id, 'register_users_data', true );

if( $data ){
    $data = json_decode( $data );

    echo '<h4>רשימת נרשמים:</h4>';

    foreach ( $data as $key => $item ) {
        echo '<b>קורס: </b>' . get_the_title( $key ) . '<br><br>';

        foreach ( $item as $in_key => $value) {
            if( is_array( $item )  && count( $item ) > 0 ){
                echo "נרשם: " . ( $in_key + 1 ) . '<br>';
            }
            echo '<b>שם פרטי: </b>' . $value->name . '<br>';
            echo '<b>שם משפחה: </b>' . $value->family . '<br>';
            echo "<b>דואר אלקטרוני:</b><a href='mailto:$value->email'>$value->email</a><br>";;
            echo "<b>טלפון: </b><a href='tel:$value->tel'>$value->tel</a><br>";
            echo '<br>';
        }

        echo '<hr><br>';
    }
}

//print_regiser_users_data( $order->get_id() );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
