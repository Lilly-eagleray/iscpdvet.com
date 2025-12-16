<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header();
global $course;
$course_id = $course->getCourseByPostId($post->ID);


?>
<div id="main" class="site-main">
<div class="inside-article">
<?php
  if($course_id){
	  $current_user = wp_get_current_user();
	  $course->setUser($current_user);
	  $course->userWatchedMe($post->ID);
	  $is_premited = $course->getCoursePerUser($course_id->course_id);
	  
	  if($is_premited || $course->isPublish($post->ID)->status){
		  
		
	
$course_id = $course->getCourseByPostId(get_the_id());

$lesson_list = $course->getAllPostOfCourse($course_id->course_id,$course->getLanguge());
while ( have_posts() ) : the_post();
?>

<div class="col-md-12">
<h1><?=get_the_title()?></h1>
</div>
<div class="col-md-9">
		<main id="main" class="site-main" role="main">

	<?php	
	if(get_post_meta(get_the_id(),"_lesson_type")){
		if(get_post_meta(get_the_id(),"_lesson_type")[0] == 1){
			echo '<div class="embed-responsive embed-responsive-4by3">
				<iframe class="embed-responsive-item" src="//www.youtube.com/embed/' . html_entity_decode(get_post_meta(get_the_id(),"_lesson_url")[0]) . '?rel=0&showinfo=0"></iframe>
			</div>';
		}
		if(get_post_meta(get_the_id(),"_lesson_type")[0] == 2){
			echo '<div align="center" class="embed-responsive embed-responsive-16by9">
				<video controls class="embed-responsive-item">
					<source src="'. html_entity_decode(get_post_meta(get_the_id(),"_lesson_local_video")[0]) .'" type="video/mp4">
				</video>
			</div>';
		}
		if(get_post_meta(get_the_id(),"_lesson_type")[0] == 3){
			echo '<iframe src="https://docs.google.com/gview?url=' . html_entity_decode(get_post_meta(get_the_id(),"_lesson_pdf")[0]) . '&embedded=true&toolbar=hide" style="width:100%; height:600px;" frameborder="0"></iframe>';
		}
		if(get_post_meta(get_the_id(),"_lesson_type")[0] == 4){
			echo '<iframe src="https://docs.google.com/gview?url=' . html_entity_decode(get_post_meta(get_the_id(),"_lesson_slideshow")[0]) . '&embedded=true&toolbar=hide" style="width:100%; height:400px;" frameborder="0"></iframe>';
		}
		if(get_post_meta(get_the_id(),"_lesson_type")[0] == 5){
			echo '<div class="embed-responsive embed-responsive-4by3">
			<iframe class="embed-responsive-item" src="//iplayerhd.com/player/video/' . html_entity_decode(get_post_meta(get_the_id(),"_lesson_url_iplayer")[0]) . '?cbartype=auto" allowtransparency="true" frameborder="0" scrolling="no"  allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen></iframe>
			</div>';
		
		}
		if(get_post_meta(get_the_id(),"_lesson_type")[0] == 6){
		echo '<div class="embed-responsive embed-responsive-4by3">
			<iframe class="embed-responsive-item" src="//iplayerhd.com/player/playlist/' . html_entity_decode(get_post_meta(get_the_id(),"_lesson_url_iplayer_playlist")[0]) . '?playlist=bottom" allowtransparency="true" frameborder="0" scrolling="no"  allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen></iframe>
			</div>';
		
		}
	}	
		

		endwhile; // End of the loop. ?>
<?php
$course_info = $course->getCourseById($course_id->course_id);

?>
		</main><!-- #main -->
		<div class="under_video_text">
			<?php
			echo ( get_post_meta(get_the_id(),"_text_under_lesson") ? get_post_meta(get_the_id(),"_text_under_lesson",true) : "");
			?>
		</div>
</div>
<div class="col-md-3">

	<?php
	
	if(get_post_meta($course_info->ID,"_course_teacher")[0]){ ?>
   <div class="box_level box_teacher_course">
   <p class="title_box_side"><?=__("Lecturer","ultimate-course")?></p>	
		<?php
		$teacher = get_post_meta($course_info->ID,"_course_teacher")[0];
		foreach($teacher AS $teacher_in){
		$teacher_post = get_post($teacher_in);
		?>
		<div class="box_lecturer">
		<a class="inner_lecturer" href="<?=get_post_permalink($teacher_in)?>">
		<?php
		echo $teacher_post->post_title;
		?>
	
		</a>
		</div>
		
		<?php } ?>
   </div>
	<?php } ?>
	
	<!--<?php if(get_post_meta($course_info->ID,"_course_level")){ ?>
   <div class="box_level box_level_course">
   <p class="title_box_side"><?=__("Course level","ultimate-course")?></p>
		<?=get_post_meta($course_info->ID,"_course_level")[0]?>	
   </div>
   <?php } ?>
   !-->
   <?php if(get_post_meta($course_info->ID,"_forum_id")){ ?>
   <div class="box_level box_forum_course">
		<a href="<?=get_permalink(get_post_meta($course_info->ID,"_forum_id")[0])?>">פורום - <?=get_the_title($course_info->ID)?></a>	
   </div>
   <?php } ?>
   
</div>

<div class="row">
<div class="col-md-12">

 <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#lesson_list"><?=__("Course lessons","ultimate-course")?></a></li>
    <li><a data-toggle="tab" href="#summery"><?=__("Lesson summery","ultimate-course")?></a></li>
    <!--<li><a data-toggle="tab" href="#qa"><?=__("Q&A","ultimate-course")?></a></li> 
	<li><a data-toggle="tab" href="#files"><?=__("Download","ultimate-course")?></a></li>!-->
  </ul>

  <div class="tab-content">
		<div id="lesson_list" class="tab-pane fade in active">

		<h2 class="side-title"><?=__("Course lessons","ultimate-course")?></h2>
		<ul class="list-unstyled">
		<?php
		foreach($lesson_list AS $lesson){
		?>	

		<li><a class="<?php echo ($lesson->post_id == get_the_id() ? "choosen" : ""); ?>" href="<?=get_post_permalink($lesson->post_id)?>" title="<?=get_post_permalink($lesson->post_id)?>"><i class="fa fa-book"></i> <?php echo get_the_title($lesson->post_id); ?></a></li>

		<?php

		}
		?>
					
		</ul>
	  
     
    </div>
    <div id="summery" class="tab-pane fade">
	
      <?=get_the_content()?>
    </div>
    <div id="qa" class="tab-pane fade">
    
    </div>
 <div id="files" class="tab-pane fade">
	<?=get_the_ID()?>
 </div>
  </div>
</div>

</div>
</div>


<?php
}
else{
?>

<div class="alert alert-danger text-center">
<?=__("You have no permission to access","ultimate-course")?> "<?=get_the_title()?>"
<br>

<?php
$id_course = "";

	$course_id = $course->getCourseByPostId(get_the_id());
	$course_in = $course->returnCurrentLangPost($course_id->course_id);
	$args = array(
	   'post_type' => 'product',
	   'meta_query' => array(
		   array(
			   'key' => '_course_related',
			   'value' => $course_in,
		   )
	   )
	);
	$query = new WP_Query($args);
	$course_to_redirect = $query->posts;
			if($course_to_redirect){
				
				$link = get_permalink( $course_to_redirect[0]->ID );
			}
			else{
				$link = get_permalink( woocommerce_get_page_id( 'shop' ) );
			}
		
	
?>
<a href="<?=$link?>"><?=__("But you can join the course very easily","ultimate-course")?></a>
</div>


<?php
}

}
?>
</div>
</div>
<?php
get_footer();