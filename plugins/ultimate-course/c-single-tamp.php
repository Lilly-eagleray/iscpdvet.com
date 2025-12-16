<?php

    $course_id = $_GET['course_id'];
    $lessons = get_course_lessons($course_id);
    $lessen = intval( $_GET['lesson']) - 1;
    $lessen_data = $lessons[$lessen];

?>


<div class="">


    <div class="single-lesson-wrapper">

      <div class="title-wrapper">
		<h2><?= $lessen_data['name'] ?></h2>
        <?php
        
        if(get_post_meta($course_id,"_course_teacher")[0]){ ?>
	   <div class="teacher_course">
	   <span class="">
		   <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
			   <path d="M15.056 12.1804C15.9955 11.4412 16.6813 10.4276 17.0179 9.28045C17.3545 8.13335 17.3252 6.90984 16.9341 5.78016C16.543 4.65048 15.8095 3.6708 14.8356 2.97741C13.8618 2.28402 12.696 1.91141 11.5005 1.91141C10.3051 1.91141 9.13932 2.28402 8.16548 2.97741C7.19164 3.6708 6.45813 4.65048 6.067 5.78016C5.67587 6.90984 5.64658 8.13335 5.98319 9.28045C6.31981 10.4276 7.00559 11.4412 7.94513 12.1804C6.3352 12.8254 4.93049 13.8952 3.88073 15.2758C2.83098 16.6563 2.17554 18.2958 1.9843 20.0196C1.97045 20.1454 1.98153 20.2728 2.0169 20.3944C2.05227 20.5159 2.11124 20.6293 2.19044 20.7281C2.3504 20.9276 2.58305 21.0554 2.83721 21.0833C3.09138 21.1113 3.34624 21.0371 3.54573 20.8772C3.74522 20.7172 3.873 20.4846 3.90096 20.2304C4.1114 18.3571 5.00466 16.6269 6.41009 15.3705C7.81552 14.1141 9.63457 13.4196 11.5197 13.4196C13.4049 13.4196 15.2239 14.1141 16.6293 15.3705C18.0348 16.6269 18.928 18.3571 19.1385 20.2304C19.1645 20.4659 19.2769 20.6834 19.4539 20.8409C19.6308 20.9984 19.8599 21.0848 20.0968 21.0833H20.2022C20.4534 21.0544 20.683 20.9274 20.841 20.73C20.999 20.5325 21.0725 20.2806 21.0455 20.0292C20.8534 18.3005 20.1944 16.6568 19.1393 15.2741C18.0841 13.8915 16.6726 12.822 15.056 12.1804ZM11.5005 11.5C10.7424 11.5 10.0012 11.2752 9.37086 10.854C8.74047 10.4328 8.24914 9.83408 7.95901 9.13363C7.66887 8.43318 7.59296 7.66242 7.74087 6.91883C7.88878 6.17523 8.25387 5.4922 8.78997 4.9561C9.32607 4.42 10.0091 4.05491 10.7527 3.907C11.4963 3.75909 12.267 3.835 12.9675 4.12514C13.6679 4.41527 14.2666 4.9066 14.6878 5.53699C15.1091 6.16738 15.3339 6.90851 15.3339 7.66667C15.3339 8.68334 14.93 9.65836 14.2111 10.3773C13.4922 11.0961 12.5172 11.5 11.5005 11.5Z" fill="#0046F2"/>
		   </svg>
		   <?=__("Lecturer","ultimate-course")?>:</span>	
                <?php
                $teacher = get_post_meta($course_id,"_course_teacher")[0];
                foreach($teacher AS $teacher_in){
                $teacher_post = get_post($teacher_in);
                ?>
                <a class="inner_lecturer" href="<?=get_post_permalink($teacher_in)?>">
                <?php
                echo $teacher_post->post_title;
                ?>
            
                </a>
                
                <?php } ?>
	   </div>
            <?php } ?>
	  </div>

        <main id="inner-content">

        <?php	

        if( $lessen_data['type'] != 'none'){

            if( $lessen_data['type'] == 'youtube'){
                echo '<div class="embed-responsive embed-responsive-4by3">
                    <iframe class="embed-responsive-item" src="//www.youtube.com/embed/' . html_entity_decode( $lessen_data['url'] ) . '?rel=0&showinfo=0"></iframe>
                </div>';
            }
            if( $lessen_data['type'] == 'video' ){
                echo '<div align="center" class="embed-responsive embed-responsive-16by9">
                    <video controls class="embed-responsive-item">
                        <source src="'. html_entity_decode( $lessen_data['file'] ) .'" type="video/mp4">
                    </video>
                </div>';
            }
            if( $lessen_data['type'] == 'pdf' ){
                echo '<iframe src="https://docs.google.com/gview?url=' . html_entity_decode($lessen_data['file'] ) . '&embedded=true&toolbar=hide" style="width:100%; height:600px;" frameborder="0"></iframe>';
            }
            if( $lessen_data['type'] == 'slideshow' ){
                echo '<iframe src="https://docs.google.com/gview?url=' . html_entity_decode($lessen_data['file'] ) . '&embedded=true&toolbar=hide" style="width:100%; height:400px;" frameborder="0"></iframe>';
            }
            if( $lessen_data['type'] == 'iplayer' ){
                echo '<div class="embed-responsive embed-responsive-4by3">
                <iframe class="embed-responsive-item" src="//iplayerhd.com/player/video/' . html_entity_decode( $lessen_data['url'] ) . '?cbartype=auto" allowtransparency="true" frameborder="0" scrolling="no"  allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen></iframe>
                </div>';
            
            }
            if( $lessen_data['type'] == 'iplayer_playlist'){
            echo '<div class="embed-responsive embed-responsive-4by3">
                <iframe class="embed-responsive-item" src="//iplayerhd.com/player/playlist/' . html_entity_decode( $lessen_data['url'] ) . '?playlist=bottom" allowtransparency="true" frameborder="0" scrolling="no"  allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen></iframe>
                </div>';
            
            }
        }
                
        ?>

        </main><!-- #main -->

		<div class="under_video_text">
			<?php
			echo $lessen_data['content'];
			?>
		</div>
            
            <!--<?php if(get_post_meta($course_id,"_course_level")){ ?>
        <div class="box_level box_level_course">
        <p class="title_box_side"><?=__("Course level","ultimate-course")?></p>
                <?=get_post_meta($course_id,"_course_level")[0]?>	
        </div>
        <?php } ?>
        !-->
        <?php if(get_post_meta($course_id,"_forum_id")){ ?>
        <div class="box_level box_forum_course">
                <a href="<?=get_permalink(get_post_meta($course_id,"_forum_id")[0])?>">פורום - <?=get_the_title($course_id)?></a>	
        </div>
        <?php } ?>
    
    </div>

</div>