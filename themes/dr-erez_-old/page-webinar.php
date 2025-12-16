<?php
/* Template Name: webinar */
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package GeneratePress
 */
 
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

	<div id="primary" <?php generate_content_class();?>>
		<main id="main" <?php generate_main_class(); ?>>
			<?php do_action('generate_before_main_content'); ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<?php echo get_field("above_webinar"); ?>
			<?php
			
				$course_id = $course->getCourseByPostId($post->ID);
				 $current_user = wp_get_current_user();
				  $course->setUser($current_user);
				  $is_premited = $course->getCoursePerUser($course_id->course_id);
				 
				  if($is_premited || $course->isPublish($post->ID)->status){
				 ?>
				<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js">
				</script>
				<script type="text/javascript">
				swfobject.embedSWF(
				"http://www.hotconference.com/conference,light3", "conference", "100%", "600", "11.7.0",
				"http://www.hotconference.com/conference,installer", {
				room: 98024149,
				fontSize: 12,
				fontColor: "#000000",
				fontFamily: "Verdana",
				backgroundColor: "0xEEEEEE"
				}, { allowfullscreen: "true" }
				);
				</script>
				<div id="conference" style="text-align:center"><!-- write description in case guest does not have adobe flash player installed 
				
				-->
				<h1>נא להתקין נגן פלאש</h1>
				<a href="https://get.adobe.com/flashplayer/" target="_blank">להורדה לחץ כאן</a>
				</div>
				<?php
				 }
				  else{
					  
				  $content = '<div class="alert alert-danger text-center">
				' . __("You have no permission to access","ultimate-course") . get_the_title()
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
				echo $content;	  
			}
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) : ?>
					<div class="comments-area">
						<?php comments_template(); ?>
					</div>
				<?php endif; ?>

			<?php endwhile; // end of the loop. ?>
			<?php do_action('generate_after_main_content'); ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php 
do_action('generate_sidebars');
get_footer();