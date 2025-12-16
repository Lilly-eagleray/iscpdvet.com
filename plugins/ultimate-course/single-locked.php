<?php
/**
 * The Template for displaying all single posts.
 *
 * @package GeneratePress
 */
 
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); 
?>
<div id="main" class="site-main">
<div class="inside-article">
<?php
global $course;
$course_id = $course->getCourseByPostId($post->ID);

  if($course_id->course_id){
	  
	  $current_user = wp_get_current_user();
	  $course->setUser($current_user);
	  $is_premited = $course->getCoursePerUser($course_id->course_id);
	 
	  if($is_premited || $course->isPublish($post->ID)->status){
		

?>

	
		<?php do_action('generate_before_main_content'); ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) : ?>
					<div class="comments-area">
						<?php comments_template(); ?>
					</div>
			<?php endif; ?>

		<?php endwhile; // end of the loop. ?>

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
		
	
?>
<a href="<?=$link?>"><?=__("But you can join the course very easily","ultimate-course")?></a>
</div>

		<?php
	  }
  }
?>

		<?php do_action('generate_after_main_content'); ?>
		</div>
	</div>

<?php
do_action('generate_sidebars');
get_footer();
?>