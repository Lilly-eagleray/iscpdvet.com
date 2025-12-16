<?php

    $course_id = $_GET['course_id'];
    $lessons = get_field('lessons', $course_id );
    $lessen = intval( $_GET['lesson']) - 1;
    $lessen_data = $lessons[$lessen];

?>


<div class="inside-article">


    <div class="col-md-9">

        <h1><?=get_the_title( $course_id )?></h1>
        <h3><?= $lessen_data['name'] ?></h3>

        <main id="main" class="site-main" role="main">

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

    </div>

    <div class="col-md-3">

        <?php
        
        if(get_post_meta($course_id,"_course_teacher")[0]){ ?>

        <div class="box_level box_teacher_course">
        <p class="title_box_side"><?=__("Lecturer","ultimate-course")?></p>	
                <?php
                $teacher = get_post_meta($course_id,"_course_teacher")[0];
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
</div>

