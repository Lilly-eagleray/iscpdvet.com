<?php

/**
 * generatepress automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the generatepress Core CSS.
 *
 * If you don't plan to dequeue the generatepress Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */



/* add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 6; // 3 products per row
	}
} */

//ICL_LANGUAGE_CODE

if (!defined('ICL_LANGUAGE_CODE')) {
    define('ICL_LANGUAGE_CODE', 'he');
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


function cfwc_save_custom_field( $post_id ) {
 $product = wc_get_product( $post_id );
 $euro_price = isset( $_POST['euro_price'] ) ? $_POST['euro_price'] : '';
 $product->update_meta_data( 'euro_price', sanitize_text_field( $euro_price ) );
 $dollar_price = isset( $_POST['dollar_price'] ) ? $_POST['dollar_price'] : '';
 $product->update_meta_data( 'dollar_price', sanitize_text_field( $dollar_price ) );


 $product->save();



}
add_action( 'woocommerce_process_product_meta', 'cfwc_save_custom_field' );


function dr_erez_add_dollar_euro_price() {
global $post;
 $args = array(
 'id' => 'euro_price',
 'label' => __( 'Price in euro', 'cfwc' ),
 'class' => 'dr-erez-euro-price',
 'value' => get_post_meta($post->ID,"euro_price",true),
 'desc_tip' => false,
 );
 woocommerce_wp_text_input( $args );

  $args = array(
 'id' => 'dollar_price',
 'label' => __( 'Price in dollar', 'cfwc' ),
 'class' => 'dr-erez-dollar-price',
 'value' =>  get_post_meta($post->ID,"dollar_price",true),
 'desc_tip' => false,
 );
 woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'dr_erez_add_dollar_euro_price' );


function wpb_sender_email( $original_email_address ) {
    return 'iscpdvet@gmail.com';
}

// Function to change sender name
function wpb_sender_name( $original_email_from ) {
    return 'המרכז ללימודי המשך וטרינריים CPD-Vet‎';
}

// Hooking up our functions to WordPress filters
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );

add_action('woocommerce_checkout_update_user_meta', 'dr_erez_checkout_field_update_user_meta');
function dr_erez_checkout_field_update_user_meta( $user_id ) {
	if ($user_id && $_POST['vetid']) update_user_meta( $user_id, '_vetid', esc_attr($_POST['vetid']) );
	if ($user_id && $_POST['billing_phone']) update_user_meta( $user_id, '_user_phone', esc_attr($_POST['billing_phone']) );
}

 function add_user_columns($column) {
    $column['lic'] = 'מספר רישיון';
	$column['phone'] = 'מספר טלפון';
    return $column;
}
add_filter( 'manage_users_columns', 'add_user_columns' );

//add the data
function add_user_column_data( $val, $column_name, $user_id ) {
    $user = get_userdata($user_id);


			if (get_user_meta( $user_id, '_vetid') && $column_name == "lic"){
				return get_user_meta( $user_id, '_vetid')[0];
			}
			else if(get_user_meta( $user_id, '_user_phone') && $column_name == "phone"){
				return get_user_meta( $user_id, '_user_phone')[0];
			}

    return;
}
add_filter( 'manage_users_custom_column', 'add_user_column_data', 10, 3 );

 add_action( 'save_post', 'check_is_registered', 10, 3 );

function check_is_registered( $post_ID, $post, $update ) {
    global $post;
    global $course;
    global $wpdb;

    if ( is_null($post) || !is_a( $post, 'WP_Post' ) ) {
        return;
    }

    if ($post->post_type != 'shop_order'){
        return;
    }
    
    $order = new WC_Order( $post_ID );
    
    if($order->get_status() == "completed"){
        $order_data = $order->get_data();
        $order_billing_email = $order_data['billing']['email'];

        $items = $order->get_items();

        foreach ($items AS $item_id)
        {
            $prod_id = $item_id["product_id"];
            
            $user_obj = get_user_by( 'email', $order_billing_email);
            
            if (!$user_obj) {
                continue;
            }
            $user_id = $user_obj->ID;


            $product_statistics = $wpdb->prefix . "product_statistics";
            $checkIfExsist = $wpdb->get_row( "SELECT * FROM {$product_statistics} WHERE product_id = {$prod_id} AND user_id = {$user_id}" );

            if( is_null( $checkIfExsist ) ){
                $date= date('Y-m-d') ;
                $wpdb->insert(
                    $product_statistics,
                    array(
                        'product_id' => $prod_id,
                        'user_id' => $user_id,
                        'time' => $date
                    )
                );
            }
        }
    }
}

 function wc_empty_cart_redirect_url() {
	return home_url();
}
add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );

 function child_theme_enqueue_styles() {
       $parent_style = 'parent-style'; // This is 'Responsive-style' for the Responsive theme.
       wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
       wp_enqueue_style( 'child-style',
                  get_stylesheet_directory_uri() . '/style.css',
                  array( $parent_style ),
                  wp_get_theme()->get('Version')
       );
}
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );

function child_theme_enqueue_scripts() {
    wp_enqueue_script( 
        'my-custom-main', 
        get_stylesheet_directory_uri() . '/js/main.js', 
        array(), 
        filemtime( get_stylesheet_directory() . '/js/main.js' ), 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_scripts' );

function iscpdvet_admin_enqueue_scripts() {
	wp_enqueue_style('iscpdvet-admin-style', get_stylesheet_directory_uri() . '/admin-css.css',[], '1.0');
}
add_action( 'admin_enqueue_scripts', 'iscpdvet_admin_enqueue_scripts' );

function add_feedback() {
if(!wp_is_mobile()){
	// $current_lang = ICL_LANGUAGE_CODE;
	$current_lang = 'he';
	if($current_lang == "en"){
		$url_to_feedback = get_permalink(445);

	}
	else if($current_lang == "he"){
		$url_to_feedback =  get_permalink(445);
	}
	global $woocommerce;
	$cart_url = wc_get_cart_url();
?>

<div class="drerez_feedback">

	<a href="<?=$url_to_feedback?>">
	<i class="fa fa-pencil-square"></i> &nbsp; צור קשר
	</a>


</div>

<?php /*
<div class="drerez_cart_btn">

	<a href="<?=$cart_url?>">
	<i class="fa fa-shopping-cart" aria-hidden="true"></i>
	</a>


</div>

*/ ?>

<?php
}
}

add_action( 'wp_head', 'add_feedback');

/* add_action( 'woocommerce_add_to_cart', 'add_discount_copun', 10, 3 );
function add_discount_copun($true, $product_id, $quantity) {
	global $woocommerce;
	if($product_id == 746){
		$coupon_code = 'membersj2017';

		if(!$woocommerce->cart->has_discount( $coupon_code )){

			$woocommerce->cart->add_discount( sanitize_text_field( $coupon_code ));

		}
	}
}

add_action( 'woocommerce_cart_item_removed', 'remove_discount_copun', 10, 2);
function remove_discount_copun($removed_cart_item_key, $instance) {
	global $woocommerce;
	$line_item = $instance->removed_cart_contents[ $removed_cart_item_key ];
    $product_id = $line_item[ 'product_id' ];

	if($product_id == 746){
		$coupon_code = 'membersj2017';

		if($woocommerce->cart->has_discount( $coupon_code )){

			$woocommerce->cart->remove_coupon( sanitize_text_field( $coupon_code ));

		}
	}
}
 */
 add_action('woocommerce_cart_coupon', 'dr_erez_discount_when_produts_in_cart');
function dr_erez_discount_when_produts_in_cart( ) {
    global $woocommerce;
	global $course;
	$cart_url = wc_get_cart_url();
	$coupon_code = 'membersj2017';


    $is_in_cart = false;
    foreach ( $woocommerce->cart->get_cart() as $cart_item ){
        if ( $cart_item['product_id'] == 746 ) {
            $is_in_cart = true;
            break;
        }
    }
    if( $is_in_cart ){
        $woocommerce->cart->add_discount( sanitize_text_field( $coupon_code ));
    }else{
        $woocommerce->cart->remove_coupon( sanitize_text_field( $coupon_code ));
    }

    if ( is_user_logged_in() ) {
        global $current_user; wp_get_current_user();
        $course->setUser($current_user);
        $is_premited = $course->getCoursePerUser(743);

        //update by naftali - if user has membersj2017 add discount auto
        if(!$woocommerce->cart->has_discount( $coupon_code ) && $is_premited ){
            $woocommerce->cart->add_discount( sanitize_text_field( $coupon_code ));
        }

        //add by naftali - if user has no membersj2017 remove coupon
        if( !$is_premited && !$is_in_cart){
            $woocommerce->cart->remove_coupon( sanitize_text_field( $coupon_code ));
        }

        /*
        if(!$woocommerce->cart->has_discount( $coupon_code ) && $_GET["discount_member"] && $is_premited){
            $woocommerce->cart->add_discount( sanitize_text_field( $coupon_code ));
        }
        else if(!$woocommerce->cart->has_discount( $coupon_code ) && !$_GET["discount_member"] && $is_premited){
            echo "<a href='" . $cart_url . "?discount_member=1' class='checkout-button button tipso add-member-discount' data-tipso='מועדון CPD-Vet נותן 10% הנחה על קנייה באתר, וכמוכן מאפשר צפייה בוובינרים ובתכנים נוספים'><i class='fa fa-id-badge'></i> הפעל קופון חברי מועדון</a>";
        }
        */
    }
}


function members_area(  ) {
	global $course;
	$current_user = wp_get_current_user();
	$id = $course->returnCurrentLangPost(743);


	$course->setUser($current_user);
	$course_member = $course->getCoursePerUser($id);

	$allinfo = $course->getAllPostOfCourseAutoLang($id);
	$ret_info = '<div class="row club_posts_all">';
	foreach($allinfo AS $info){

	$post_info = get_post( $info->post_id );
	setup_postdata( $post_info );

	$ret_info .= '<div class="col-md-4 club_posts">';

	$ret_info .= '<a href="';

	if(empty($course_member) && !$course->isPublish($post_info->ID)->status){
		$ret_info .= get_permalink(746);
	}
	else{

		 $ret_info .= get_permalink($post_info->ID);
	}
	$ret_info .= '">';
	$ret_info .= '<div class="img-holder" style="background:url(\'';
	if(get_the_post_thumbnail_url($post_info->ID)){
		$ret_info .= get_the_post_thumbnail_url($post_info->ID, array(200, 200));
	}
	else{
		$ret_info .= "https://iscpdvet.com/wp-content/uploads/2017/09/cpd_profile-01.png";
	}

	$ret_info .= '\')"></div>';
	$ret_info .= '<div class="header_club_posts">';
		$ret_info .= '<div>' .$post_info->post_title . '</div>';
	if(get_the_excerpt($post_info->ID) != ""){
		//$ret_info .= '<div class="more_member_info">' . get_the_excerpt($post_info->ID) . '</div>';
	}
	$ret_info .= '</div>
	</a>
	</div>';


	}

	$ret_info .= '</div>';
	return $ret_info;


}
add_shortcode( 'members_area', 'members_area' );


function popup_webinar(){
$popup = '
<script type="text/javascript">function submitForm(){document.form1.target = "myActionWin";window.open("","myActionWin","width=900,height=600,toolbar=0");document.form1.submit();}</script>
<form name="form1" action="http://webinar.iscpdvet.com" method="post">
<input type="hidden" name="user_allow" value="1">
<input type="button" name="btnSubmit" value="פלטפורמת וובינרים" onclick="submitForm()" />
</form>';

return $popup;
}

add_shortcode( 'popup_webinar', 'popup_webinar' );
function member( ) {
		global $course;
		$current_user = wp_get_current_user();
		$course->setUser($current_user);
		$url=strtok($_SERVER["REQUEST_URI"],'?');

		if(!isset($_GET["cat_id"])){
		$output = "<div class='wrapper_personal_area'>";
		$all_category =  get_categories( array(
        'orderby'            => 'id',
        'parent'           => 51,
		'hide_empty'               => 0
		) );

		if(!empty($all_category)){
		foreach($all_category AS $category){


			$output .= "<div class='course_box'><a href='". $url . "?cat_id=" . $category->term_id . "'>";
			$output .= "<div class='img_container' style='background-image:url(";
			if ( z_taxonomy_image_url($category->term_id) != "" ) {
				$output .= z_taxonomy_image_url($category->term_id);
			}
			else {
				$output .= get_stylesheet_directory_uri() . "/img/folder.jpg";
			}
			$output .= ")'></div>";
			$output .= "<h2>" . $category->name . "</h2>";
			$output .= "</a></div>";

		}
		}
		$output .= "</div>";
		}
		if(isset($_GET["cat_id"])){

			$all_parts = get_posts( array(
				'post_type'        => array('post','page'),
				'posts_per_page' => -1,
				'category'       => $_GET["cat_id"]
			) );

			$output .=  '<div class="list-group">';
			$output .=  '<a class="button button_back_to_course_list" href="' . $url . '">'.__("Back to CPD-Vet member page","ultimate-course").'</a>';
			$output .= "<h2>" . get_cat_name($_GET["cat_id"]) . "</h2>";
			foreach($all_parts AS $part){


				if($part->ID && get_post_status ( $part->ID ) == "publish"){


					$output .=  "<a class='list-group-item' href='" . get_the_permalink($part->ID) . "'>" . $part->post_title . "</a>";

				}



			}
			$output .=  "</div>";
		}



echo $output;
}
add_shortcode( 'member', 'member' );



remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
add_action('woocommerce_proceed_to_checkout', 'sm_woo_custom_checkout_button_text',20);

function sm_woo_custom_checkout_button_text() {
    $checkout_url = wc_get_checkout_url();
    ?>
    <a href="<?php echo esc_url($checkout_url); ?>" class="checkout-button button alt wc-forward">
        <?php _e( 'Registration and payment', 'ultimate-course' ); ?>
    </a>
    <?php
}

function woocommerce_order_status_cancelled_custom( $order_id ) {
	//mail("liranhecht@gmail.com","sdfsdF","sdfsdf");
	$order = wc_get_order( $order_id );

	$order_data = $order->get_data();
	$order_billing_email = $order_data['billing']['email'];
	//mail($order_billing_email,"asdasd","Hi Why");

}
add_action( 'woocommerce_order_status_cancelled', 'woocommerce_order_status_cancelled_custom', 10, 1 );


//add title before payment box checkout page

function action_woocommerce_review_order_before_payment(  ) {
    echo "<h2>" . __("Choose payment method","ultimate-course") . "</h2>";
};

// add the action
add_action( 'woocommerce_review_order_before_payment', 'action_woocommerce_review_order_before_payment', 10, 0 );



//add functionality after copun added

function dr_erez_applied_coupon( $array, $int ) {
    ?>
<script>
jQuery(document).ready(function( $ ) {
if ( $( ".coupon" ).length ) {
$( ".coupon" ).append($("<i class='fa fa-check-square'></i>"));
}
});
</script>

	<?php
};

function my_pcl_whitelist_roles( $prevent, $user_id ) {

    $whitelist = array( 'administrator', 'editor' ); // Provide an array of whitelisted user roles

    $user = get_user_by( 'id', absint( $user_id ) );

    $roles = ! empty( $user->roles ) ? $user->roles : array();

    return array_intersect( $roles, $whitelist ) ? false : $prevent;

}
add_filter( 'pcl_prevent_concurrent_logins', 'my_pcl_whitelist_roles', 10, 2 );

function get_currency($price){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'http://www.apilayer.net/api/live?access_key=a3c226f9c77aab6ce161d2b9e9ecc760&format=1');
			$result = curl_exec($ch);
			curl_close($ch);
			$obj = json_decode($result);


$return = '<p class="price">';
$dollar_price = $price/$obj->quotes->USDILS;
$euro_price = $dollar_price*$obj->quotes->USDEUR;
$return .= round($euro_price)  . ".00 &#8364; includes VAT";
$return .= '</p>';
return $return;
}

// Add Free Shipping Under Each Product

function show_free_shipping () {
global $product;
if(!get_post_meta( $product->get_id(), '_hide_stock') || get_post_meta( $product->get_id(), '_hide_stock')[0] == 0 && $product->get_stock_quantity() != ""){
$alert_class = "alert-info";
if($product->get_stock_quantity() < 3){
$alert_class = "alert-danger";
}
?>
<p class="stock-inner <?php echo esc_attr( $class ); ?>"><i class="fa fa-info-circle" aria-hidden="true"></i> נותרו עוד <?php echo $product->get_stock_quantity(); ?> מקומות</p>
<?php
}

}
add_action('woocommerce_after_shop_loop_item','show_free_shipping', 1);

remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count', 20 );

// add input to checkout estimated Production Quantities
function drerez_display_order_data_in_admin( $order ){
    $order = wc_get_order( $order );
    if ( ! $order ) {
        return;
    }

    $order_key = $order->get_order_key(); // במקום order_key
    $order_id  = $order->get_id();        // במקום id

    $url = "<a target='_blank' href='https://iscpdvet.com/checkout/?key={$order_key}&order={$order_id}'>עבור לדף התשלום</a>";
    echo $url;
}


 add_action( 'woocommerce_admin_order_data_after_order_details', 'drerez_display_order_data_in_admin' );

 function dr_erez_stock($order_id){
	$order = wc_get_order($order_id);
	$items = $order->get_items();

    foreach ($items AS $item_id => $item_values)
    {
		$item_data = $item_values->get_data();
		$product_id = $item_data['product_id'];
		$item_data = $item_values->get_data();
		$quantity = $item_data['quantity'];
		$product_id = $item_data['product_id'];
		$quantity_now = get_post_meta($product_id, '_stock', true);

		$new_quantity = $quantity + $quantity_now;

		update_post_meta($product_id, '_stock',$new_quantity);



	}
 }

 add_action( 'woocommerce_order_status_refunded', 'dr_erez_stock', 10, 1);
add_action( 'woocommerce_order_status_cancelled', 'dr_erez_stock', 10, 1);

 remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
 function dr_erez_template_product_description() {
  wc_get_template( 'single-product/tabs/description.php' );
}
add_action( 'woocommerce_single_product_summary', 'dr_erez_template_product_description', 20 );

add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => 'אזור מחליף שפה',
        'id' => 'lang_switch',
        'description' => 'אזור מחליף שפה',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>',
    ) );
}

/*
 * Add customer email to Cancelled Order recipient list
 */
 function wc_cancelled_order_add_customer_email( $recipient, $order ){
     return $recipient . ',' . $order->billing_email;
 }



 add_filter( 'woocommerce_email_recipient_cancelled_order', 'wc_cancelled_order_add_customer_email', 10, 2 );

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );
function custom_woocommerce_product_add_to_cart_text() {
	global $product;
    $numleft  = $product->get_stock_quantity();
    if($numleft==0) {
        return "ההרשמה הסתיימה";
    }
    else{
        return "לרכישה";

    }
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'replace_loop_add_to_cart_button', 10, 2 );
function replace_loop_add_to_cart_button( $button, $product  ) {

    $is_in_cart = false;
    foreach ( WC()->cart->get_cart() as $cart_item )
        if ( $cart_item['product_id'] == $product->get_id() ) {
            $is_in_cart = true;
            break;
    }

    if( $is_in_cart ){
        return '<a style="opacity:0.5;" class="button product_type_simple" href="' . wc_get_cart_url() . '">הזמן <small>(בסל הקניות)</small></a>';
    }

    return $button;
}



function wpse_128636_redirect_post() {


    if ( !current_user_can('administrator') && !is_page('6248') && !is_admin()) {
        //wp_redirect( home_url('/under-construction'), 302 );
        //exit;
    }
}
add_action( 'template_redirect', 'wpse_128636_redirect_post' );




function woocommerce_coupon_options_usage_restriction_naf($coupon_get_id, $coupon){
    ?>

    <p class="form-field"><label for="extra_users"><?php _e( 'איימלים מורשים', 'woocommerce' ); ?></label>
        <select id="extra_users" name="" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'ללא מגבלות', 'woocommerce' ); ?>">
            <?php
                $users   = get_users();
                if ( $users ) {
                    foreach ( $users as $user ) {
                        echo '<option value="' . esc_attr( $user->user_email ) . '">' . esc_html( $user->user_email ) . '</option>';
                    }
                }
            ?>
        </select>
    </p>

    <script>
jQuery(document).ready(function(){

    let all_users = jQuery('#customer_email').val().split(',');
    let num = 0;

    jQuery.each(all_users, function( index, value ) {
        num++;

        if (jQuery('#extra_users').find("option[value='" + value + "']").length ) {
            jQuery('#extra_users').val(value).trigger('change');
        } else {
            var newOption = new Option(value, value, true, true);
            jQuery('#extra_users').append(newOption);
        }

    });
    //jQuery('#extra_users').select2();
    jQuery('#extra_users').select2({ 'tags' : true }).val( all_users ).trigger('change');

    jQuery('#extra_users').on('change', function (e) {
        let all = jQuery('#extra_users option:selected');
        let arr = [];
        jQuery.each(all, function( index, value ) {
            arr.push( jQuery(value).val() );
        });
        jQuery('#customer_email').val( arr.join(',') );
    });
});

</script>
<?php
}
add_action( 'woocommerce_coupon_options_usage_restriction', 'woocommerce_coupon_options_usage_restriction_naf', 10, 2  );

function iscpdvet_set_content_type(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','iscpdvet_set_content_type' );


include 'naf/naf_main.php';


/**
 * Remove password strength check.
 */
function iconic_remove_password_strength() {
    wp_dequeue_script( 'wc-password-strength-meter' );
}
add_action( 'wp_print_scripts', 'iconic_remove_password_strength', 10 );

add_action("template_redirect", function(){
    if(is_page(699)){
        wp_safe_redirect( home_url("my-account/lost-password/"), 302 );
        exit;
    }
});

// add leesons meta box (instead of ACF repeater field)
$lessons_meta_box_file = get_stylesheet_directory() . '/lessons-meta-box.php';
if ( file_exists( $lessons_meta_box_file ) ) {
    require $lessons_meta_box_file;
}

require_once get_stylesheet_directory() . '/inc/template-tags.php';

function my_custom_footer_copyright_info( $copyright ) {
    
    $custom_output = sprintf( '&copy; %1$s המרכז ללימודי המשך וטרינריים &bull; <small>2025</small> &bull; נבנה על ידי <a href="https://www.xplace.com/il/u/pixelbuilt" target="_blank" rel="noopener noreferrer">PIXELBUILT</a>', '2017' );

    return $custom_output;
}
add_filter( 'generate_copyright', 'my_custom_footer_copyright_info' );

add_action( 'init', function() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
});

add_action( 'init', function() {
    if ( is_admin() ) return;

    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
});


add_action( 'woocommerce_after_main_content', function() {
    if ( ! is_product_taxonomy() ) return;

    $term = get_queried_object();
    if ( ! $term ) return;

    $extra_content = get_field( 'extra_content', $term ); 

    if ( ! $extra_content ) return;

    echo '<div class="custom-cat-extra-content">';
    echo wp_kses_post( $extra_content );
    echo '</div>';

});

add_filter( 'template_include', 'force_lecturer_custom_template', 99 );

function force_lecturer_custom_template( $template ) {
    if ( is_singular( 'lecturer_post_type' ) ) {
        $child_template = get_stylesheet_directory() . '/single-lecturer_post_type.php';
        if ( file_exists( $child_template ) ) {
            return $child_template;
        }
    }
    return $template;
}
