<?php
/** Step 2 (from text above). */
add_action( 'admin_menu', 'drerez_admin' );
/** Step 1. */
function drerez_admin() {
	add_menu_page( 'הגדרות קורסים', 'הגדרות קורסים', 'manage_options', 'course-drerez-page', 'course_drerez',"",30 );
	add_submenu_page(  'course-drerez-page', 'Add course', 'Add course', 'manage_options','post-new.php?post_type=course_post_type');
	add_submenu_page(  'course-drerez-page', 'Add lesson', 'Add lesson', 'manage_options','post-new.php?post_type=lesson_post_type');
	add_submenu_page(  'course-drerez-page', 'Add lecturer', 'Add lecturer', 'manage_options','post-new.php?post_type=lecturer_post_type');
}
/** Step 3. */
function course_drerez() {	
	global $course;
	$user = wp_get_current_user();
	$action = "add";
	$button_text = "הוסף קורס";
	$courseId="";
	$course_name_he="";
	$course_name_en="";
	$course_name_teacher_he = "";
	$course_name_teacher_en = "";
	$course_level_he = "";
	$course_level_en = "";
	$course_info = "";
	$courses = $course->getAllCourses();


	if(isset($_POST["add_user_to_course"]) && $_POST["add_user_to_course"] == "1"){
		
        
        $users = $_POST["user_id"];
		foreach ( $users as $key => $user ) {
		    $action_id = $course->addCourseToUserById($_POST["course_id"], $user );
            if($action_id){
                echo $course->textSuccses( __("Course added to user " . $user ,"drerez-trans") );
            }
        }
        
		
	}
	else if(isset($_POST["del_course_from_user"]) && $_POST["del_course_from_user"] == "1"){
	
		$action_id = $course->delCourseToUserById($_POST["course_id"],$_POST["user_id"]);
		if($action_id){
			echo $course->textSuccses( __("Course deleted successfully","drerez-trans") );
		}
		
	}
	else if(isset($_POST["update_date"]) && $_POST["update_date"] == "1"){
		$action_id = $course->updateCourseToUserDate($_POST["course_id"],$_POST["user_id"],$_POST["register_date"]);	
		if($action_id){
			echo $course->textSuccses( __("Course date updated successfully","drerez-trans") );
		}
		
	}
	else if(isset($_POST["del_multi_user"]) && $_POST["del_multi_user"] == "1"){
		
		$action_id = $course->delCourseToUserByIdMulti($_POST["course_id"],$_POST["user_to_del"]);	
		if($action_id){
			echo $course->textSuccses( __("Multi users updated successfully","drerez-trans") );
		}
	}
	
	
?>
<?php 
if(isset($_GET["tab"])){
$whiche = $_GET["tab"];
?>
<script>
jQuery(document).ready(function( $ ) {

	
		$( ".box_admin" ).removeClass("active");
		$(".nav-tabs").find('[data-box="<?=$whiche?>"]').addClass("active");
		$(".box_big").hide();
		$("#<?=$whiche?>").show();
	});
</script>
<?php
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="bootstrap-iso">
<ul class="nav nav-tabs">
  <li class="box_admin" data-box="second_part" class="active"><a href="#">ניהול הרשמות לקורסים</a></li>
  <li class="box_admin" data-box="third_part"><a href="#">סדר שיעורים בקורס</a></li>
  <li class="box_admin" data-box="fourth_part"><a href="#">מחק משתמשים</a></li>
</ul>


	<div id="second_part" class="box_big">
	<?php
	$users = $course->getAllRegisterdUsers();
	?>
	<div class="container">
		<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" class="form-inline">
		<input type="hidden" name="add_user_to_course" value="1">
	   <select name="user_id[]" class="user_id select-t" multiple="multiple">
	   <option value="0">בחר משתמש</option>
	   <?php
	   $blogusers = get_users();
	   foreach($blogusers AS $blogusers_in){
		?> 
		  
		<option value="<?=$blogusers_in->ID?>"><?=$blogusers_in->display_name?> -- <?=$blogusers_in->user_email?></option>
		  
	   <?php
	   }
	   ?>
	   </select>
	   
	   <select name="course_id" class="form-control">
	   <option value="0">בחר קורס</option>
	   <?php
	   foreach($courses AS $course_in){
		?> 
		  
		<option value="<?=$course_in->ID?>"><?=$course_in->post_title?></option>
		  
		<?php
	   }
	   ?>
	   </select>
	   <button type="submit" class="btn btn-info">הוסף לקורס</button>
	   </form>
	</div>
		 <table class="table table-striped user-table-course">
    <thead>
      <tr>
		<th></th>
        <th>שם משתמש</th>
        <th>כתובת מייל</th>
        <th>רשום לקורסים</th>
      </tr>
    </thead>
    <tbody>
      <?php 
	   foreach($users AS $user_in){ 
	
	  $course_list_text = "";
	  $course_list_text = "<ul>";
	  $user_profile = get_user_by('id', $user_in->user_id);
	     
	  $course_list_per_user = $course->getCoursesPerUser($user_in->user_id);
	  foreach($course_list_per_user AS $course_in){
		//fix del users
		if(!$user_profile){
			$course->delCourseToUserById($course_in->course_id,$user_in->user_id);
		 }
		$form_del = '<form method="post" action="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '" class="form-inline form_del_course">
		<input type="hidden" name="del_course_from_user" value="1">
		<input type="hidden" name="course_id" value="' . $course_in->course_id . '">	
		<input type="hidden" name="user_id" value="' . $course_in->user_id . '">		
		<button type="submit" class="submit_del">מחק משתמש</button></form>
		';
		$form_date = "";
		if($course_in->course_id == "743"){
			$createDate = new DateTime($course_in->time);
			$strip = $createDate->format('Y-m-d');
		$form_date = '<form method="post" action="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '" class="form-inline form_del_course">
		<input type="hidden" name="update_date" value="1">
		<input type="hidden" name="course_id" value="' . $course_in->course_id . '">	
		<input type="hidden" name="user_id" value="' . $course_in->user_id . '">	
		<input class="club_date" type="date" name="register_date" value="' . $strip  . '">			
		<button type="submit" class="submit_del">עדכן תאריך</button></form>
		';

		}  
	  $course_list_text .= "<li><div class='text_course'>" . $course->getCourseById($course_in->course_id)->post_title . " | </div>" .  $form_del . $form_date;	
	  $course_list_text .= "</li>";
		 
	  
	   
	   }
	   $course_list_text .= "</ul>";
	  ?>
				
		<tr>
		<td></td>
		<td>
		<?=$user_profile->user_login?>
		</td>
		<td>
		<a target="_blank" href="mailto:<?=$user_profile->user_email?>">
		<?=$user_profile->user_email?>
		</a>
		</td>
		<td>
		<?=$course_list_text?>
		</td>
		</tr>
	 <?php } ?>
	 
    </tbody>
  </table>
	
	
	
	</div>
	
	
	
	<div id="third_part" class="box_big">
	
	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&tab=third_part" class="form-inline">
		<input type="hidden" name="sourse_order" value="1">	   
	   <select name="course_id_order" class="form-control">
	   <option value="0">בחר קורס</option>
	   <?php
	   foreach($courses AS $course_in){
		
		?> 
		  
		<option value="<?=$course_in->ID?>"><?=$course_in->post_title?></option>
		  
		<?php
	   }
	   ?>
	   </select>
	   <button type="submit" class="btn btn-info">בחר קורס</button>
	   </form>
	   <div class="massage_checkbox">
	   <!-- <label><input type="checkbox" name="update_all_lang" class="update_all_lang"><span class="radio_text"><?=__("Update order in all languges","ultimate-course")?></span></label> !-->
	   </div>

	<ul id="sortable">
	
	<?php 
	if(isset($_POST["course_id_order"])){
		$id = $_POST["course_id_order"];
	
	$course_list_per_user = $course->getAllPostOfCourse($id,ICL_LANGUAGE_CODE);
	foreach($course_list_per_user AS $course_in){	
	?>
	
	<li id="<?=$course_in->post_id?>" class="box_lesson"><?php echo esc_html( get_the_title($course_in->post_id) ); ?></li>
	
	<?php
	}
	?>
	</ul>
	
	<?php
	}
	?>
	
	</div>	
	<div id="fourth_part" class="box_big">
		<form method="post" action="<?=str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])?>" class="form-inline form_del_multi_course" onsubmit="return confirm('אתה בטוח רוצה למחוק ?');">
		<input name="del_multi_user" value="1" type="hidden">
		<input name="course_id" value="743" type="hidden">
		<button type="submit" class="btn btn-danger">מחק משתמשים</button>
			<table class="table table-striped table-sm" style="max-width:400px">
			<thead>
			<tr>
				<th>
				
				</th>
				<th>
				#
				</th>
				<th>
				כתובת מייל
				</th>
				<th>
				תאריך רישום
				</th>
			</tr>
			</thead>
			
			<tbody>
			<?php
			$users = $course->getAllRegisterdUsersToCourse(743);
			foreach($users AS $key => $_user){
			$user_profile = get_user_by('id', $_user->user_id);
			$reg_date =  $course->getRegisterationDate($_user->user_id,743);
			$createDate = new DateTime($reg_date);
			$strip = $createDate->format('d-m-Y');
			?>
			<tr>
				<td>
				<?=$key?>
				</td>
				<td>
				<input type="checkbox" name="user_to_del[]" value="<?=$_user->user_id?>">
				</td>
				<td>
				<?=$user_profile->user_email?>
				</td>
				<td>
				<?=$strip?>
				</td>
			
			</tr>
			
			<?php
			}
			?>
			</tbody>
			</table>
		</form>
	</div>
	</div>	
<?php
}
?>