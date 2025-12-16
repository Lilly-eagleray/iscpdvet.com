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
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( "Thank you. Your order has been received.", 'woocommerce' ); ?></p>

<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );



$cat_68 = false; // online
$cat_73 = false; // member
$cat_41 = false; // course
$cat_47 = false; // equipment

foreach ( $order->get_items() as $item_id => $item ) {

    $product_id = $item->get_product_id();
    if( has_term( 68, 'product_cat', $product_id  ) ){
        $cat_68 = true;
    }

    if( has_term( 73, 'product_cat', $product_id  ) ){
        $cat_73 = true;
    }

    if( has_term( 41, 'product_cat', $product_id  ) ){
        $cat_41 = true;
    }

    if( has_term( 47, 'product_cat', $product_id  ) ){
        $cat_47 = true;
    }

}

if( $cat_68 && get_option('_email_msg_after_online_toggle') ){
    $email_msg_after_online = get_option('_email_msg_after_online');
    echo nl2br( $email_msg_after_online ) ;
    echo "<br><br>";
}


if( $cat_73 && get_option('_email_msg_after_member_toggle') ){
    $email_msg_after_member = get_option('_email_msg_after_member');
    echo nl2br( $email_msg_after_member ) ;
    echo "<br><br>";
}


if( $cat_41 && get_option('_email_msg_after_course_toggle') ){
    $email_msg_after_course = get_option('_email_msg_after_course');
    echo nl2br( $email_msg_after_course ) ;
    echo "<br><br>";
}

if( $cat_47 && get_option('_email_msg_after_equipment_toggle') ){
    $email_msg_after_equipment = get_option('_email_msg_after_equipment');
    echo nl2br( $email_msg_after_equipment ) ;
    echo "<br><br>";
}






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


/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
