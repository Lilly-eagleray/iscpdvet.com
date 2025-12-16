<?php
/*
Plugin Name: Ultimate Course
Author: Liran hecht
Version: 1.6

*/
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */
include "db.php";
include "classCourse.php";
include "woocommerce/wooField.php";
include "woocommerce/gateway_tranzila.php";
include "addPostTypeClass.php";
include "addPostTypeCourse.php";
include "addPostTypeTeacher.php";
include "admin/admin.php";
include "enque.php";
include "functions.php";
include "checkIsPremitrd.php";

add_action('plugins_loaded', 'ultimate_course_load_textdomain');
function ultimate_course_load_textdomain() {
	load_plugin_textdomain( 'ultimate-course', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

$course = new Course;

/**
 * @snippet       WooCommerce add fee to checkout for a gateway ID
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.7
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
add_action( 'woocommerce_cart_calculate_fees', 'bbloomer_add_checkout_fee_for_gateway', 10, 1  );
  
function bbloomer_add_checkout_fee_for_gateway( $cart_object ) {
   $chosen_gateway = WC()->session->get( 'chosen_payment_method' );
   if ( $chosen_gateway == 'ppcp-gateway' ) {
      $cart_total = WC()->cart->get_cart_contents_total();
      $fee_amount = $cart_total * 0.05; // 5% fee
      WC()->cart->add_fee( 'עמלת פייפל', $fee_amount );
   }
}
 
add_action( 'woocommerce_after_checkout_form', 'bbloomer_refresh_checkout_on_payment_methods_change' );
   
function bbloomer_refresh_checkout_on_payment_methods_change(){
    wc_enqueue_js( "
      $( 'form.checkout' ).on( 'change', 'input[name^=\'payment_method\']', function() {
         $('body').trigger('update_checkout');
        });
   ");
}

?>