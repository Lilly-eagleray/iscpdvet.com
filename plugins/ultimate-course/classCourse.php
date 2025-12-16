<?php
class Course
{

 public $db;
 public $user;
 public $table_cours_list;
 public $table_cours_to_user;
 public $table_post_to_course;
 public $course_levels;

 function __construct(){
        global $wpdb;

        $this->db = $wpdb;
		$this->table_cours_list = $this->db->prefix . 'course_list';
		$this->table_cours_to_user =  $this->db->prefix . 'course_to_user';
		$this->table_post_to_course =  $this->db->prefix . 'post_to_course';
		$this->table_score_test =  $this->db->prefix .  'mlw_results';
		$this->course_levels= array("en" => array(
		"beginners","intermediate","expert"
		) ,
		"he"=>array(
		"מתחילים","מתקדמים","מומחים"
		));
    }
	public function getLanguge(){
		if(function_exists('icl_object_id')){
		global $sitepress;
		// $current_lang = $sitepress->get_current_language();
		$current_lang = apply_filters( 'wpml_current_language', NULL );
		}
		else{
			$pieces = explode("_", get_locale());
			$current_lang = $pieces[0];
		}
		return $current_lang;
	}
	public function getMainIdWpml($course_id){
		 $my_default_lang = apply_filters('wpml_default_language', NULL );
		 return icl_object_id($course_id, 'course_post_type', true, $my_default_lang);
	}
	public function returnCurrentLangPost($post_id){
		// global $sitepress;
		// $current_lang = $sitepress->get_current_language();
		// $current_lang = apply_filters( 'wpml_current_language', NULL );
		// return icl_object_id($post_id, 'post', true, $current_lang);
		return $post_id;
	}


	public function returnPostByLang($post_id,$lang){
		global $sitepress;
		// return icl_object_id($post_id, 'post', true, $lang);
		return $post_id;
	}

	public function getAllLangugesOnSystem(){
		$languages = icl_get_languages('skip_missing=0&orderby=code');
		return $languages;
	}

  public function returnCourseLevels(){
	  return $this->course_levels;
  }
  public function setUser($user,$post_id = ""){
	  $this->user = $user;
  }
  public function userWatchedMe($lesson_id){
	   if(!$this->user->ID)
		  return;
	  //delete_user_meta($this->user->id,"_watched_lesson");
	  //echo $lesson_id;
	  $all_lessons_watched = get_user_meta($this->user->ID,"_watched_lesson",true);
      if( !is_array( $all_lessons_watched ) ){
        $all_lessons_watched = [];
      }
	  if(!in_array($lesson_id,$all_lessons_watched)){
		  $all_lessons_watched[] = $lesson_id;
		  update_user_meta($this->user->ID,"_watched_lesson",$all_lessons_watched);
	  }

	  //print_r(get_user_meta($this->user->id,"_watched_lesson"));

  }
    public function getCurrentUserLatestScroe($quiz_id){

	 if(!$this->user->ID)
		  return;

	 return $this->db->get_row( "SELECT `correct_score` FROM {$this->table_score_test} WHERE `user`={$this->user->ID} AND `quiz_id` = {$quiz_id} ORDER BY `correct_score` DESC"  );


	}



	public function didUserWatchedAllLessons($course_id, $current_class) {
		$all_lessons_watched = get_user_meta($this->user->ID, "_watched_lesson", true);
		$all_lesson_in = $this->getAllPostOfCourseAutoLang($course_id);

		$all_lesson_in_course = []; // Initialize as an array
		foreach ($all_lesson_in as $_lesson) {
			if ($current_class != $_lesson->post_id) {
				$all_lesson_in_course[] = $_lesson->post_id;
			}
		}

		// Ensure both are arrays before passing to array_diff
		$all_lessons_watched = is_array($all_lessons_watched) ? $all_lessons_watched : [];
		return count(array_diff($all_lesson_in_course, $all_lessons_watched));
	}
  public function addPostToCourse($post_id,$courseId,$lang,$status){
	  return $this->db->insert(
				$this->table_post_to_course,
				array(
					'post_id' => $post_id,
					'course_id' => $courseId,
					'lang'=>$lang,
					'status'=>$status
				)
			);
  }
   public function updatePostToCourse($post_id,$courseId,$postToUpdate,$status){

    return $this->db->update(
				$this->table_post_to_course,
				array(
					'post_id' => $post_id,
					'course_id' => $courseId,
					'status' => $status
				),
				array( 'ID' => $postToUpdate)
				);
   }
 public function getCourseByPostId($post_id){
	 $courses = $this->db->get_row( "SELECT * FROM {$this->table_post_to_course} WHERE `post_id`={$post_id}"  );
	 return $courses;
 }
  public function getAllCourses()
  {

		$args = array( 'post_type' =>  'course_post_type','numberposts' => -1,"suppress_filters"=>0 );
		return get_posts( $args );

  }

   public function getCourseById($course_id)
  {
		if ( function_exists('icl_object_id') ) {
		return get_post($this->returnCurrentLangPost($course_id));
		}
		else{
			return get_post($course_id);
		}

  }
  public function getLecturers(){
	 return  get_posts( array(
		'post_type' => 'lecturer_post_type',
		'numberposts' => -1,
		'suppress_filters'=>0
		));
  }
   public function getClassById($lesson_id)
  {
	return $this->db->get_row( "SELECT * FROM {$this->table_post_to_course} WHERE `post_id`={$lesson_id}"  );
  }

    public function removeClassById($lesson_id)
  {
		return $this->db->delete( $this->table_post_to_course, array( 'post_id' => $lesson_id) );
  }

   public function getAllCoursesByUser()
  {
      if(!$this->user->ID)
		  return;

	 return $this->db->get_results( "SELECT * FROM {$this->table_cours_to_user} WHERE `user_id`={$this->user->ID}"  );

  }
   public function getAllPostOfCourse($courseId,$lang)
  {

      	return $this->db->get_results( "SELECT * FROM {$this->table_post_to_course} WHERE `course_id`={$courseId} AND `lang`='{$lang}' ORDER BY `weight`"  );

  }
   public function getAllPostOfCourseAutoLang($courseId)
  {

      	return $this->db->get_results( "SELECT `post_id` FROM {$this->table_post_to_course} WHERE `course_id`={$courseId} ORDER BY `weight`"  );

  }
  public function isExistPostInCourse($postId,$courseId)
  {
      	return $this->db->get_row( "SELECT * FROM {$this->table_post_to_course} WHERE `post_id`={$postId} AND `course_id`={$courseId}"  );

  }

  public function isPublish($postId)
  {
      	return $this->db->get_row( "SELECT `status` FROM {$this->table_post_to_course} WHERE `post_id`={$postId}");

  }


   public function updateCourseOrder($order,$id,$is_wpml)
  {


  if ( function_exists('icl_object_id') && $is_wpml == 'true' ) {
	$all_langs = $this->getAllLangugesOnSystem();

	foreach($all_langs AS $key => $value){
			$this->db->update(
			$this->table_post_to_course,
			array(
				"weight"=>$order
			),
			array( 'post_id' => $this->returnPostByLang($id,$key) )
		);
	}


  }
  else{
	  return $this->db->update(
			$this->table_post_to_course,
			array(
				"weight"=>$order
			),
			array( 'post_id' => $id )
		);
  }


  }


  public function textSuccses($text){
	     return '<div class="notice notice-success is-dismissible">' . $text . '</div>';

  }

  public function getCoursePerUser($course_id){
  if(!$this->user->ID)
		  return;

	if ( function_exists('icl_object_id') ) {
	 $course_id = $this->getMainIdWpml($course_id);
	}
	 return $this->db->get_results( "SELECT * FROM {$this->table_cours_to_user} WHERE `user_id`={$this->user->ID} AND `course_id`={$course_id}"  );
  }

  public function addCourseToUser($course_id,$date = ""){
		if(!$this->user->ID)
		  return;

    if ( function_exists('icl_object_id') ) {
	 $main_id = $this->getMainIdWpml($course_id);
	}
	if($date == ""){
		$date = date('Y-m-d');
		echo $date;
	}

		$this->db->insert(
					$this->table_cours_to_user,
					array(
						'user_id' => $this->user->ID,
						'course_id' => $course_id,
						'time' => $date
					)
		);
		$lastid = $this->db->insert_id;
		return $lastid;
  }
  public function updateCourseToUserDate($course_id,$user_id,$date){

    if ( function_exists('icl_object_id') ) {
	 $main_id = $this->getMainIdWpml($course_id);
	}

		$res = $this->db->update(
					$this->table_cours_to_user,
					array(
						'time' => $date,
					),
					array( 'user_id' => $user_id , 'course_id' => $course_id)
		);

		return $res;
  }

   public function addCourseToUserById($course_id,$user_id,$date = ""){
		if(!$user_id)
		  return;
	if ( function_exists('icl_object_id') ) {
	 $course_id = $this->getMainIdWpml($course_id);
	}
	if($date == ""){
		$date = date('Y-m-d');
	}

		$this->db->insert(
					$this->table_cours_to_user,
					array(
						'user_id' => $user_id,
						'course_id' => $course_id,
						'time' => $date

					)
		);
		$lastid = $this->db->insert_id;
		return $lastid;
  }
   public function delCourseToUserById($course_id,$user_id){
		if(!$user_id)
		  return;
	if ( function_exists('icl_object_id') ) {
	 $course_id = $this->getMainIdWpml($course_id);
	}
		return $this->db->delete( $this->table_cours_to_user, array( 'user_id' => $user_id,"course_id" => $course_id) );
  }


  public function delCourseToUserByIdMulti($course_id,$user_id_array){
		if(empty($user_id_array))
		  return;


	foreach($user_id_array AS $_user){

	    if ( function_exists('icl_object_id') ) {
			$course_id = $this->getMainIdWpml($course_id);
		}

		 $this->db->delete( $this->table_cours_to_user, array( 'user_id' => $_user->ID,"course_id" => $course_id) );
	}

	return true;

  }

   public function getAllRegisterdUsers(){

	 return $this->db->get_results( "SELECT DISTINCT(user_id) AS user_id FROM {$this->table_cours_to_user}"  );

  }
  public function getAllRegisterdUsersToCourse($course_id){

	 return $this->db->get_results( "SELECT DISTINCT(user_id) AS user_id FROM {$this->table_cours_to_user} WHERE `course_id` = {$course_id}"  );

  }
   public function getCoursesPerUser($user_id){

	 return $this->db->get_results( "SELECT * FROM {$this->table_cours_to_user} WHERE `user_id`={$user_id}"  );

  }
public function getRegisterationDate($user_id, $course_id){

	 $date = $this->db->get_row( "SELECT * FROM {$this->table_cours_to_user} WHERE `user_id`={$user_id} AND `course_id`={$course_id}" );
	
    if ( $date ) {
        return $date->time;
    }
    
    return null;
}  
  
//    public function isUserSubscripationValid($user_id,$course_id){

// 	 $date = $this->db->get_row( "SELECT * FROM {$this->table_cours_to_user} WHERE `user_id`={$user_id} AND `course_id`={$course_id}"  );
// 	 if($date->time != ""){
// 		$date1 = new DateTime($date->time);
// 		$date2 = new DateTime('now');

// 		$difference = $date1->diff($date2);
// 		//difference between two dates
// 		return $difference->days;

// 	 }
// 	 else{
// 		 return "-1";
// 	 }




//   }

  public function isUserSubscripationValid($user_id, $course_id) {
    // Fetch the subscription data for the user and course
    $date = $this->db->get_row("SELECT * FROM {$this->table_cours_to_user} WHERE `user_id`={$user_id} AND `course_id`={$course_id}");

    // Check if data is found and if the `time` property is set
    if ($date && !empty($date->time)) {
        $date1 = new DateTime($date->time);
        $date2 = new DateTime('now');

        // Calculate the difference in days between the two dates
        $difference = $date1->diff($date2);
        return $difference->days;
    } else {
        // Return "-1" if no subscription date is found
        return "-1";
    }
}


  public function isParent($course_id){

	$args = array(
			'post_type' => array('page','post','lesson_post_type'),
				'meta_query' => array(
				array(
				'key' => '_parent_id_ultimate',
				'value' => $course_id,
				)
			),
			'orderby'          => 'menu_order',
			'order'            => 'DESC',
			);
	return get_posts( $args );
  }

}
?>
