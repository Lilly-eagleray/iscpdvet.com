<?php

function woo_naf_settings_page() {

	add_submenu_page(  'course-drerez-page',
        'הגדרות כלליות',
        'הגדרות כלליות',
        'manage_options',
        'woo-naf-settings',
        'woo_naf_settings_func'
    );
}
add_action( 'admin_menu', 'woo_naf_settings_page' );


function woo_naf_settings_func(){

    if( isset( $_POST['_email_msg_after_online']) && $_POST['_email_msg_after_online'] != '' ){

        update_option('_email_msg_after_online_toggle', wp_kses_post( $_POST['_email_msg_after_online_toggle'] ) );
        update_option('_email_msg_after_online', wp_kses_post( $_POST['_email_msg_after_online'] ) );

        update_option('_email_msg_after_member_toggle', wp_kses_post( $_POST['_email_msg_after_member_toggle'] ) );
        update_option('_email_msg_after_member', wp_kses_post( $_POST['_email_msg_after_member'] ) );

        update_option('_email_msg_after_course_toggle', wp_kses_post( $_POST['_email_msg_after_course_toggle'] ) );
        update_option('_email_msg_after_course', wp_kses_post( $_POST['_email_msg_after_course'] ) );

        update_option('_email_msg_after_equipment_toggle', wp_kses_post( $_POST['_email_msg_after_equipment_toggle'] ) );
        update_option('_email_msg_after_equipment', wp_kses_post( $_POST['_email_msg_after_equipment'] ) );

        update_option('_member_email_subject', wp_kses_post( $_POST['_member_email_subject'] ) );
        update_option('_member_email_content', wp_kses_post( $_POST['_member_email_content'] ) );
    }   

    $email_msg_after_online = stripslashes( get_option('_email_msg_after_online'));
    $email_msg_after_online_toggle = get_option('_email_msg_after_online_toggle');

    $email_msg_after_member = stripslashes( get_option('_email_msg_after_member'));
    $email_msg_after_member_toggle = get_option('_email_msg_after_member_toggle');

    $email_msg_after_course = stripslashes( get_option('_email_msg_after_course'));
    $email_msg_after_course_toggle = get_option('_email_msg_after_course_toggle');

    $email_msg_after_equipment = stripslashes( get_option('_email_msg_after_equipment'));
    $email_msg_after_equipment_toggle = get_option('_email_msg_after_equipment_toggle');

    $member_email_subject = get_option('_member_email_subject');
    $member_email_content = get_option('_member_email_content');
?>

<div class="wrap">
    <h1>הגדרות כלליות</h1>
    <br>

    <?php
        $title = 'הצגת תפריט מינימאלי';
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . '&hide_full_menu=1';
    
        $hide_full_menu = get_option( 'hide_full_menu' );
    
        if( $hide_full_menu ){
            $title = 'הצגת תפריט מלא';
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . '&hide_full_menu=0';
        }
    ?>
    <a href='<?=$url?>'><?=$title?></a>
    
    <form method="post" action="">
    
        <div class='email_msg'>
            <h2> הודעה במייל לאחר רכישת קורס און ליין </h2>
            <label for="">
                <input type="checkbox" name="_email_msg_after_online_toggle" id="" <?= $email_msg_after_online_toggle ? 'checked' : '' ?>>הפעלת הודעה
            </label>
            <?php
                wp_editor(
                    $email_msg_after_online,
                    '_email_msg_after_online',
                    array(
                        'media_buttons' =>  true,
                    )
                );
            ?>
        </div>

        <br>
        <br>

        <div class='email_msg'>
            <h2> הודעה במייל לאחר רכישת מועדון   </h2>
            <label for="">
                <input type="checkbox" name="_email_msg_after_member_toggle" id="" <?= $email_msg_after_member_toggle ? 'checked' : '' ?>>הפעלת הודעה
            </label>
            <?php
                wp_editor(
                    $email_msg_after_member,
                    '_email_msg_after_member',
                    array(
                        'media_buttons' =>  true,
                    )
                );
            ?>
        </div>


        <br>
        <br>

        <div class='email_msg'>
            <h2> הודעה במייל לאחר רכישת קורס מעשי   </h2>
            <label for="">
                <input type="checkbox" name="_email_msg_after_course_toggle" id="" <?= $email_msg_after_course_toggle ? 'checked' : '' ?>>הפעלת הודעה
            </label>
            <?php
                wp_editor(
                    $email_msg_after_course,
                    '_email_msg_after_course',
                    array(
                        'media_buttons' =>  true,
                    )
                );
            ?>
        </div>


        <br>
        <br>

        <div class='email_msg'>
            <h2> הודעה במייל לאחר רכישת קורס ציוד   </h2>
            <label for="">
                <input type="checkbox" name="_email_msg_after_equipment_toggle" id="" <?= $email_msg_after_equipment_toggle ? 'checked' : '' ?>>הפעלת הודעה
            </label>
            <?php
                wp_editor(
                    $email_msg_after_equipment,
                    '_email_msg_after_equipment',
                    array(
                        'media_buttons' =>  true,
                    )
                );
            ?>
        </div>





    <br>
    <br>
    <hr>
    
    <br>
    <br>

    <h2> הודעת סיום מנוי </h2>

    <label for="">כותרת מייל</label>
    <input style="width: 100%;" type="text" name='_member_email_subject' value='<?= $member_email_subject ?>'>
    <br><br>
    <?php
        wp_editor(
            $member_email_content,
            '_member_email_content',
            array(
                'media_buttons' =>  true,
            )
        );
    ?>


<?php
submit_button('שמירה');
?>
</form>
</div>
<?php
}


if ( ! wp_next_scheduled( 'check_date_of_members_hook' ) ) {
    wp_schedule_event( time(), 'daily', 'check_date_of_members_hook' );
}


add_action( 'check_date_of_members_hook', 'check_date_of_members');


function check_date_of_members(){

    $users = get_users();

    $member_email_subject = get_option('_member_email_subject');
    $member_email_content = nl2br(get_option('_member_email_content'));

    foreach ( $users as $key => $user ) {
        global $wpdb;
        $table = $wpdb->prefix . 'course_to_user';

        $user_id = $user->ID;

        $is_premited = $wpdb->get_results( "SELECT * FROM $table WHERE `user_id`= $user_id AND `course_id`= 743"  );

        if( !$is_premited ){
            continue;
        }

        $today = date('Y-m-d');
        $new_time = date('Y-m-d', strtotime('+1 year', strtotime( $is_premited[0]->time )));
        
        if( $new_time < $today ){
            wp_mail($user->user_email, $member_email_subject, $member_email_content );
            $wpdb->delete( $table, array( 'user_id' => $user_id,"course_id" => 743 ) );
        }
    }

    die();
}


//update cart auto and hide update btn
add_action( 'wp_footer', 'isc_cart_update_qty_script' );
function isc_cart_update_qty_script() {
    if (is_cart()) :
        ?>
        <style>
            input[name='update_cart']{
                display: none  !important;
            }
        </style>
        <script type="text/javascript">
            (function($){
                $(function(){
                    $('div.woocommerce').on( 'change', '.qty', function(){
                        $("[name='update_cart']").trigger('click');
                    });
                });
            })(jQuery);
        </script>
        <?php
    endif;
}


add_filter( 'retrieve_password_message', 'isc_retrieve_password_message', 10, 4 );
function isc_retrieve_password_message( $message, $key, $user_login, $user_data ) {

    $message = "שלום<br>מייל זה נשלח אליכם בהמשך לבקשתכם לאיפוס הסיסמא לאתר iscpdvet.com<br>באם לא ביקשתם איפוס סיסמא אנא התעלמו מהודעה זו.<br>";
    $message .= '<a href="' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . '">לאיפוס הסיסמא לחצו כאן</a>';
    return $message;

}


  
function isc_register_users_data_field( $checkout ) {

    global $woocommerce;

    $is_in_cart = false;

    $arr_table = [];

    foreach ( $woocommerce->cart->get_cart() as $cart_item ){
        $categories = array(43,41,45,57,65,68);
        if ( has_term( $categories, 'product_cat', $cart_item['product_id'] ) ) {
            $arr_table[ $cart_item['product_id'] ] = ['item_name'=> get_the_title( $cart_item['product_id'] ), 'quantity' => $cart_item['quantity'] ];
        }
    }

    if( !empty( $arr_table ) ){
        echo '<br><h2>רשימת נרשמים לקורסים</h2><span class="req">יש למלא את פרטי הנרשמים</span>';
        echo '<small>יש למלא את כל הפרטים, שם פרטי ומשפחה באנגלית בלבד</small><br>';
        echo '<input onchange="copy_data_from_user(this)" type="checkbox" id="copy_data"> <label for="copy_data">פרטי נרשם זהים למשלם</label><br>';
        ?>
        <script>
        
        let register_users_data = {};

        let enable = true;

        function add_register_users_data( key, num, type, el ){
            
            if( Object.keys(register_users_data).length === 0 ){
                jQuery('.more_data').val('');
            }

            if (!(key in register_users_data) ){
                register_users_data[key] = {};
            }

            if (!(num in register_users_data[key]) ){
                register_users_data[key][num] = {};
            }

            register_users_data[key][num][type] = jQuery(el).val();
            
            jQuery('#register_users_data').val( JSON.stringify(register_users_data) );
            
            enable = false;

            jQuery('.more_data').each( function( el ){
                if( !jQuery(this).val() ){
                    enable = true;
                }
            });

            enable_disable_naf();
        }

        function enable_disable_naf(){
            if( !enable ){
                jQuery('.req').hide();
                jQuery('div#payment').css('opacity', 1);
                jQuery('div#payment').css('pointer-events', 'all');

            }else{
                jQuery('.req').show();
                jQuery('div#payment').css('opacity', 0.5);
                jQuery('div#payment').css('pointer-events', 'none');
            }
        }

        function copy_data_from_user( el ){

            if( jQuery( el ).prop( "checked" ) ){

                jQuery('.more_data').eq(0).val( jQuery('#billing_first_name').val() ).trigger("input");
                jQuery('.more_data').eq(0).val( jQuery('#billing_first_name').val() ).trigger("input");
                jQuery('.more_data').eq(1).val( jQuery('#billing_last_name').val() ).trigger("input");
                jQuery('.more_data').eq(2).val( jQuery('#billing_email').val() ).trigger("input");
                jQuery('.more_data').eq(3).val( jQuery('#billing_phone').val() ).trigger("input");
                enable = false;
            }else{
                enable = true;
                jQuery('.more_data').val('');
                jQuery('.req').show();
                register_users_data = {};
            }
            jQuery('#register_users_data').val( JSON.stringify(register_users_data) );
            enable_disable_naf();

        }

        jQuery( 'body' ).on( 'updated_checkout', function() {
            enable_disable_naf();
        });

        </script>

        <style>
            .req {
                color: red;
                display: block;
            }

        </style>

        <?php
        foreach ( $arr_table as $key => $item ) {
            echo '<br><b>קורס: </b>' . $item['item_name'] . '<br><br>';
            for ($i=0; $i < $item['quantity']; $i++) { 
                ?>
                    <?php if($item['quantity'] > 1) { ?> <small>נרשם <?= $i + 1?></small><br> <?php } ?>
                    <input class='more_data' type="text" placeholder='*שם פרטי (אנגלית בלבד)' oninput="add_register_users_data(<?=$key?>, <?=$i?>, 'name', this)">
                    <input class='more_data' type="text" placeholder='*שם משפחה (אנגלית בלבד)' oninput="add_register_users_data(<?=$key?>, <?=$i?>, 'family', this)">
                    <input class='more_data' type="email" placeholder='*דוא"ל' oninput="add_register_users_data(<?=$key?>, <?=$i?>, 'email', this)">
                    <input class='more_data' type="text" placeholder='*טלפון' oninput="add_register_users_data(<?=$key?>, <?=$i?>, 'tel', this)">
                    <br>
                <?php
            }
        }
        echo '<br>';
    }

    woocommerce_form_field( 'register_users_data', array(        
      'type' => 'text',        
      'class' => array( 'form-row-wide naf_hide' ),        
      'placeholder' => '',        
      'required' => false,        
   ), $checkout->get_value( 'register_users_data' ) );   
}
add_action( 'woocommerce_before_order_notes', 'isc_register_users_data_field' );

function isc_save_register_users_data_field( $order_id ) { 
    if ( isset( $_POST['register_users_data'] ) && $_POST['register_users_data'] != '' ){
        update_post_meta( $order_id, 'register_users_data', $_POST['register_users_data'] );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'isc_save_register_users_data_field' );

function print_regiser_users_data(){

    $order_id = $_GET['post'];

    $data = get_post_meta( $order_id, 'register_users_data', true );

    if( $data ){
        $data = json_decode( $data );

        echo '<h3>רשימת נרשמים:</h3>';

        foreach ( $data as $key => $item ) {
            echo '<b>קורס: </b>' . get_the_title( $key ) . '<br><br>';

            foreach ( $item as $in_key => $value ) {
                if( is_array( $item )  && count( $item ) > 0 ){
                    echo "נרשם: " . ( $in_key + 1 ) . '<br>';
                }
                echo '<b>שם פרטי: </b>' . $value->name . '<br>';
                echo '<b>שם משפחה: </b>' . $value->family . '<br>';
                echo "<b>דואר אלקטרוני:</b><a href='mailto:$value->email'>$value->email</a><br>";;
                echo "<b>טלפון: </b><a href='tel:$value->tel'>$value->tel</a><br>";
                echo '<br>';
            }

            echo '<hr><br>';
        }

        $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "&download_users_csv";
        echo "<a href='$actual_link'>יצוא רשומים לאקסל</a>";
        
    }
}

add_action( 'add_meta_boxes', 'naf_woo_add_meta_boxes' );

if( isset( $_GET['download_users_csv']) ){

    $order_id = $_GET['post'];

    $data = get_post_meta( $order_id, 'register_users_data', true );
    $data = json_decode( $data );
    $array = [];
    $array[] = ['course', 'first name', 'last name', 'email', 'phone'];
    foreach ( $data as $key => $item ) {
        foreach ( $item as $in_key => $value) {
            $array[] = [
                'course'=> get_the_title( $key ),
                'first name'=> $value->name,
                'last name'=> $value->family,
                'email'=> $value->email,
                'phone'=> $value->tel,
            ];
        }
    }
    
    download_send_headers("data_export_" . date("Y-m-d") . ".csv");
    echo array2csv($array);
    die();
}

function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   //fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download 
    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
}

function naf_woo_add_meta_boxes(){
    add_meta_box(
        'register_users_data',
        'נרשמים לקורס',
        'print_regiser_users_data',
        'shop_order'
    );
}



if( isset($_GET['hide_full_menu']) && $_GET['hide_full_menu'] == 1 ){
    update_option( 'hide_full_menu' , 1 );
}

if( isset($_GET['hide_full_menu']) && $_GET['hide_full_menu'] == 0 ){
    update_option( 'hide_full_menu' , 0 );
}

function naf_admin_admin_enqueue_scripts() {
	if( get_option( 'hide_full_menu' ) ) { 
		wp_enqueue_style('admin-editor', get_stylesheet_directory_uri() . '/naf/admin.css',[],'1.0.0');
    }
}
add_action( 'admin_enqueue_scripts', 'naf_admin_admin_enqueue_scripts' );




function woocommerce_grouped_product_thumbnail( $product ) {
    $image_size = array( 300, 300 );  // array( width, height ) image size in pixel 
    $attachment_id = get_post_meta( $product->id, '_thumbnail_id', true );
    ?>
    <td class="label">
        <?php echo wp_get_attachment_image( $attachment_id, $image_size ); ?>
    </td>
    <?php
}
add_action( 'woocommerce_grouped_product_list_before_price', 'woocommerce_grouped_product_thumbnail' );

//jQuery('.woocommerce-grouped-product-list.group_table').prepend("<tr><th>כמות</th><th>מוצר</th><th>תמונה</th><th>מחיר</th></tr>") 


function naf_course_add_course_to_user( $order_id ){

    //getting order object
    $order = wc_get_order( $order_id );
	$orderstat = $order->get_status();

    if( get_post_meta( $order_id, 'addd_course_to_user', true ) ){
        return;
    }

    if( $orderstat != "failed" ){

        global $course;
        $data = get_post_meta( $order_id, 'register_users_data', true );

        if( $data ){
            $data = json_decode( $data );

            foreach ( $data as $key => $item ) {

                $course_id = $key;

                $_course_related = get_post_meta( $course_id ,"_course_related", true );

                if( $_course_related ){
                    $course_id = $_course_related;
                }
                
                foreach ( $item as $in_key => $value ) {

                    if( email_exists( $value->email ) ){

                        $user = get_user_by( 'email', $value->email );
                        $course->setUser( $user ) ;

                    }else{

                        $clean_user_login = sanitize_user( strtok($user_email, '@') . '_' . time() );
                    
                        $random_password = wp_generate_password();
                    
                        $id = wp_insert_user([
                            'user_email' => $user_email,
                            'user_login' => $clean_user_login, 
                            'user_pass' => $random_password,   
                            'display_name' => $value->name . ' ' . $value->family,
                            'first_name' => $value->name,
                            'last_name' => $value->family,
                        ]);

                        update_user_meta( $id , 'billing_phone' , $value->tel );
                        update_user_meta( $id , 'billing_email' , $value->email );

                        $user = get_user_by( 'id', $id );
                        $course->setUser( $user ) ;

                    }

                    $courses = $course->getCoursePerUser( $course_id );
                    
                    if ( ! is_array( $courses ) ) {
                        $courses = [];
                    }                    

                    if( count( $courses ) == 0 ){
                        $listid = $course->addCourseToUser( $course_id );
                        add_prod_stac_single( $key, $user->ID );
                    }
                }
            }
        }
        update_post_meta( $order_id, 'addd_course_to_user', '1' );
	}
}
add_action('woocommerce_thankyou', 'naf_course_add_course_to_user', 10, 1 );


function naf_woocommerce_email_recipient_processing_order( $this_recipient, $order ) {

    $list = [ $this_recipient ];

    $data = get_post_meta( $order->get_id(), 'register_users_data', true );

    if( $data ){
        $data = json_decode( $data );
        foreach ( $data as $key => $item ) {
            foreach ( $item as $in_key => $value ) {
                if( !in_array( $value->email, $list ) ){
                    $list[] = $value->email;
                }
            }
        }
    }
    return implode(',', $list ); 
}; 
add_filter( "woocommerce_email_recipient_customer_processing_order", 'naf_woocommerce_email_recipient_processing_order', 10, 2 ); 


add_shortcode('wp_otp_login_form', 'wp_otp_login_form');

function wp_otp_login_form() {
    

    ob_start();
    $otp_login = isset($_GET['otp']) ? true : false;
    if( !$otp_login ){
    ?>
    <form method='post'>
        <p>יש להזין כתובת מייל ע"מ לקבל סיסמא חד פעמית</p>
        <div>
            <label for="user-email">כתובת מייל</label>
            <input type="email" name="user-email" required>
            <input type="hidden" name="send-email" value='1'>
            <button type="submit">שלחו לי סיסמא</button>
        </div>
        <?php if(isset($_GET['err'])){ ?>
            <p style='color:red'><?= $_GET['err'] == "1" ? "לא הצלחנו לאמת את כתובת המייל" : "" ?></p>
            <p style='color:red'><?= $_GET['err'] == "2" ? "הקוד שגוי" : "" ?></p>
        <?php } ?>
    </form>
    <?php
    }else{ ?>
    <form method='post'>
        <p>נא בדקו את תיבת המייל והזינו את הקוד שנשלח אליכם</p>
        <div>
            <label for="otp">סיסמא</label>
            <input type="text" name="otp" required>
            <input type="hidden" name="login-otp" value='1'>
            <button type="submit">התחברות</button>
        </div>
        <?php if(isset($_GET['err'])){ ?>
            <p style='color:red'><?= $_GET['err'] == "1" ? "לא הצלחנו לאמת את כתובת המייל" : "" ?></p>
            <p style='color:red'><?= $_GET['err'] == "2" ? "הקוד שגוי" : "" ?></p>
        <?php } ?>
    </form>
    <?php }
    return ob_get_clean();
}

add_action( 'init', function(){
    
    if( isset($_POST['send-email'])){
        $user_email = sanitize_email($_POST['user-email']);

        $user = get_user_by('email', $user_email);
        if (!$user) {
            wp_safe_redirect( home_url( "login-opt?err=1" ) );
            exit;
        }
        
        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Send OTP via email
        $subject = 'סיסמאת התחברות חד פעמית';
        $message = 'סיסמאת התחברות חד פעמית<br>';
        $message .= 'סיסמא: ' . $otp;
        wp_mail($user_email, $subject, $message);
        
        // Store OTP in session for verification
        session_start();
        $_SESSION['otp'] = $otp;
        $_SESSION['otp-user'] = $user;

        wp_safe_redirect( home_url( "login-opt?otp=" . $user_email ) );
        exit;
    }
    
    if( isset($_POST['login-otp'])){

        session_start();
        $otp = sanitize_text_field($_POST['otp']);
        
        if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {
            
            $user = $_SESSION['otp-user'];
            unset($_SESSION['otp']);
            unset($_SESSION['otp-user']);
            
            if (!$user) {
                wp_safe_redirect( home_url( "login-opt?otp=1&err=2" ) );
                exit;
            }

            wp_set_auth_cookie( $user->ID, true );
            wp_set_current_user( $user->ID );
            wp_safe_redirect( home_url() );
            exit;

        } else {
            wp_safe_redirect( home_url( "login-opt?otp=1&err=2" ) );
            exit;
        }
    }

});

add_action( 'wp_footer', function(){
    ?>
        <script>
            if(jQuery("body").hasClass("tml-action-login")){
                jQuery(".entry-header").append("<style>.btn-hover:hover{background:#3484a5 !important}</style><a href='https://iscpdvet.com/login-opt/'><button class='btn-hover' style='background: #239ccf;font-size:2.5rem;margin-block-start:18px;display: block;'>להתחברות עם סיסמה חד פעמית למייל, לחצו כאן</button></a>");
            }
        </script>
    <?php
});

