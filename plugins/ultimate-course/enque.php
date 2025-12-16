<?php

function drerez_wp_admin_style($hook) {
	 global $wp_scripts;
    $queryui = $wp_scripts->query('jquery-ui-core');
		wp_enqueue_script( 'jquery-ui-sortable' );
		

	wp_enqueue_style( 'bootstrap-iso.css', plugin_dir_url(__FILE__)  . '/css/bootstrap-iso.css');
	wp_enqueue_script( 'bootstrap.min.js', plugin_dir_url(__FILE__)  . '/js/bootstrap.min.js',array( 'jquery' ));

	wp_enqueue_style( 'main.css', plugin_dir_url(__FILE__)  . '/css/admin/main.css');
	wp_enqueue_style( 'jquery-customselect-1.9.1.css', plugin_dir_url(__FILE__)  . '/css/admin/jquery-customselect-1.9.1.css');
	
	wp_enqueue_style( 'dataTables.css', '//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css');
	wp_enqueue_script( 'dataTables.js', '//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', array( 'jquery' ));

	wp_enqueue_script( 'mainadmin', plugin_dir_url(__FILE__) . '/js/admin/main.js', array( 'jquery' ));
		 wp_localize_script( 'mainadmin', 'ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ))
		);
		

	wp_enqueue_media();
    wp_enqueue_script( 'wp-media-uploader', plugin_dir_url(__FILE__) . '/js/admin/wp_media_uploader.js', array( 'jquery' ) );
    wp_enqueue_script( 'jquery-customselect', plugin_dir_url(__FILE__) . '/js/admin/jquery-customselect-1.9.1.min.js', array( 'jquery' ) );
   
	
}
add_action( 'admin_enqueue_scripts', 'drerez_wp_admin_style' );


function drerez_enque() {

	wp_enqueue_style( 'bootstrap-iso.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
	wp_enqueue_style( 'main.css', plugin_dir_url(__FILE__)  . '/css/main.css');
	wp_enqueue_script( 'main.js', plugin_dir_url(__FILE__) . '/js/main.js', array( 'jquery' ) );
	wp_localize_script( 'main.js', 'ajax_object',
					array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
					"vet" => __('Veterinary license number',"ultimate-course"),
					"student" => __('Name of the institution',"ultimate-course"),
					"tech" => __('Name of the clinic',"ultimate-course"),
			)	
		);
	wp_enqueue_script( 'sweetalert2.min.js', plugin_dir_url(__FILE__) . '/js/sweetalert2.min.js', array( 'jquery' ) );
	wp_enqueue_style( 'sweetalert2.min.css', plugin_dir_url(__FILE__)  . '/css/sweetalert2.min.css');
	//wp_enqueue_script( 'bootstrap.min.js', plugin_dir_url(__FILE__) . '/js/bootstrap.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'mainboot.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ) );
	wp_enqueue_style( 'font.css', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	if(is_rtl()){
		
		wp_enqueue_style( 'main_rtl_bootstrap.css','https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.min.css');
		wp_enqueue_style( 'main_rtl.css', plugin_dir_url(__FILE__)  . '/css/main_rtl.css');
	}
	
	wp_enqueue_style( 'animate', plugin_dir_url(__FILE__)  . '/css/animate.css');
	wp_enqueue_style( 'tipso', plugin_dir_url(__FILE__)  . '/css/tipso.css');
	wp_enqueue_script( 'tipso', plugin_dir_url(__FILE__) . '/js/tipso.js', array( 'jquery' ) );
	
}
add_action( 'wp_enqueue_scripts', 'drerez_enque' );

?>