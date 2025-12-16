<?php
/**
 * @version 1.6
 */
/*
Plugin Name: WP DIPLOMA
Description: This plugin helps create online diploma
Author: Liran Hecht
Version: 1.6
*/
function diploma_form($atts) {
	$a = shortcode_atts( array(
        'course_name' => '',
		'type' => '',
		'hour' => '',
		'type_bottom' => "",
		'lecturer_name' => "",
		'date_text' => "",
		'bg_extra' => ""
    ), $atts );
	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
		echo "<div class='alert alert-danger'>Please Connect</div>";
	} else {
		
		$form = '
		<div class="alert">Please notice that only english names are allowed</div>
		<form method="post" action="'. plugin_dir_url( __FILE__ ). 'fpdf/diploma.php" class="form-inline">

        <span style="width:200px;display:inline-block;">Course name:</span>
		<input  style="width:300px;" name="course_name" value="'  . $a["course_name"] . '" type="text">
        <br>
        
        <span style="width:200px;display:inline-block;">hour:</span>
		<input style="width:300px;" name="hour" value="'  . $a["hour"] . '" type="text">
        <br>
        
        <span style="width:200px;display:inline-block;">type:</span>
		<input style="width:300px;" name="type" value="'  . $a["type"] . '" type="text">
        <br>
        
        <span style="width:200px;display:inline-block;">Date text:</span>
		<input style="width:300px;" name="date_text" value="'  . $a["date_text"] . '" type="text">
        <br>
        
        <span style="width:200px;display:inline-block;">Lecturer name:</span>
		<input style="width:300px;" name="lecturer_name" value="'  . $a["lecturer_name"] . '" type="text">
        <br>

        <span style="width:200px;display:inline-block;">Lecturer name 2:</span>
		<input style="width:300px;" name="lecturer_name_2" value="'  . $a["lecturer_name_2"] . '" type="text">
        <br>
        
        <span style="width:200px;display:inline-block;">Type bottom:</span>
		<input style="width:300px;" name="type_bottom" value="'  . $a["type_bottom"] . '" type="text">
        <br>
        
		<input name="bg_extra" value="'  . $a["bg_extra"] . '" type="hidden">
        
		<span style="width:200px;display:inline-block;">First name:</span>
		<input style="width:300px;" type="text" name="firstname" value="' . $current_user->first_name . '">
		<br>
		<span style="width:200px;display:inline-block;">Last name:</span>
		<input style="width:300px;" type="text" name="lastname" value="' . $current_user->last_name . '">
		

        <br>
		<span style="width:200px;display:inline-block;">color:</span>
		<input type="color" name="color">
		<br><br>


		<input type="submit" value="Submit">
		</form>
		<hr>
		<form method="post" action="'. plugin_dir_url( __FILE__ ). 'fpdf/diploma.php" class="form-inline" enctype="multipart/form-data">
			
        <input type="file" name="file_csv">
        

        <div>
            <input type="hidden" value="1" name="multi">
            <input id="d_file" type="radio" name="multi_t" value="file" checked>
            <label for="d_file">קבצים מופרדים</label>
            
            <input id="d_files" type="radio" name="multi_t" value="files">
            <label for="d_files">קובץ מרובה עמודים</label>

            <br>
            <span style="width:200px;display:inline-block;">color:</span>
            <input type="color" name="color">

        </div>
        <br>
			<button type="submit">Submit</button>
			
		</form>


		
		';
		
		
	return $form;
		
	}
}
add_shortcode( 'diploma_form', 'diploma_form' );


