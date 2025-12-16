<?php
/**
 * @version 1.6
 */
/*
Plugin Name: WP woocommerce stac
Author: Liran Hecht
Version: 1.6
*/
function db_woo_stat(){
global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

$product_statistics = $wpdb->prefix . "product_statistics"; 

$sql = "CREATE TABLE $product_statistics (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  product_id text NOT NULL,
  user_id text NOT NULL,
  time date DEFAULT '0000-00-00' NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}
register_activation_hook( __FILE__, 'db_woo_stat' );


add_action('addProduct', 'addProduct', 10, 2);

function add_prod_stac($order_id)
{	
	global $course;
	global $wpdb;	
    //getting order object
    $order = wc_get_order($order_id);
	$status = get_post_meta($order_id,"response",true);
    $items = $order->get_items();
if($status == "000"){
	if(is_user_logged_in()){
		foreach ($items AS $item_id => $item_data)
		{
		$current_user = wp_get_current_user();	
		$prod_id = $item_data["product_id"];
		$user_id = $current_user->ID;
				
		$product_statistics = $wpdb->prefix . "product_statistics";
		//$checkIfExsist = $wpdb->get_row( "SELECT * FROM {$product_statistics} WHERE product_id = {$prod_id} AND user_id = {$user_id}" );
		$loop = $item_data->get_quantity();
		
		//$is_exsist = count($checkIfExsist);
			//if(!$is_exsist){
				for ($x = 1; $x <= $loop; $x++) {
					//echo $prod_id;
					$date= date('Y-m-d') ;
					$wpdb->insert( 
					$product_statistics, 
					array( 
					'product_id' => $prod_id, 
					'user_id' => $user_id,
					'time' => $date
					)
					);
					
				}

			}		
			
				
			//}
			
		}
}
  
}

function add_prod_stac_single($prod_id,$user_id)
{	
	global $course;
	global $wpdb;

    $related_is = get_post_meta($prod_id,"_course_related", true );
    if( $related_is ){
        $action_id = $course->addCourseToUserById($related_is ,$user_id);
    }

	$product_statistics = $wpdb->prefix . "product_statistics";
				$date= date('Y-m-d') ;
				return $wpdb->insert( 
				$product_statistics, 
				array( 
				'product_id' => $prod_id, 
				'user_id' => $user_id,
				'time' => $date
				)
				);
  
}

function remove_prod_stac($row_id, $prod_id, $user_id)
{	
	global $course;
	global $wpdb;

    $related_is = get_post_meta($prod_id,"_course_related", true );
    if( $related_is ){
        $action_id = $course->delCourseToUserById($related_is ,$user_id);
    }

	$product_statistics = $wpdb->prefix . "product_statistics";	
	return $wpdb->delete( 
				$product_statistics, 
				array( 'id' => $row_id )
				);



  
}
function alert_massage($msg){
	return "<div style='border:2px solid white;padding:5px;color:red'>" . $msg . "</div>";
}

//add_action('woocommerce_thankyou', 'add_prod_stac');


add_action( 'admin_menu', 'prod_stac' );
/** Step 1. */
function prod_stac() {
	add_submenu_page(  'course-drerez-page', 'ניהול רישום', 'ניהול רישום', 'manage_options', 'product-statistics', 'product_statistics_func' );

}

function product_statistics_func(){
	if(isset($_POST["del_course_from_user"])){
		if(remove_prod_stac($_POST["row_id"], $_POST["product_id"],  $_POST["user_id"])){
			echo alert_massage("המשתמש נמחק בהצלחה");
		}
		
	}
	if(isset($_POST["add_user"])){
		if(add_prod_stac_single($_POST["product_id"],$_POST["user_id"])){
			echo alert_massage("המשתמש נוסף בהצלחה");
		}
	}
	?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	
	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" class="form-inline">
	<div class="checkbox">
	  <label><input type="checkbox" name="add_user" value="">הוסף משתמש לקורס</label>
	</div>
	 <select name="user_id" class="select-t user_id">
	   <option value="0">בחר משתמש</option>
	   <?php
	   $blogusers = get_users();
	   foreach($blogusers AS $blogusers_in){
		?> 
		  
		<option value="<?=$blogusers_in->ID?>"><?=$blogusers_in->display_name?></option>
		  
	   <?php
	   }
	   ?>
	   </select>
	   
	  <?php
	  $args = array( 'post_type' => 'product', 'posts_per_page' => -1,
	  'tax_query'             => array(
        array(
            'taxonomy'      => 'product_cat',
            'field' => 'term_id', //This is optional, as it defaults to 'term_id'
            'terms'         => array(43,41,45,57,65,68,73),
            'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
        )
		)
		);
	$product_loop = new WP_Query( $args );
	// The Loop
	?>
	<select name="product_id" class="select-t product_id" style='max-width: 100% !important;'>
	<option value="0">בחר קורס</option>
	<?php
		if ( $product_loop->have_posts() ) {
			while ( $product_loop->have_posts() ) {
				$product_loop->the_post();
			
			echo '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
			
			}
			?>
			</select>
			<?php
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		?>

		<button type="submit" class="button">הצג מידע</button>
        
		</form>
		<?php
		if(isset($_POST["user_id"]) && $_POST["user_id"] != 0){
		$user_info = get_userdata($_POST["user_id"]);
		?>
		<h1>
		שם הנרשם: <?=$user_info->display_name?>
		</h1>
			<table class="table table-striped user-table table2excel">
					<thead>
					  <tr>
						<th>#</th>
						<th>שם פרטי</th>
						<th>שם משפחה</th>
						<th>אימייל</th>
						<th>כתובת</th>
						<th>מיקוד</th>
						<th>טלפון</th>
						<th>סטטוס</th>
						<th>מידע על הסטטוס</th>
						<th>שם הקורס/מוצר</th>
						<th>תאריך רישום</th>
						<th></th>
					  </tr>
					</thead>
			<tbody>
		<?php
			$i = 1;
			foreach(getUserCourses($_POST["user_id"]) AS $user){
			
			$product = wc_get_product($user->product_id);
			
			$address = get_user_meta( $user->user_id, 'billing_address_1', true );
			$billing_pobox = get_user_meta( $user->user_id, 'billing_pobox', true );
			
			$billing_phone = get_user_meta( $user->user_id, 'billing_phone', true );
			$status = get_user_meta( $user->user_id, '_vettype',true);
			$vetid = get_user_meta( $user->user_id, '_vetid',true);

            if( !$product ){
                continue;
            }
		?>
			
			<tr>
			<td>
			<?=$i?>
			</td>
			<td>
			<?=$user_info->first_name?>
			</td>
			<td>
			<?=$user_info->last_name?>
			</td>
			<td>
			<?=$user_info->user_email?>
			</td>
			<td>
			<?=$address?>
			</td>
			<td>
			<?=$billing_pobox?>
			</td>
			<td>
			<?=$billing_phone?>
			</td>
			<td>
			<?=$status?>
			</td>
			<td>
			<?=$vetid?>
			</td>
			<td>
			<?=$product->get_title()?>
			</td>
			<td>
			<?=$user->time?>
			</td>
			<td>
			<?php
			echo '<form method="post" action="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '" class="form-inline form_del_course">
			<input type="hidden" name="del_course_from_user" value="1">
			<input type="hidden" name="row_id" value="' . $user->id . '">	
			<input type="hidden" name="product_id" value="' . $user->product_id. '">	
			<input type="hidden" name="user_id" value="' . $user->user_id . '">	
			<button type="submit" class="submit_del">מחק משתמש</button></form>';
			?>
			</td>
			</tr>
			
			
		<?php
		$i++;
		}
		?>
			 </tbody>
			  </table>
			  <button class="export_to_excel">ייצוא לאקסל</button>

		<?php
		}
		if(isset($_POST["product_id"]) && $_POST["product_id"] != 0){
			$product = wc_get_product($_POST["product_id"]);
			
			?>
			<h1>
			שם הקורס: <?=$product->get_title()?> 
			</h1>
				<table class="table table-striped user-table table2excel">
					<thead>
					  <tr>
						<td>#</th>
						<th>שם פרטי</th>
						<th>שם משפחה</th>
						<th>אימייל</th>
						<th>כתובת</th>
						<th>מיקוד</th>
						<th>טלפון</th>
						<th>הזמנות</th>
						<th>תאריך רישום</th>
						<th></th>
					  </tr>
					</thead>
			<tbody>
			
		<?php
			$i = 1;
			foreach(getCoursesUsers($_POST["product_id"]) AS $user){
			$user_info = get_userdata($user->user_id);
			
			$address = get_user_meta( $user->user_id, 'billing_address_1', true );
			$billing_pobox = get_user_meta( $user->user_id, 'billing_pobox', true );
			
			$billing_phone = get_user_meta( $user->user_id, 'billing_phone', true );
			// $status = get_user_meta( $user->user_id, '_vettype',true);
			// $vetid = get_user_meta( $user->user_id, '_vetid',true);

            if( !$product ){
                continue;
            }
		?>
			
			<tr>
			<td>
			<?=$i?>
			</td>
			<td>
			<?=$user_info->first_name?>
			</td>
			<td>
			<?=$user_info->last_name?>
			</td>
			<td>
			<?=$user_info->user_email?>
			</td>
			<td>
			<?=$address?>
			</td>
			<td>
			<?=$billing_pobox?>
			</td>
			<td>
			<?=$billing_phone?>
			</td>
			<td>
			<?php
			$orders = getOrdersFromProductAndUser($_POST["product_id"], $user->user_id);

			$product_id = isset($_POST["product_id"]) ? sanitize_text_field($_POST["product_id"]) : '';
			
			$names = []; // Array to store names for output
			
			foreach ($orders as $order) {
				$order_id = $order->order_id;
			
				$register_users_data = get_post_meta($order_id, 'register_users_data', true);
			
				$user_data = json_decode($register_users_data, true);
			
				if (isset($user_data[$product_id])) {
					foreach ($user_data[$product_id] as $entry) {
						$name = isset($entry['name']) ? $entry['name'] : 'Unknown';
						$family = isset($entry['family']) ? $entry['family'] : 'Unknown';
						$full_name = $name . ' ' . $family;
			
						$edit_url = get_edit_post_link($order_id);
			
						$names[] = '<a href="' . esc_url($edit_url) . '" target="_blank">' . esc_html($full_name) . '</a>';
					}
				}
			}
			
			echo implode(', ', $names);
			
			
			?>
			</td>
			<td>
			<?=$user->time?>
			</td>
			<td>
			<?php
			echo '<form method="post" action="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '" class="form-inline form_del_course">
			<input type="hidden" name="del_course_from_user" value="1">
			<input type="hidden" name="row_id" value="' . $user->id . '">	
			<input type="hidden" name="product_id" value="' . $user->product_id. '">	
			<input type="hidden" name="user_id" value="' . $user->user_id . '">				
			<button type="submit" class="submit_del">מחק משתמש</button></form>';
			?>
			</td>
			</tr>
	
			
			
		<?php
		$i++;
		}
		?>
			 </tbody>
			  </table>
			   <button class="export_to_excel">ייצוא לאקסל</button>
		<?php
		}
}



function getUserCourses($user_id){
	global $wpdb;
	$product_statistics = $wpdb->prefix . "product_statistics"; 
		$prod_sts = $wpdb->get_results( 
			"
			SELECT * 
			FROM {$product_statistics}
			WHERE user_id = {$user_id}
			"
		);

		return $prod_sts;


}
function getCoursesUsers($product_id){
	global $wpdb;
	$product_statistics = $wpdb->prefix . "product_statistics"; 
		$prod_sts = $wpdb->get_results( 
			"
			SELECT * 
			FROM {$product_statistics}
			WHERE product_id = {$product_id}
			"
		);

		return $prod_sts;
}

function getOrdersFromProductAndUser($product_id, $user_id) {
    global $wpdb;

    // הגדרת שמות הטבלאות
    $customer_lookup = $wpdb->prefix . "wc_customer_lookup"; 
    $order_product_lookup = $wpdb->prefix . "wc_order_product_lookup"; 

    // שליפת הלקוח בצורה בטוחה (הגנה מפני SQL Injection)
    $customer_data = $wpdb->get_row( 
        $wpdb->prepare(
            "SELECT customer_id FROM {$customer_lookup} WHERE user_id = %d",
            $user_id
        )
    );

    // בדיקה: אם לא נמצא לקוח, נחזיר מערך ריק ולא נמשיך לשאילתה הבאה
    if (!$customer_data || !isset($customer_data->customer_id)) {
        return array();
    }

    $customer_id = $customer_data->customer_id;

    // שליפת ההזמנות בצורה בטוחה
    $orders = $wpdb->get_results( 
        $wpdb->prepare(
            "SELECT * FROM {$order_product_lookup} AS opl
            INNER JOIN {$wpdb->posts} AS p ON opl.order_id = p.ID
            WHERE opl.product_id = %d 
            AND opl.customer_id = %d
            AND (p.post_status = 'wc-completed' OR p.post_status = 'wc-processing')",
            $product_id,
            $customer_id
        )
    );

    return $orders;
}

function product_statistics_enque() {
	wp_enqueue_script( 'jquery.table2excel.min.js', plugin_dir_url(__FILE__) . 'js/jquery.table2excel.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'main-statistics.js', plugin_dir_url(__FILE__) . 'js/main-statistics.js', array( 'jquery' ) );

	

	
}
add_action( 'admin_enqueue_scripts', 'product_statistics_enque' );




