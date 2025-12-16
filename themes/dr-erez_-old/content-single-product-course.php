<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $course;
?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
	 global $product;
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php	
	if(get_post_meta(get_the_id(),"_alert_type")){
	$alert_type = "";
	$alert_type = get_post_meta(get_the_id(),"_alert_type")[0];
?>
<div class="alert <?=$alert_type?>">
<?php
if(get_post_meta(get_the_id(),"_course_massage")){
	$course_massage = "";
	$course_massage = get_post_meta(get_the_id(),"_course_massage")[0];
	echo $course_massage;
	
}
?>
</div>


<?php
}
?>

<div class="row course-top-section">
	<div class="col-md-8">
		<h1 class="course-title"> <?=$product->get_title()?> </h1>
	<?php
	get_template_part( 'product-image-prod' );
	
	
	get_template_part( 'description-prod' );	
				
	?>
	</div>
	
	<div class="col-md-4">
	<?php
		if(get_post_meta(get_the_id(),"_add_info_product_course")){
		$add_info_product_course = get_post_meta(get_the_id(),"_add_info_product_course")[0];
		
		
	?>
	<div class="teacher_box">
	<div class="info-wrapper">
		<?php /*<div class="panel-heading"><?_e("Course info","ultimate-course")?></div> */ ?>
		<div class="teacher_info col-md-12">
		
		<?=$add_info_product_course?>

		<?php
		get_template_part( 'price-prod' );	
		
		?>
		
		<?php get_template_part( 'simple-prod' );	?>
		<?php
		if(function_exists('icl_object_id')){
		global $sitepress;
		if($sitepress->get_current_language() != "en"){
			$is_shown = get_post_meta( $post->ID, '_hide_member_text',true);
			if(!$is_shown){
			?>
			<div class="massage">
			<?=__("CPD-Vet Members and members of the Veterinarians Association are entitled to a discount of 10%","ultimate-course")?>
			</div>
			<?php
			}
		}
		}
		?>
		</div>
	</div>
	<?php
	}
	?>
	
	
	<?php
	
	if(get_post_meta(get_the_id(),"_course_teacher")){
	$teacher_id = get_post_meta(get_the_id(),"_course_teacher")[0];
	?>
	<div class="lecturer-wrapper">
		<?php /*<div class="panel-heading"><?=__("Lecturer biography","ultimate-course")?></div>*/ ?>
		<?php
		foreach($teacher_id AS $teacher){
		$post_teacher = get_post($course->returnCurrentLangPost($teacher));
		?>
		<div class="box_lecturer">
		<div class="teacher_info_wrapper">
		<span class="teacher_name"><?=__("Lecturer name","ultimate-course")?>: <?=$post_teacher->post_title?></span>
		<div class="teacher_info">
		<?=$post_teacher->post_excerpt;?>
		</div>
		</div>
		<div class="lecturer_image">
		<?php
		echo get_the_post_thumbnail( $post_teacher->ID, array( 200, 500) ); 
		?>
		</div>
		<div class="lecturer_btn">
		<a class="btn btn-default btn_full" href="<?=get_permalink($post_teacher->ID)?>"><?=__("More info","ultimate-course")?> >></a>
		
		</div>
		</div>
		<?php } ?>				
	</div>
	<?php
	}
	?>
	</div>

	</div>
</div>
<div class="col-md-12 course-content-section">
	<?php 
	$course_content = get_field('course_content');
	if ($course_content){
		echo $course_content;
	}?>
	<div class="row btn_cancel_info">
	<?php
	if(get_post_meta(get_the_id(),"_cancel_page")){
		$cancel_page = get_post_meta(get_the_id(),"_cancel_page")[0];
	?>
	<a href="<?=get_permalink($cancel_page)?>" class="button alt btn_white naf_btn"><span><?php _e("Cancel info","ultimate-course")?></span></a>
	<?php
	}
	if(get_post_meta(get_the_id(),"_info_page")){
		$info_page = get_post_meta(get_the_id(),"_info_page")[0];
	?>
	<a href="<?=get_permalink($info_page)?>" class="button alt btn_white naf_btn"><span><?php _e("Course Program","ultimate-course")?></span></a>
	<?php
	}
	?>
	</div>
</div>
	
	<!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
