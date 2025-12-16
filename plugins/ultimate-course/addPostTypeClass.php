<?php


function drerez_lesson_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Lessons', 'Post Type General Name', 'ultimate-course' ),
		'singular_name'       => _x( 'Lesson', 'Post Type Singular Name', 'ultimate-course' ),
		'menu_name'           => __( 'Lessons', 'ultimate-course' ),
		'parent_item_colon'   => __( 'Main lesson', 'ultimate-course' ),
		'all_items'           => __( 'All lessons', 'ultimate-course' ),
		'view_item'           => __( 'Watch lesson', 'ultimate-course' ),
		'add_new_item'        => __( 'Add new lesson', 'ultimate-course' ),
		'add_new'             => __( 'Add new', 'ultimate-course' ),
		'edit_item'           => __( 'Edit lesson', 'ultimate-course' ),
		'update_item'         => __( 'Update lesson', 'ultimate-course' ),
		'search_items'        => __( 'Search a lesson', 'ultimate-course' ),
		'not_found'           => __( 'No Lessons found', 'ultimate-course' ),
		'not_found_in_trash'  => __( 'No Lessons in can', 'ultimate-course' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Lessons', 'ultimate-course' ),
		'description'         => __( 'Lessons', 'ultimate-course' ),
		'rewrite' => array('slug' => 'lesson'),

		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports' => array(
            'title',
            'editor',
            'excerpt',
            'trackbacks',
            'custom-fields',
            'comments',
            'revisions',
            'thumbnail',
            'author',
            'page-attributes'
        ),
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
	register_post_type( 'lesson_post_type', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'drerez_lesson_post_type', 0 );


/* Create one or more meta boxes to be displayed on the post editor screen. */
function drerez_add_post_meta_boxes() {

  add_meta_box(
    'drerez-post-related',      // Unique ID
    __( 'Related to course', 'ultimate-course' ),    // Title
    'drerez_course_related',   // Callback function
    array('page','post','lesson_post_type'),         // Admin page (or post type)
    'side',         // Context
    'high'         // Priority
  );
  
}
add_action( 'add_meta_boxes', 'drerez_add_post_meta_boxes' );

/* Display the post meta box. */
function drerez_course_related( $post ) { 
global $wpdb;
global $course;
global $post;
	$user = wp_get_current_user();
?>

  

<div class="bootstrap-iso">
<?php
$courses = $course->getAllCourses();

	?>
	
    <label for="drerez-post-class"><?php _e( "Select the related course", 'ultimate-course' ); ?></label>
	<div class="form-group">
	
	<?php wp_nonce_field( basename( __FILE__ ), 'ultimate_course_class_type_course_nonce' ); ?>
	<select name="list_courses">
	<option value="0"><?php echo __("Select a course","ultimate-course"); ?></option>
	<?php
		
	$courses = $course->getAllCourses();
	//print_r($courses);

	foreach( $courses as $post_loaded ){
	?>
	
	<?php
	$courses_is = $course->isExistPostInCourse($post->ID, $post_loaded->ID);
	
	?>
	<option value="<?=$post_loaded->ID?>"
	<?php if(!empty($courses_is)){
		echo " selected";
	}
	?>
	><?=$post_loaded->post_title?></option>
	<?php
	}
	?>
	</select>
	<br>
	
	  <label><input type="checkbox" name="status" value="" <?php
	  if($course->isPublish($post->ID) && $course->isPublish($post->ID)->status ){
		echo "checked";
	  }
	  
	  ?>
	  ><?=__("Public?","ultimate-course")?></label>
	   
	   <label><input type="checkbox" name="ltr" value="1" <?php
	  if(get_post_meta($post->ID,"_ltr")){
		 
		echo "checked";
	  }
	  
	  ?>
	  ><?=__("Left to right","ultimate-course")?></label>
	  
	  
	<label><input type="checkbox" name="show_only_if_course_finish" value="1" <?php
	  if(get_post_meta($post->ID,"_show_only_if_course_finish")){
		 
		echo "checked";
	  }
	  
	  ?>
	  ><?=__("Show only if course finish","ultimate-course")?></label>
	 <br>
	 <label><input type="checkbox" name="quiz" value="1" <?php
	  if(get_post_meta($post->ID,"_quiz")){
		 
		echo "checked";
	  }
	  
	  ?>
	  ><?=__("Is quiz?","ultimate-course")?></label>
	  
	   <?php
		$quiz_score = "";
		if(get_post_meta($post->ID,"_quiz_score") ){
		$quiz_score = get_post_meta($post->ID,"_quiz_score")[0];
		}
	 ?>
	 <br>
	 <input type="text" name="quiz_score" value="<?=$quiz_score?>" placeholder="<?=__("Quiz min score","ultimate-course")?>">
	 <br>
	   
	   <?php
		$quiz_id = "";
		if(get_post_meta($post->ID,"_quiz_score") ){
		$quiz_id = get_post_meta($post->ID,"_quiz_id")[0];
		}
	 ?>
	 <input type="text" name="quiz_id" value="<?=$quiz_id?>" placeholder="<?=__("Quiz ID","ultimate-course")?>">
<br>
  
	 
	 
	  <?php
		$parent_id = "";
		if(get_post_meta($post->ID,"_parent_id_ultimate") ){
		$parent_id = get_post_meta($post->ID,"_parent_id_ultimate")[0];
		}
	 ?>
	<div>
	<input type="text" name="parent_id_ultimate" value="<?=$parent_id?>" placeholder="<?=__("Parent id","ultimate-course")?>">
	</div> 
	</div>
  </div>
<?php }

/* Save the meta box's post metadata. */
function ultimate_course_save_post_class_meta( $post_id, $post ) {
	global $wpdb;
	global $course;
	$table_name = $wpdb->prefix . 'post_to_course';
  /* Verify the nonce before proceeding. */
	if ( !isset( $_POST['ultimate_course_class_type_course_nonce'] ) || !wp_verify_nonce( $_POST['ultimate_course_class_type_course_nonce'], basename( __FILE__ ) ) )
    return;


  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;
if ( wp_is_post_revision( $post_id ) )
		return;
if($post->post_type != "product"){
  /* Get the posted data and sanitize it for use as an HTML class. */
  $list_courses = ( isset( $_POST['list_courses'] ) ? sanitize_html_class( $_POST['list_courses'] ) : '' );
  $status = 0;
    if($list_courses != 0){
			if(isset($_POST['status'])){
				$status = 1;
			}
			$courses = $course->getCourseByPostId($post_id);

			$current_lang = $course->getLanguge();
			
			if(is_array($courses) && count($courses) != 0){
			$course->updatePostToCourse($post_id,$list_courses,$courses->id,$status);
			}
			else{
			$course->addPostToCourse($post_id,$list_courses,$current_lang,$status);
			}
	}
	else{
		$course->removeClassById($post_id);
	}
	
	$parent_id = ( isset( $_POST['parent_id_ultimate'] ) ? $_POST['parent_id_ultimate']  : '' );
	$ltr = ( isset( $_POST['ltr'] ) ? $_POST['ltr']  : '' );
	$show_if = ( isset( $_POST['show_only_if_course_finish'] ) ? $_POST['show_only_if_course_finish']  : '' );
	$quiz_score = ( isset( $_POST['quiz_score'] ) ? $_POST['quiz_score']  : '' );
	$quiz = ( isset( $_POST['quiz'] ) ? $_POST['quiz']  : '' );
	$quiz_id = ( isset( $_POST['quiz_id'] ) ? $_POST['quiz_id']  : '' );
	$text_under_lesson = ( isset( $_POST['text_under_lesson'] ) ? $_POST['text_under_lesson']  : '' );

	
		
	if($quiz_id != ""){
	update_post_meta( $post_id, '_quiz_id', $quiz_id );
	}
	else{
	delete_post_meta( $post_id, '_quiz_id');  
	}
	
	
	if($quiz != ""){
	update_post_meta( $post_id, '_quiz', $quiz );
	}
	else{
	delete_post_meta( $post_id, '_quiz');  
	}
	
	if($quiz_score != ""){
	update_post_meta( $post_id, '_quiz_score', $quiz_score );
	}
	else{
	delete_post_meta( $post_id, '_quiz_score');  
	}
	
	//mail("liranhecht@gmail.com","sdfdsF",$ltr );
	if($parent_id != ""){
	update_post_meta( $post_id, '_parent_id_ultimate', $parent_id );
	}
	else{
	delete_post_meta( $post_id, '_parent_id_ultimate');  
	}
	
		
	//mail("liranhecht@gmail.com","sdfdsF",$ltr );
	if($show_if != ""){
	update_post_meta( $post_id, '_show_only_if_course_finish', $show_if );
	}
	else{
	delete_post_meta( $post_id, '_show_only_if_course_finish');  
	}
	
	
	if($ltr != ""){
	update_post_meta( $post_id, '_ltr', $ltr );
	}
	else{
	delete_post_meta( $post_id, '_ltr');  
	}
	
	if($text_under_lesson != ""){
	update_post_meta( $post_id, '_text_under_lesson', $text_under_lesson );
	}
	else{
	delete_post_meta( $post_id, '_text_under_lesson');  
	}
	
	
	
	switch ($_POST['type_lesson']) {
    case 1:
		$video_url = ( isset( $_POST['video_lesson_url'] ) ? $_POST['video_lesson_url']  : '' );
        update_post_meta( $post_id, '_lesson_type', 1 );
		update_post_meta( $post_id, '_lesson_url', htmlentities($video_url) );
        break;
	case 2:
		$lesson_local_video = ( isset( $_POST['lesson_local_video'] ) ? $_POST['lesson_local_video']  : '' );
        update_post_meta( $post_id, '_lesson_type', 2 );
		update_post_meta( $post_id, '_lesson_local_video', htmlentities($lesson_local_video) );
        break;
    case 3:
		$pdf = ( isset( $_POST['pdf'] ) ? $_POST['pdf']  : '' );
        update_post_meta( $post_id, '_lesson_type', 3 );
		update_post_meta( $post_id, '_lesson_pdf', htmlentities($pdf) );
        break;
    case 4:
       $slideshow = ( isset( $_POST['slideshow'] ) ? $_POST['slideshow']  : '' );
        update_post_meta( $post_id, '_lesson_type', 4 );
		update_post_meta( $post_id, '_lesson_slideshow', htmlentities($slideshow) );
        break;
	case 5:
       $video_lesson_url_iplayer = ( isset( $_POST['video_lesson_url_iplayer'] ) ? $_POST['video_lesson_url_iplayer']  : '' );
        update_post_meta( $post_id, '_lesson_type', 5 );
		update_post_meta( $post_id, '_lesson_url_iplayer', $video_lesson_url_iplayer );
        break;
	case 6:
       $video_lesson_url_iplayer_playlist = ( isset( $_POST['video_lesson_url_iplayer_playlist'] ) ? $_POST['video_lesson_url_iplayer_playlist']  : '' );
        update_post_meta( $post_id, '_lesson_type', 6 );
		update_post_meta( $post_id, '_lesson_url_iplayer_playlist', $video_lesson_url_iplayer_playlist );
        break;	
		
		
}
	
}
  
}
add_action( 'save_post', 'ultimate_course_save_post_class_meta', 10, 2 );



 function ultimate_course_class_post_type($single_template) {
    global $wp_query, $post;
    if ($post->post_type == 'lesson_post_type'){
        $single_template = plugin_dir_path(__FILE__) . 'single.php';
    }//end if MY_CUSTOM_POST_TYPE
    return $single_template;
}

 
add_filter( 'single_template', 'ultimate_course_class_post_type' ) ;


add_action( 'init', 'ultimate_course_add_post_meta_boxes_class', 0 );


/* Create one or more meta boxes to be displayed on the post editor screen. */
function ultimate_course_add_post_meta_boxes_class() {

  add_meta_box(
    'ultimate-course-post-lesson-type',      // Unique ID
    __( 'Lesson Type', 'ultimate-course' ),    // Title
    'ultimate_course_course_lesson_type',   // Callback function
    array('lesson_post_type'),         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );
  
}

function ultimate_course_course_lesson_type(){
	global $post;
	$post_id = $post->ID;
	$lesson_type = "";
	if(get_post_meta( $post_id, '_lesson_type')){
		$lesson_type = get_post_meta( $post_id, '_lesson_type')[0];
	}
	
	
	
	?>
	
	<div class="bootstrap-iso">
	
	<div class="row">
		<div class="col-md-12">
		<div class="col-md-2">
		
		<label><input type="radio" value="1" name="type_lesson" class="type_lesson" <?php echo ($lesson_type == 1 ? "checked" : ""); ?> ><span class="radio_text"><?=__("YOUTUBE","ultimate-course")?></span></label>
		
		</div>
		<div class="col-md-2">
		
		  <label><input type="radio" value="2" name="type_lesson" class="type_lesson" <?php echo ($lesson_type == 2 ? "checked" : ""); ?>><span class="radio_text"><?=__("VIDEO","ultimate-course")?></span></label>
		
		</div>
		<div class="col-md-2">
		
		  <label><input type="radio" value="3" name="type_lesson" class="type_lesson" <?php echo ($lesson_type == 3 ? "checked" : ""); ?>><span class="radio_text"><?=__("PDF","ultimate-course")?></span></label>
		
		</div>
		<div class="col-md-3">
		
		 <label><input type="radio" value="4" name="type_lesson" class="type_lesson" <?php echo ($lesson_type == 4 ? "checked" : ""); ?>><span class="radio_text"><?=__("SLIDESHOW","ultimate-course")?></span></label>
		
		</div>
		<div class="col-md-2">
		
		 <label><input type="radio" value="5" name="type_lesson" class="type_lesson" <?php echo ($lesson_type == 5 ? "checked" : ""); ?>><span class="radio_text"><?=__("Iplayer","ultimate-course")?></span></label>
		
		</div>
		
			<div class="col-md-2">
		
		 <label><input type="radio" value="6" name="type_lesson" class="type_lesson" <?php echo ($lesson_type == 6 ? "checked" : ""); ?>><span class="radio_text"><?=__("Iplayer playlist","ultimate-course")?></span></label>
		
		</div>
		
		</div>
	</div>
<div class="box_holder">
	<div class="row">
	<div class="col-md-12 video_type type_lesson_box" style="display:<?php echo ($lesson_type == 1 ? "block" : "hidden"); ?>">
	<div class="form-group">
	<label for="video_lesson_url"><?=__("Please insert a youtube video ID","ultimate-course")?></label>
         <input type="text" class="form-control" name="video_lesson_url" value="<?php echo ($lesson_type == 1 && get_post_meta(get_the_id(),"_lesson_url") ? get_post_meta(get_the_id(),"_lesson_url")[0] : ""); ?>">     
	</div>
    </div>
	
	<div class="col-md-12 video_type_local type_lesson_box" style="display:<?php echo ($lesson_type == 2 ? "block" : "hidden"); ?>">
	<div class="form-group smartcat-uploader">
		<label for="lesson_local_video"><?=__("Please upload a video file","ultimate-course")?></label>
		<input type="text" class="form-control" name="lesson_local_video" value="<?php echo ($lesson_type == 2 && get_post_meta(get_the_id(),"_lesson_local_video") ? get_post_meta(get_the_id(),"_lesson_local_video")[0] : ""); ?>">
	</div>
	</div>
	<div class="col-md-12 pdf_type type_lesson_box" style="display:<?php echo ($lesson_type == 3 ? "block" : "hidden"); ?>">
          <div class="form-group smartcat-uploader">
		<label for="pdf"><?=__("Please upload a pdf file","ultimate-course")?></label>
		
	<input type="text" class="form-control" name="pdf" value="<?php echo ($lesson_type == 3 ? get_post_meta(get_the_id(),"_lesson_pdf")[0] : ""); ?>">
		</div>   
    </div>
	
	<div class="col-md-12 ppt_type type_lesson_box" style="display:<?php echo ($lesson_type == 4 ? "block" : "hidden"); ?>">
		<div class="form-group smartcat-uploader">
		<label for="slideshow"><?=__("Please upload a pptx file","ultimate-course")?></label>
		<input type="text" class="form-control" name="slideshow" value="<?php echo ($lesson_type == 4 ? get_post_meta(get_the_id(),"_lesson_slideshow")[0] : ""); ?>">
		</div>
    </div>
	
	<div class="col-md-12 iplayer_type type_lesson_box" style="display:<?php echo ($lesson_type == 5 ? "block" : "hidden"); ?>">
	<div class="form-group">
	<label for="video_lesson_url_iplayer"><?=__("Please insert a iplayer video ID","ultimate-course")?></label>
         <input type="text" class="form-control" name="video_lesson_url_iplayer" value="<?php echo ($lesson_type == 5 && get_post_meta(get_the_id(),"_lesson_url_iplayer") ? get_post_meta(get_the_id(),"_lesson_url_iplayer")[0] : ""); ?>">     
	</div>
    </div>
	
		<div class="col-md-12 iplayer_playlist_type type_lesson_box" style="display:<?php echo ($lesson_type == 6 ? "block" : "hidden"); ?>">
	<div class="form-group">
	<label for="video_lesson_url_iplayer_playlist"><?=__("Please insert a iplayer playlist video ID","ultimate-course")?></label>
         <input type="text" class="form-control" name="video_lesson_url_iplayer_playlist" value="<?php echo ($lesson_type == 6 && get_post_meta(get_the_id(),"_lesson_url_iplayer_playlist") ? get_post_meta(get_the_id(),"_lesson_url_iplayer_playlist")[0] : ""); ?>">     
	</div>
    </div>
	
	</div>
	</div>
	
	
	<div class="row">
		<h2>טקסט מתחת לוידאו</h2>
		<div class="col-md-12">
			<?php
			$content = ( get_post_meta(get_the_id(),"_text_under_lesson") ? get_post_meta(get_the_id(),"_text_under_lesson",true) : "");
			
			$editor_id = 'text_under_lesson';

			wp_editor( $content, $editor_id );
			?>
		</div>
	
	</div>
</div>
	<?php
}
	
	
	function removeLessonFromCourse($new_status, $old_status, $post){
		global $course;
		
		if($new_status === "trash"){
			if($course->getClassById($post->ID)){
			$course->removeClassById($post->ID);
			}
		}
		
		
	}
	add_action( 'transition_post_status', 'removeLessonFromCourse', 10, 3);


?>