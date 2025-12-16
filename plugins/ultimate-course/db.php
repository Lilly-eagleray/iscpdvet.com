<?php
global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

$table_course = $wpdb->prefix . "course_list"; 

$sql = "CREATE TABLE $table_course (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  course_name_he text NOT NULL,
  course_name_en text NOT NULL,
  course_name_teacher_he text NOT NULL,
  course_name_teacher_en text NOT NULL,
  course_level_en text NOT NULL,
  course_level_he text NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );


$table_course = $wpdb->prefix . "lessons_watched"; 

$sql = "CREATE TABLE $table_course (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  post_id mediumint(11) NOT NULL,
  date text NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );


$table_course_to_user = $wpdb->prefix . "course_to_user"; 

$sql = "CREATE TABLE $table_course_to_user (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  user_id mediumint(9),
  course_id mediumint(9),
  time date DEFAULT '0000-00-00' NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );


$table_post_to_course = $wpdb->prefix . "post_to_course"; 

$sql = "CREATE TABLE $table_post_to_course (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  post_id mediumint(9),
  course_id mediumint(9),
  lang text NOT NULL,
  weight mediumint(9),
  status mediumint(9),
  PRIMARY KEY  (id),
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

?>