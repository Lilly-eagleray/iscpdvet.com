<?php

/* function return_custom_price($price, $product) {
	if ( function_exists('icl_object_id') ) {
		if(ICL_LANGUAGE_CODE == "en"){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'https://api.fixer.io/latest?base=eur');
			$result = curl_exec($ch);
			curl_close($ch);

			$obj = json_decode($result);

			if($product->get_id() == 1308){
				//print_r($obj->rates->ILS);
				//echo $price/$obj->rates->ILS;
				return $price/$obj->rates->ILS;
			}
		}
		else{
			return $price;
		}
	}
	else{
	return $price;
	}
}
add_filter('woocommerce_get_price', 'return_custom_price', 10, 2);


add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);

function change_existing_currency_symbol( $currency_symbol, $currency ) {
	global $post;
	if($post->ID == 1308){
    if ( function_exists('icl_object_id') ) {
		 if(ICL_LANGUAGE_CODE == "en"){
			 return "&#8364;";
		 }
	}
	}
	else
     return $currency_symbol;
}

 */



add_action( 'wp_ajax_update_order', 'update_order' );
function update_order() {
	global $wpdb;
	$all_posts = json_decode(stripslashes($_POST["stringval"]),true);
    $i = 1;
    foreach($all_posts as $_post){
       $wpdb->update(
			$wpdb->prefix . 'post_to_course',
			array(
				"weight"=>$i
			),
			array( 'post_id' => $_post )
		);

        $i++;
    }
    echo json_encode(array("suc"=> "1"));
    wp_die();
}





function ultimate_course_add_course_to_user($order_id)
{

    //getting order object
    $order = wc_get_order($order_id);
	$orderstat = $order->get_status();
if($orderstat != "failed"){
    $items = $order->get_items();
	global $course;



    foreach ($items AS $item_id)
    {
		if(is_user_logged_in()){

			$current_user = wp_get_current_user();

			$current_user->add_role( 'cpd_member' );

			$course->setUser($current_user);

			if(get_post_meta($item_id['product_id'],"_course_related")){

				$course_id = get_post_meta($item_id['product_id'],"_course_related")[0];

                //add only membership
                if( $course_id != '743' ){
                    continue;
                }

				$courses = $course->getCoursePerUser($course_id);

				if( count($courses) == 0 ){

					$listid = $course->addCourseToUser($course_id);

					if($listid != 0){

					}

				}
				else{
					$date = "";
					if(get_post_meta($course_id,"_isYearly") ){
						 $course->delCourseToUserById($course_id,$current_user->ID);
						 $date = strtotime(date("Y-m-d", strtotime($courses[0]->time)) . " +1 year");
						 $date = date("Y-m-d",$date);
					}

					$listid = $course->addCourseToUser($course_id,$date);
				}
				add_prod_stac_single($course_id, $current_user->ID );

			}
		}
    }

	}
}

add_action('woocommerce_thankyou', 'ultimate_course_add_course_to_user');






function ultimate_course_lock_them_out_page($content) {
  // assuming you have created a page/post entitled 'debug'

  global $post;
  global $course;

if($post->post_type == "page"){

			  $course_id = $course->getCourseByPostId($post->ID);

			  if($course_id){
				  $current_user = wp_get_current_user();
				  $course->setUser($current_user);
				  $is_premited = $course->getCoursePerUser($course_id->course_id);
				  if($is_premited || $course->isPublish($post->ID)->status){

					   return $content;
				  }
				  else{

				  $content = '<div class="alert alert-danger text-center">
				' . __("You have no permission to access","ultimate-course") . " " . get_the_title()
				. '<br>';


				$id_course = "";

				$course_id = $course->getCourseByPostId(get_the_id());
				$course_id = $course->returnCurrentLangPost($course_id->course_id);


						$args = array( 'post_type' =>  'product','numberposts' => 1,"suppress_filters"=>0 ,
						'meta_query' => array(
						array(
						'key'   => '_course_related',
						'value' => $course_id,
						)));

						$post_in = get_posts( $args );

						if($post_in){
							$link = get_permalink( $post_in[0]->ID );
						}
						else{
							$link = get_permalink( woocommerce_get_page_id( 'shop' ) );
						}



			$content .= '<a href="' . $link . '">' . __("But you can join the course very easily","ultimate-course") . '</a>
			</div>';

				 return $content;


				  }
			  }
			  else{

				 return $content;
			  }
	}
	else{

				 return $content;
	 }
}

add_filter( 'the_content', 'ultimate_course_lock_them_out_page' );



function personal_area( ) {
		global $course;
		$output = "";
		$current_user = wp_get_current_user();
		$course->setUser($current_user);
		$url=strtok($_SERVER["REQUEST_URI"],'?');
		$output = "<div class='wrapper_personal_area'>";
		if(!isset($_GET["course_id"]) && !isset($_GET["list_id"]) ){

		$all_courses = $course->getAllCoursesByUser();
		if(!empty($all_courses)){
		foreach($all_courses AS $course_to){

			$course_id = $course->returnCurrentLangPost($course_to->course_id);

			if(!get_post_meta($course_id,"_hide_me_personal_area") ){
			$name_of_course = $course->getCourseById($course_id);

			if(get_post_meta($course_id,"_custom_layout")){
				$output .= "<div class='course_box'><a href='". $url . "?list_id=" . $course_id . "&parent_id=303'>";
			}
			else{
				$output .= "<div class='course_box'><a href='". $url . "?course_id=" . $course_id . "'>";
			}

			$output .= "<div class='img_container' style='background-image:url(";
			if ( has_post_thumbnail($course_id) ) {
				$output .= get_the_post_thumbnail_url($course_id);
			}
			else {
				$output .= plugins_url( 'img/doc_def.jpg', __FILE__ );
			}
			$output .= ")'></div>";
			$output .= "<h2>" . $name_of_course->post_title . "</h2>";
			$output .= "</a></div>";
			}



		}
		}
		else{
			echo "<div class='alert alert-info'>" . __("You are not registered to any course yet...","ultimate-course") . "</div>";
		}
		$output .= "</div>";
		}


        if( isset($_GET["course_id"]) && isset($_GET["lesson"]) ){

            if( is_user_logged_in() ){

                $current_user = wp_get_current_user();
                $course->setUser($current_user);
                $is_premited = $course->getCoursePerUser( $_GET["course_id"] );

                if( $is_premited ){
                    include 'c-single-tamp.php';
                }else{
                    echo "<div class='alert alert-danger text-center'>" .
                     __("You have no permission to access","ultimate-course") ." " . get_the_title( $_GET["course_id"] ) . "</div>";
                }

            }else{
                echo "<div class='alert alert-danger text-center'>" .
                 __("You have no permission to access","ultimate-course") ." " . get_the_title( $_GET["course_id"] ) . "</div>";
            }
        }


		if(isset($_GET["course_id"])){
			$name_of_course = $course->getCourseById($_GET["course_id"]);
			$all_classes = $course->getAllPostOfCourse($_GET["course_id"],$course->getLanguge());
			$output .=  '<div class="list-group">';
			$output .=  '<a class="button button_back_to_course_list" href="' . $url . '">'.__("Back to course list","ultimate-course").'</a>';
			$output .= "<h2>" . $name_of_course->post_title . "</h2>";
			foreach($all_classes AS $class){
				if($class->post_id && get_post_status ( $class->post_id ) == "publish"){
					$post_to_show = get_post( $class->post_id );

					if(get_post_meta($class->post_id,"_show_only_if_course_finish",true) == 1){
						$did_finish = $course->didUserWatchedAllLessons($_GET["course_id"],$class->post_id);
							if(!$did_finish){
								$output .=  "<div class='list-group-item'> <a href='" . get_the_permalink($class->post_id) . "'>" . $post_to_show->post_title . "</a>";
								if(get_post_meta($class->post_id,"_quiz")){
									//echo get_post_meta($class->post_id,"_quiz_score",true);
									//echo $course->getCurrentUserLatestScroe(get_post_meta($class->post_id,"_quiz_id",true))->correct_score;
									//echo get_post_meta($class->post_id,"_quiz_id",true);
									if($course->getCurrentUserLatestScroe(get_post_meta($class->post_id,"_quiz_id",true))->correct_score > get_post_meta($class->post_id,"_quiz_score",true) ){
											$output .= "<a class='btn btn-info pull-left' href='" . get_permalink(get_post_meta($_GET["course_id"],"_diploma_id",true)) . "'>לחץ כאן ליצירת תעודת גמר</a>";
									}
								}
								$output .= "</div>";
							}
					}
					else{
					$output .=  "<a class='list-group-item' href='" . get_the_permalink($class->post_id) . "'>" . $post_to_show->post_title . "</a>";
					}

				}
			}


            if( is_array( get_field('lessons', $_GET["course_id"]) ) ){
                $lessons = get_field('lessons', $_GET["course_id"] );
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                foreach ($lessons as $key => $lesson) {

                    $active = '';
                    if( isset( $_GET['lesson']) ){
                        $l = intval( $_GET['lesson'] ) - 1 ;
                        if( $l == $key ){
                            $active = ' active';
                        }
                    }
                    $lesson_title = $lesson['name'];
                    $link = home_url() . '/אזור-אישי/'.  '?course_id='. $_GET["course_id"] .'&lesson=' . ($key + 1);
                    $output .=  "<a class='list-group-item$active' href='$link'>$lesson_title</a>";
                }
            }

			$output .=  "</div>";
		}

		if($_GET["list_id"]){

			$all_parts = $course->isParent($_GET["list_id"]);
			if(isset($_GET["parent_id"]) && $_GET["parent_id"] != 303){
				$course_id = $course->getCourseByPostId($_GET["list_id"]);


				$output .=  '<a class="button_back_to_course_list" href="' . $url . '?list_id=' . $_GET["parent_id"] . '">'.__("Back","ultimate-course").' <i class="fa fa-level-up" aria-hidden="true"></i></a> | ';
				$output .=  '<a class="button_back_to_course_list" href="' . $url . '?list_id=' . $course_id->course_id . '">'.__("Back to","ultimate-course") . get_the_title($course_id->course_id) . '</a>';
			}

			$output .= "<p style='clear:both'></p>";
			$content_post = get_post($_GET["list_id"]);
			$content = apply_filters('the_content', $content_post->post_content);
			if($content != ""){
			$output .= "<p>" . $content . "</p>";
			}
			$output .= "<p style='clear:both'></p>";
			foreach($all_parts AS $part){

					$is_parent = $course->isParent($part->ID);
					$output .= "<a class='list-group-item' href='";
					if(count($is_parent) != 0){
						$output .=  $url . "?list_id=" . $part->ID . "&parent_id=" . $_GET["list_id"];
					}
					else{
						$output .= get_permalink($part->ID);
					}
					$output .= "'>" . $part->post_title . "</a>";
			}
		}



echo $output;
}
add_shortcode( 'personal_area', 'personal_area' );


add_filter('woocommerce_is_purchasable', 'my_woocommerce_is_purchasable', 10, 2);
function my_woocommerce_is_purchasable($is_purchasable, $product) {
		if(is_user_logged_in()){
			global $wpdb;
			global $course;


			$course_id = get_field("_course_related", $product->get_id() );

            if( !$course_id ){
                return $is_purchasable;
            }

			$current_user = wp_get_current_user();

			$course->setUser($current_user);
			$courses = $course->getCoursePerUser( $course_id );

			if(count($courses) == 0 || get_post_meta($course_id,"_isYearly")){

			    return $is_purchasable;
			}else{
			    return false;
			}
		}
		else{
			return $is_purchasable;
		}
}


add_filter( 'woocommerce_add_to_cart_validation', 'validate_active_subscription', 10, 3 );
function validate_active_subscription($true, $product_id, $quantity){




			global $wpdb;
			global $course;
			global $woocommerce;
			$cart_url = wc_get_cart_url();
			$ids = array();
			$items = $woocommerce->cart->get_cart();

			foreach($items as $item => $values) {
				$ids[] = $values['product_id'];
			}

			if(in_array($product_id,$ids)){
				wp_safe_redirect($cart_url);
				exit();

			}
			else{
			return $true;
			}

			return $true;


}

 function ultimate_course_post_locked($single_template) {
    global $wp_query, $post;
	global $course;
	$course_id = $course->getCourseByPostId($post->ID);
	 if ($post->post_type != 'lesson_post_type' && !empty($course_id)){
    $single_template = plugin_dir_path(__FILE__) . 'single-locked.php';
    return $single_template;
	 }
}


add_filter( 'single_template', 'ultimate_course_post_locked',1,10 ) ;



function drerez_css() {
	global $post;
	if(get_post_meta($post->ID,"_ltr")){
    ?>
        <style>
            .entry-content{
				direction:ltr !important;
			}
        </style>
    <?php
	}
}
add_action('wp_head', 'drerez_css');

function remove_product_description_add_cart_button(){
    global $product;
	global $course;
    global $post;
    $current_user = wp_get_current_user();
	$days = $course->isUserSubscripationValid($current_user->ID,743);
if( $days != "-1"){
	if( $days < 335 && $current_user->ID != 0 && $post->ID == 746 ){
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}
}

}
add_action('wp','remove_product_description_add_cart_button');

function add_to_cat_club( $atts ) {
	global $product;
	global $course;
    global $post;
    $current_user = wp_get_current_user();
	$days = $course->isUserSubscripationValid($current_user->ID,743);

if( $days != "-1" && $current_user->ID != 0){
	if( $days > 335 ){
		return '<a class="single_add_to_cart_button button alt" href="'. do_shortcode('[add_to_cart_url id="746"]') . '">הוסף לסל</a>';
	}
}
else{
	return '<a class="single_add_to_cart_button button alt" href="'. do_shortcode('[add_to_cart_url id="746"]') . '">הוסף לסל</a>';
}
}
add_shortcode( 'add_to_cat_club', 'add_to_cat_club' );

?>
