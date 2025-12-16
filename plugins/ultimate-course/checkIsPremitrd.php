<?php
register_activation_hook(__FILE__, 'daily_check_is_premited_activate');

function daily_check_is_premited_activate() {
    if (! wp_next_scheduled ( 'daily_check_is_premited' )) {
	wp_schedule_event(time(), 'daily', 'daily_check_is_premited');
    }
}

add_action('daily_check_is_premited', 'checkIsPremited');

function checkIsPremited() {
	global $course;
	
	$all_courses = $course->getAllCourses();
	//print_r($all_courses);
	
	foreach($all_courses AS $course_in){
		
		if(get_post_meta($course_in->ID,"_isYearly") ){
			
			$all_users = $course->getAllRegisterdUsersToCourse($course_in->ID);
		
				foreach($all_users AS $user){
					
					$days = $course->isUserSubscripationValid($user->user_id,$course_in->ID);
					
					$user_obj = get_user_by('id', $user->user_id);
					if($days == 358){
						$template = file_get_contents(plugin_dir_path( __FILE__ ) .'mail/' . $course_in->ID . ".html");
						$headers = array('Content-Type: text/html; charset=UTF-8');
 
						wp_mail( $user_obj->user_email , "המנוי שלך עומד להסתיים" , $template, $headers );
					}
					if($days > 365){
						$course->delCourseToUserById($course_in->ID,$user->user_id);
					}
					
				}
		}
		
	}
	
	
}





function aptcron_register_check_status(){
    add_menu_page( 
        __( 'בדיקת תאריכי הרשמה', 'textdomain' ),
        'בדיקת תאריכי הרשמה',
        'administrator',
        'run_cron_manualy.php',
        'run_cron_manualy',
        "",
        6
    ); 
}
add_action( 'admin_menu', 'aptcron_register_check_status' );
 
/**
 * Display a custom menu page
 */
function run_cron_manualy(){
	
	
	do_action('daily_check_is_premited');

	
}

?>