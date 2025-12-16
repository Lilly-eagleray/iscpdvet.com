<?php


function ultimate_course_course_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Courses', 'Post Type General Name', 'ultimate-course' ),
		'singular_name'       => _x( 'Course', 'Post Type Singular Name', 'ultimate-course' ),
		'menu_name'           => __( 'Courses', 'ultimate-course' ),
		'parent_item_colon'   => __( 'Main course', 'ultimate-course' ),
		'all_items'           => __( 'All courses', 'ultimate-course' ),
		'view_item'           => __( 'Watch a course', 'ultimate-course' ),
		'add_new_item'        => __( 'Add new course', 'ultimate-course' ),
		'add_new'             => __( 'Add new', 'ultimate-course' ),
		'edit_item'           => __( 'Edit course', 'ultimate-course' ),
		'update_item'         => __( 'Update course', 'ultimate-course' ),
		'search_items'        => __( 'Search a course', 'ultimate-course' ),
		'not_found'           => __( 'No course founded', 'ultimate-course' ),
		'not_found_in_trash'  => __( 'No courses in can', 'ultimate-course' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Courses', 'ultimate-course' ),
		'description'         => __( 'Courses', 'ultimate-course' ),
		'rewrite' => array('slug' => 'course'),

		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports' => array(
            'title',
            'editor',
			 'comments',
			 'thumbnail'
        ),
		'taxonomies'          => array( 'category' ),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		//'taxonomies'          => array( 'genres' ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		// 'capability_type'     => array('gruops_billing','gruops_billing'),
         'map_meta_cap'        => true,
	);
	
	// Registering your Custom Post Type
	register_post_type( 'course_post_type', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'ultimate_course_course_post_type', 0 );


/* Create one or more meta boxes to be displayed on the post editor screen. */
function ultimate_course_add_course_settings() {

  add_meta_box(
    'ultimate_course_course_page',      // Unique ID
    __( 'Course settings', 'ultimate-course' ),    // Title
    'ultimate_course_course_page',   // Callback function
    array('course_post_type'),         // Admin page (or post type)
    'side',         // Context
    'high'         // Priority
  );
  
}
add_action( 'add_meta_boxes', 'ultimate_course_add_course_settings' );

/* Display the post meta box. */
function ultimate_course_course_page( $post ) { 
global $wpdb;
global $course;
global $post;
$course_teacher = "";
$course_level = "";
	if(get_post_meta( $post->ID, '_course_teacher')){
	$course_teacher = get_post_meta( $post->ID, '_course_teacher')[0];	
	}
	if(get_post_meta( $post->ID, '_course_level')){
	$course_level = get_post_meta( $post->ID, '_course_level')[0];
	}
	
	$user = wp_get_current_user();
	$course_levels = $course->returnCourseLevels();
	?>
	
	<?php wp_nonce_field( basename( __FILE__ ), 'ultimate_course_course_type_course_nonce' ); ?>
	
	<div class="form-group">

		<select name="course_level">
		<option value="0"><?php echo __("Select course level","ultimate-course"); ?></option>
		<?php
		if ( function_exists('icl_object_id') ) {
			$curr_lang = ICL_LANGUAGE_CODE;
		}
		else{
			$curr_lang = "he";
		}
	 
		foreach($course_levels[$curr_lang] AS $level){
			
			
		
		?>
		
		<option value="<?=$level?>" 
		<?php
		if($course_level == $level){
		echo " selected";
		}
		?>
		><?=$level?></option>
		
		<?php
		}
		?>
		
		</select>

	</div>
	

<div class="form-group">
	
	<select name="teacher[]" multiple="multiple">
		<option value="0"><?php echo __("Select a teacher","ultimate-course"); ?></option>
		
		
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
	
	<div class="form-group">
	
	<input name="forum_id" placeholder="<?php echo __("Forum id","ultimate-course"); ?>" value="<?php echo (get_post_meta($post->ID,"_forum_id") ? get_post_meta($post->ID,"_forum_id")[0] : ""); ?>">
	
	</div>
<div class="form-group">
	<label><input type="checkbox" name="yearly" value="" <?php
	  if(get_post_meta($post->ID,"_isYearly") ){
		echo "checked";
	  }
	  ?>
	  ><?=__("Yearly subscripation?","ultimate-course")?></label>
</div>

<div class="form-group">
<label><input type="checkbox" name="hide_personal" value="" <?php
	  if(get_post_meta($post->ID,"_hide_me_personal_area") ){
		echo "checked";
	  }
	  ?>
	  ><?=__("Hide from personal area?","ultimate-course")?></label>
<div class="form-group">	  
	  <label><input type="checkbox" name="custom_layout" value="" <?php
	  if(get_post_meta($post->ID,"_custom_layout") ){
		echo "checked";
	  }
	  ?>
	  ><?=__("Use custom list layout?","ultimate-course")?></label>
</div>	  
	 
<div class="form-group">	  
<?php
		$diploma_id = "";
		if(get_post_meta($post->ID,"_diploma_id") ){
		$diploma_id = get_post_meta($post->ID,"_diploma_id")[0];
		}
	 ?>
	 <input type="text" name="diploma_id" value="<?=$diploma_id?>" placeholder="<?=__("Diploma page ID","ultimate-course")?>">
</div>
	 
</div>
	<?php
	
	
	}

	/* Save the meta box's post metadata. */
function ultimate_course_save_post_course_meta( $post_id, $post ) {
	global $wpdb;
	global $course;
	$table_name = $wpdb->prefix . 'post_to_course';
  /* Verify the nonce before proceeding. */
	if ( !isset( $_POST['ultimate_course_course_type_course_nonce'] ) || !wp_verify_nonce( $_POST['ultimate_course_course_type_course_nonce'], basename( __FILE__ ) ) )
    return $post_id;


  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;
if ( wp_is_post_revision( $post_id ) )
		return;
if($post->post_type != "course_post_type")
	return;


  /* Get the posted data and sanitize it for use as an HTML class. */
  $course_level = ( isset( $_POST['course_level'] ) ?  $_POST['course_level']  : '' );
  $hide_personal = ( isset( $_POST['hide_personal'] ) ?  1  : '' );
  $course_teacher = ( isset( $_POST['teacher'] ) ? sanitize_html_class( $_POST['teacher'] ) : '' );
  $forum_id = ( isset( $_POST['forum_id'] ) ? sanitize_html_class( $_POST['forum_id'] ) : '' );
  $yearly = ( isset( $_POST['yearly'] ) ? 1 : '' );
  $custom_layout = ( isset( $_POST['custom_layout'] ) ? 1 : '' );
  $diploma_id = ( isset( $_POST['diploma_id'] ) ? $_POST['diploma_id']  : '' );

	if($diploma_id != ""){
	update_post_meta( $post_id, '_diploma_id', $diploma_id );
	}
	else{
	delete_post_meta( $post_id, '_diploma_id');  
	}
	
//mail("liranhecht@gmail.com","sfdds",json_encode($yearly));
 if($custom_layout != ""){
	update_post_meta( $post_id, '_custom_layout', $custom_layout );
  }
   else{
	delete_post_meta( $post_id, '_custom_layout');  
  }	
  if($hide_personal != ""){
	update_post_meta( $post_id, '_hide_me_personal_area', $hide_personal );
  }
   else{
	delete_post_meta( $post_id, '_hide_me_personal_area');  
  }
  if($forum_id != ""){
	  update_post_meta( $post_id, '_forum_id', $forum_id );
  }
   else{
	delete_post_meta( $post_id, '_forum_id');  
  }
   if($yearly != ""){
	  update_post_meta( $post_id, '_isYearly', $yearly );
  }
   else{
	delete_post_meta( $post_id, '_isYearly');  
  }
  
 if($course_level != ""){
	  update_post_meta( $post_id, '_course_level', $course_level );
  }
   else{
	delete_post_meta( $post_id, '_course_level');  
  }
  
  if($course_teacher != ""){
	  update_post_meta( $post_id, '_course_teacher', $course_teacher );
  }
  else{
	delete_post_meta( $post_id, '_course_teacher');  
  }
  

}
add_action( 'save_post', 'ultimate_course_save_post_course_meta', 10, 2 );


 
?>