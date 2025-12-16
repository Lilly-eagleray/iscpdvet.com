<?php

add_action('plugins_loaded', 'woocommerce_mrova_payu_init', 0);
function woocommerce_mrova_payu_init(){
  if(!class_exists('WC_Payment_Gateway')) return;

  class WC_Mrova_Payu extends WC_Payment_Gateway{
    public function __construct(){
      $this -> id = 'tranzila';
      $this -> medthod_title = "Credit Card";
      $this -> has_fields = false;

       $this -> init_form_fields();
      $this -> init_settings();

      $this -> title = $this -> settings['title'];
      $this -> description = $this -> settings['description'];

      $this -> msg['message'] = "";
      $this -> msg['class'] = "";

      add_action('wp_loaded', array(&$this, 'check_payu_response'));
      add_action('woocommerce_receipt_tranzila', array(&$this, 'receipt_page'));
	  
	   if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
             } else {
                add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
            }
   }
    
   function init_form_fields(){

       $this -> form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'mrova'),
                    'type' => 'checkbox',
                    'label' => __('Enable Tranzila Payment Module.', 'mrova'),
                    'default' => 'no'),
                'terminal' => array(
                    'title' => __('terminal', 'mrova'),
                    'type' => 'text',
                    'label' => __('Enable Tranzila Payment Module.', 'mrova'),
                    'default' => ''),
                'maxpay' => array(
                    'title' => __('תשלומים', 'mrova'),
                    'type' => 'number',
                    'label' => __('כמות תשלומים בדף תשלום', 'mrova'),
                    'default' => ''),
                'title' => array(
                    'title' => __('Title:', 'mrova'),
                    'type'=> 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'mrova'),
                    'default' => __('Tranzila', 'mrova')),
                'description' => array(
                    'title' => __('Description:', 'mrova'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'mrova'),
                    'default' => __('Pay securely by Credit or Debit card or internet banking through Tranzila Secure Servers.', 'mrova')),
                
              
            );
    }

       public function admin_options(){
        echo '<h3>'.__('Credit Card', 'mrova').'</h3>';
        echo '<p>'.__('Pay with credit card').'</p>';
        echo '<table class="form-table">';
        // Generate the HTML For the settings form.
        $this -> generate_settings_html();
        echo '</table>';

    }

  
    /**
     * Receipt Page
     **/
    function receipt_page($order){
       //echo '<p>'.__('Thank you for your order, please click the button below to pay with PayU.', 'mrova').'</p>';
        echo $this -> generate_payu_form($order);
    }
    /**
     * Generate payu button link
     **/
    public function generate_payu_form($order_id){

       global $woocommerce;
    	$order = new WC_Order( $order_id );
        $order = wc_get_order($order_id);

		$items = $order->get_items();
		$desc = "";

		foreach($order->get_items() as $item) {
		$desc .= $item["name"] . "+";

		}
		
		$desc = rtrim($desc,"+");
		$desc = urlencode($desc);
		$lang = "il";
		// if(ICL_LANGUAGE_CODE == "he"){
		// 	$lang = "il";
		// }
		// else if(ICL_LANGUAGE_CODE == "en"){
		// 	$lang = "us";
		// }
		//var_dump($order);
		$data = array(
			'sum' => $order->get_total(),
			'currency'=>1,
			'pdesc' => $desc,
			'cred_type' => 8,
			'maxpay' => $this->settings['maxpay'],
			'address' => $order->billing_address_1,
			'lang'=>$lang,
			'company'=> $order->billing_company,
			'contact'=> $order->billing_first_name . " " . $order->billing_last_name,
			'country' => $order->billing_country,
			'email'=> $order->billing_email,
			'phone'=> $order->billing_phone,
			'vetid'=> get_post_meta( $order->id, '_vetid',  true ),
			'vetyear'=>  get_post_meta( $order->id, '_vetyear',  true ),
			'orderid'=>$order->id,
			
			
		);
		//print_r($order);
		$arg_to_iframe = http_build_query($data);
//echo $arg_to_iframe;		
        return '<iframe  width="560" height="560" border="0" src="https://direct.tranzila.com/'.$this->settings['terminal'].'/iframe.php?' . $arg_to_iframe . '"></iframe>';


    }
    /**
     * Process the payment and return the result
     **/
    function process_payment($order_id){
        global $woocommerce;
    	$order = new WC_Order( $order_id );
        return array('result' => 'success', 'redirect' => add_query_arg('order',
            //$order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id'))))
            $order->id, add_query_arg('key', $order->order_key, wc_get_checkout_url() ))

        );
    }

    /**
     * Check for valid payu server callback
     **/
    function check_payu_response(){
        global $woocommerce;

    }

    function showMessage($content){
            return '<div class="box '.$this -> msg['class'].'-box">'.$this -> msg['message'].'</div>'.$content;
        }
   
}
   /**
     * Add the Gateway to WooCommerce
     **/
    function woocommerce_add_mrova_payu_gateway($methods) {
        $methods[] = 'WC_Mrova_Payu';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_mrova_payu_gateway' );
}
?>