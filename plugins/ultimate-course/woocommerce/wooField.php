<?php

		add_action('woocommerce_before_order_notes', 'ultimate_course_checkout_field');
		 
		function ultimate_course_checkout_field($checkout)
		{
			
			$year_list = array();
			$year_list[0] = __("Select Year","ultimate-course");
			$info = '';
			$vetyear = '';
			for ($x = 1950; $x <= 2101; $x++) {
				$year_list[$x] = $x;
			} 

			/* $placeholder = "";
			$display = "hide_me";
			$current_user = wp_get_current_user();
			if ( 0 == $current_user->ID ) {
				$def = "vet";
				$placeholder = __('Veterinary license number',"ultimate-course") ;
				$display = "";
			} else {
				$type = get_user_meta( $current_user->ID, '_vettype' )[0];
				$info = get_user_meta( $current_user->ID, '_vetinfo' )[0];
				
				if($type == "vet"){
					$def = "vet";
					$display = "show_me";
					$placeholder = __('Veterinary license number',"ultimate-course");
				}
				else if($type == "student"){
					$def = "student";
					$placeholder = __('Name of the institution',"ultimate-course");
				}
				else if($type == "tech"){
					$def = "tech";
					$placeholder = __('Name of the clinic',"ultimate-course");
				}
			}
			woocommerce_form_field( 'vettype', array(
			'type'          => 'select',
			'class'         => array('type-of-person','form-row-wide'),
			'label'         => __('',"ultimate-course"),
			'required'    => false,
			'options'     => array(
			"vet" => __('Veterinar',"ultimate-course"),
			"student" => __('Student',"ultimate-course"),
			"tech" => __('Technician',"ultimate-course")
			),
			'default' => $def), 
			$checkout->get_value( 'vettype' ));
 */
			$current_user = wp_get_current_user();
			$display = "";
			if ( $current_user->ID ) {
				$info    = get_user_meta( $current_user->ID, '_vetinfo', true );
				$vetyear = get_user_meta( $current_user->ID, '_vetyear', true );

				if ( is_array( $info ) )    $info    = $info[0]    ?? '';
				if ( is_array( $vetyear ) ) $vetyear = $vetyear[0] ?? '';
			}		
			
			woocommerce_form_field('vetid', array(
				'type' => 'text',
				'class' => array(
					'ultimate_course_vetid form-row-wide'
				) ,
				'label' => __('Veterinary license number',"ultimate-course"),
				'required' => false,
				'default' => $info,
			) , $checkout->get_value('vetid'));
			
			woocommerce_form_field( 'vetyear', array(
			'type'          => 'select',
			'class'         => array('year','form-row-wide',$display),
			'label'         => __('Year of obtaining the Veterinary degree',"ultimate-course"),
			'required'    => false,
			'options'     => $year_list,
			'default' => $vetyear), 
			$checkout->get_value( 'vetyear' ));			
			
		}
		
		
		add_action('woocommerce_checkout_process', 'ultimate_course_checkout_field_process');

		function ultimate_course_checkout_field_process() {
			// Check if set, if its not set add an error.
			//if ( ! $_POST['vetid'] ){
				//wc_add_notice( __( 'Please fill in missing info.' ,"ultimate-course"), 'error' );
			//}
			//if ( ! $_POST['vetyear'] ||  $_POST['vetyear'] == 0)
				//wc_add_notice( __( 'Please fill in year of obtaining the Veterinary degree.' ,"ultimate-course"), 'error' );
		}
		
		add_action( 'woocommerce_checkout_update_order_meta', 'ultimate_course_checkout_field_update_order_meta', 10, 1 );
		function ultimate_course_checkout_field_update_order_meta( $order_id ) {
			$current_user = wp_get_current_user();
			/* if($current_user->ID != 0 &&  ! empty( $_POST['vettype'] )){
				update_user_meta( $current_user->ID, '_vettype', sanitize_text_field( $_POST['vettype'] ) );
				
			} */
			if ( ! empty( $_POST['vetid'] ) ) {
				update_post_meta( $order_id, '_vetid', sanitize_text_field( $_POST['vetid'] ) );
				if($current_user->ID != 0){
					update_user_meta( $current_user->ID, '_vetinfo', sanitize_text_field( $_POST['vetid'] ) );
				}
				
			}
			if ( ! $_POST['vetyear'] ||  $_POST['vetyear'] != 0){
				
				update_post_meta( $order_id, '_vetyear', sanitize_text_field( $_POST['vetyear'] ) );
				if($current_user->ID != 0){
					update_user_meta( $current_user->ID, '_vetyear', sanitize_text_field( $_POST['vetyear'] ) );
				}
			}
}
   
   
   
   
/* Create one or more meta boxes to be displayed on the post editor screen. */
function ultimate_course_add_product_settings() {

  add_meta_box(
    'ultimate_course_product_page',      // Unique ID
    __( 'Product course settings', 'ultimate-course' ),    // Title
    'ultimate_course_product_page',   // Callback function
    array('product'),         // Admin page (or post type)
    'side',         // Context
    'high'         // Priority
  );
  
}
add_action( 'add_meta_boxes', 'ultimate_course_add_product_settings' );

/* Display the post meta box. */
function ultimate_course_product_page( $post ) { 
global $wpdb;
global $course;
global $post;
$course_teacher = "";
	if(get_post_meta( $post->ID, '_course_teacher')){
	$course_teacher = get_post_meta( $post->ID, '_course_teacher')[0];	
	}
	
	
	$user = wp_get_current_user();
	
	?>
	
	<?php wp_nonce_field( basename( __FILE__ ), 'ultimate_course_product_nonce' ); ?>
	
	

<div class="form-group">
	
		<select name="teacher[]" multiple="multiple">
		<option value="0"><?php echo __("Select a lecturer","ultimate-course"); ?></option>
		
		
		<?php
		$post_list = $course->getLecturers();

			 
		foreach ( $post_list as $post_in ) {
		?>
		<?=$post_in->ID?>
		<option value="<?=$post_in->ID?>"
		<?php
		if(is_array($course_teacher) && in_array($post_in->ID,$course_teacher)){
		echo " selected";
		}
		?>
		
		><?=get_the_title($post_in->ID)?></option>
		<?php } ?>
		</select>

	</div>
	

<?php
$courses = $course->getAllCourses();

	?>
	
   
	<div class="form-group">
	<select name="list_courses">
	<option value="0"><?php echo __("Select a course","ultimate-course"); ?></option>
	<?php

	$courses = $course->getAllCourses();
	//print_r($courses);

	foreach( $courses as $post_loaded ){
	?>
	
	<?php
		
	$related_courses_array = get_post_meta($post->ID, "_course_related");

	if (!empty($related_courses_array) && is_array($related_courses_array)) {
		$related_is = $related_courses_array[0];
	} else {
		$related_is = 0; 
	}

	echo $related_is;		
	?>
	<option value="<?=$post_loaded->ID?>"
	<?php
	if($related_is == $post_loaded->ID){
		echo " selected";
		}
	?>
	><?=$post_loaded->post_title?></option>
	<?php
	}
	?>
	</select>
	
	</div>
	<?php
	
	if(get_post_meta( $post->ID, '_hide_stock')){
		$hide_stock = get_post_meta( $post->ID, '_hide_stock')[0];
	}
	?>
	
		<div>
		
		<label><input type="checkbox" value="1" name="hide_stock" class="hide_stock" <?php echo ($hide_stock == 1 ? "checked" : ""); ?>><span class="radio_text"><?=__("Hide stock","ultimate-course")?></span></label>
		
		</div>
	
	<?php
	
	if(get_post_meta( $post->ID, '_hide_member_text')){
		$hide_member_text = get_post_meta( $post->ID, '_hide_member_text')[0];
	}
	
	?>
	
		<div>
		
		<label><input type="checkbox" value="1" name="hide_member_text" class="hide_member_text" <?php echo ($hide_member_text == 1 ? "checked" : ""); ?>><span class="radio_text"><?=__("Hide member text","ultimate-course")?></span></label>
		
		</div>
	<?php
	
	
	}
	
	
	/* Create one or more meta boxes to be displayed on the post editor screen. */
function ultimate_course_add_add_product_settings() {

  add_meta_box(
    'ultimate_course_product_add_page',      // Unique ID
    __( 'Additional info', 'ultimate-course' ),    // Title
    'ultimate_course_product_add_page',   // Callback function
    array('product'),         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );
  
}
add_action( 'add_meta_boxes', 'ultimate_course_add_add_product_settings' );

/* Display the post meta box. */
function ultimate_course_product_add_page( $post ) { 
global $wpdb;
global $course;
global $post;
$course_teacher = "";
	if(get_post_meta( $post->ID, '_course_teacher')){
	$course_teacher = get_post_meta( $post->ID, '_course_teacher')[0];	
	}
	
	
	$user = wp_get_current_user();
	
	?>
	
	<?php wp_nonce_field( basename( __FILE__ ), 'ultimate_course_product_nonce' );
	$text_editor = "";
	if(get_post_meta( $post->ID, '_add_info_product_course')){
		$text_editor = get_post_meta( $post->ID, '_add_info_product_course')[0];
	}
	wp_editor($text_editor , "add_info_product_course" );
	?>
	<div class="form-group">
	  <label for="usr">דף ביטולים:</label>
	  <?php
	     $selected = "";
	  if(get_post_meta( $post->ID, '_cancel_page')){
		  $selected = get_post_meta( $post->ID, '_cancel_page')[0];
	  }
			  $dropdown_args = array(
				'post_type'        => "page",
				'show_option_none' => __('Chosse page'),
				'name'             => 'cancel_page',
				'sort_column'      => 'menu_order, post_title',
				'echo'             => 0,
				'selected'         =>	$selected,

			);

		echo wp_dropdown_pages( $dropdown_args );
	  
	  ?>
	</div>
	
	<div class="form-group">
	  <label for="usr">סילבוס:</label>
	  <?php
	   $selected = "";
	  if(get_post_meta( $post->ID, '_info_page')){
		  $selected = get_post_meta( $post->ID, '_info_page')[0];
	  }
			  $dropdown_args = array(
				'post_type'        => "page",
				'show_option_none' => __('Chosse page'),
				'name'             => 'info_page',
				'sort_column'      => 'menu_order, post_title',
				'echo'             => 0,
				'selected'         =>  $selected,
			);

		echo wp_dropdown_pages( $dropdown_args );
	  
	  ?>
	</div>
	<?php
	 $alert_type = "";
	 if(get_post_meta( $post->ID, '_alert_type')){
		  $alert_type = get_post_meta( $post->ID, '_alert_type')[0];
		  
		 
	  }
	  $course_massage = "";
	 if(get_post_meta( $post->ID, '_course_massage')){
		  $course_massage = get_post_meta( $post->ID, '_course_massage')[0];
		 
	  }
	 ?>
	<div class="form-group">
	      <input type="text" class="form-control" name="course_massage" id="massage" value="<?=$course_massage?>" placeholder="תוכן ההודעה...">
		  <select name="alert_type">
		  <option value="0"><?=__("Choose alert type","ultimate-course")?></option>
		  <option 
		  <?php echo ($alert_type == "alert-danger" ? 'selected' : ''); ?>
		  value="alert-danger"><?=__("Alert (red)","ultimate-course")?></option>
		  <option 
		  <?php echo ($alert_type == "alert-sucsses" ? 'selected' : ''); ?>
		  value="alert-sucsses"><?=__("Sucsses (green)","ultimate-course")?></option>
		  
		  </select>
	</div>
	<?php
	
	}

	/* Save the meta box's post metadata. */
function ultimate_course_save_post_product_meta( $post_id, $post ) {
	global $wpdb;
	global $course;
	$table_name = $wpdb->prefix . 'post_to_course';
  /* Verify the nonce before proceeding. */
	if ( !isset( $_POST['ultimate_course_product_nonce'] ) || !wp_verify_nonce( $_POST['ultimate_course_product_nonce'], basename( __FILE__ ) ) )
    return $post_id;


  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;
if ( wp_is_post_revision( $post_id ) )
		return;
if($post->post_type != "product")
	return;

	
	
 $hide_stock = ( isset( $_POST['hide_stock'] ) ? sanitize_html_class( $_POST['hide_stock'] ) : '' );
  if($hide_stock){
	
	  update_post_meta( $post_id, '_hide_stock', $hide_stock );
  }
  else{
	  update_post_meta( $post_id, '_hide_stock', 0 );
  }
  
   $hide_member_text = ( isset( $_POST['hide_member_text'] ) ? sanitize_html_class( $_POST['hide_member_text'] ) : '' );
  if($hide_member_text){
	
	  update_post_meta( $post_id, '_hide_member_text', $hide_member_text );
  }
  else{
	  update_post_meta( $post_id, '_hide_member_text', 0 );
  }
  
  
  $info_page = ( isset( $_POST['info_page'] ) ? sanitize_html_class( $_POST['info_page'] ) : '' );
  if($info_page){
	
	  update_post_meta( $post_id, '_info_page', $info_page );
  }
  else{
	  delete_post_meta( $post_id, '_info_page');
  }
   /* Get the posted data and sanitize it for use as an HTML class. */
  $cancel_page = ( isset( $_POST['cancel_page'] ) ? sanitize_html_class( $_POST['cancel_page'] ) : '' );
  if($cancel_page){
	
	  update_post_meta( $post_id, '_cancel_page', $cancel_page );
  }
   else{
	  delete_post_meta( $post_id, '_cancel_page');
  }
   /* Get the posted data and sanitize it for use as an HTML class. */
  $add_info_product_course = ( isset( $_POST['add_info_product_course'] ) ? $_POST['add_info_product_course']  : '' );
  if($add_info_product_course){
	
	  update_post_meta( $post_id, '_add_info_product_course', $add_info_product_course );
  }
   else{
	  delete_post_meta( $post_id, '_add_info_product_course');
  }
  /* Get the posted data and sanitize it for use as an HTML class. */
  $course_teacher = ( isset( $_POST['teacher'] ) ? sanitize_html_class( $_POST['teacher'] ) : '' );

  if($course_teacher){
	
	  update_post_meta( $post_id, '_course_teacher', $course_teacher );
  }
   else{
	  delete_post_meta( $post_id, '_course_teacher');
  }
  
    /* Get the posted data and sanitize it for use as an HTML class. */
  $list_courses = ( isset( $_POST['list_courses'] ) ? sanitize_html_class( $_POST['list_courses'] ) : '' );
  
  if($list_courses){
	  update_post_meta( $post_id, '_course_related', $list_courses );			
	}
	 else{
	  delete_post_meta( $post_id, '_course_related');
  }
  
   $alert_type = ( isset( $_POST['alert_type'] ) ? sanitize_html_class(  $_POST['alert_type'] ) : '' );
  if($alert_type != ""){
	  update_post_meta( $post_id, '_alert_type', $alert_type );			
	}
	 else{
	  delete_post_meta( $post_id, '_alert_type');
  }
  
  
      /* Get the posted data and sanitize it for use as an HTML class. */
  $course_massage = ( isset( $_POST['course_massage'] ) ? sanitize_html_class( $_POST['course_massage'] ) : '' );
  if($course_massage){
	  update_post_meta( $post_id, '_course_massage', $course_massage );			
	}
	 else{
	  delete_post_meta( $post_id, '_course_massage');
  }
  
}
add_action( 'save_post', 'ultimate_course_save_post_product_meta', 10, 2 );
   
function reduce_woocommerce_min_strength_requirement( $strength ) {
    return 3;
}
add_filter( 'woocommerce_min_password_strength', 'reduce_woocommerce_min_strength_requirement' ); 

/* Add to the functions.php file of your theme */
add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' ); 

function woo_custom_order_button_text() {
    return __( 'Continue', 'woocommerce' ); 
}

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
	 $fields['order']['order_comments']['label'] = __("Anything important?","ultimate-course");
     $fields['order']['order_comments']['placeholder'] = '';
	 $fields['billing']['billing_address_1']['label'] = __("Your Address","ultimate-course");
	 $fields['billing']['billing_address_1']['placeholder'] = __("Street, City","ultimate-course");
	 $fields['billing']['billing_first_name']['label'] = __("First name in english","ultimate-course");
	 $fields['billing']['billing_last_name']['label'] = __("Last name in english","ultimate-course");
	
	
	
	 $fields['billing']['billing_phone']['required'] = true;
	 
	 $fields['account']['account_password']['label'] = __("Account Password, please remember this password since it's going to be your account password.","ultimate-course");
	 $fields['account']['account_username']['label'] = __("Account username (This will be the username of your account)","ultimate-course");
	 
	 unset($fields['account']['account_password']);
	 //unset($fields['account']['account_username']);
	 unset($fields['billing']['billing_address_2']);
	 unset($fields['billing']['billing_postcode']);
	 unset($fields['billing']['billing_city']);


     return $fields;
}



?>