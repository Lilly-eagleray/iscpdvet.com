<?php


function ultimate_course_lecturer_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Lecturers', 'Post Type General Name', 'ultimate-course' ),
		'singular_name'       => _x( 'Lecturer', 'Post Type Singular Name', 'ultimate-course' ),
		'menu_name'           => __( 'Lecturers', 'ultimate-course' ),
		'parent_item_colon'   => __( 'Main lecturer', 'ultimate-course' ),
		'all_items'           => __( 'All lecturers', 'ultimate-course' ),
		'view_item'           => __( 'Watch a lecturer', 'ultimate-course' ),
		'add_new_item'        => __( 'Add new lecturer', 'ultimate-course' ),
		'add_new'             => __( 'Add new', 'ultimate-course' ),
		'edit_item'           => __( 'Edit lecturer', 'ultimate-course' ),
		'update_item'         => __( 'Update lecturer', 'ultimate-course' ),
		'search_items'        => __( 'Search lecturer', 'ultimate-course' ),
		'not_found'           => __( 'No lecturers found', 'ultimate-course' ),
		'not_found_in_trash'  => __( 'No lecturers in can', 'ultimate-course' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Lecturers', 'ultimate-course' ),
		'description'         => __( 'Lecturers', 'ultimate-course' ),
		'rewrite' => array('slug' => 'lecturer'),

		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports' => array(
            'title',
            'editor',
			'thumbnail',
			'excerpt'
        ),
		//'taxonomies'          => array( 'category' ),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		//'taxonomies'          => array( 'genres' ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		// 'capability_type'     => array('gruops_billing','gruops_billing'),
         'map_meta_cap'        => true,
	);
	
	// Registering your Custom Post Type
	register_post_type( 'lecturer_post_type', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'ultimate_course_lecturer_post_type', 0 );

?>