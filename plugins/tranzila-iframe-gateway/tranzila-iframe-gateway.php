<?php
/*
Plugin Name: Tranzila iFrame Gateway
Description: Tranzila iFrame payment gateway for WooCommerce, with full handling of Notify and Redirect responses to prevent automatic order cancellations and status issues.
Version: 2.1.0
Author: Pixel built
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// ----------------------------------------------------------------------
// IMPORTANT: Ensure any previous code using these names is disabled or removed!
// ----------------------------------------------------------------------

if ( ! function_exists( 'init_tranzila_gateway_final' ) ) {
    add_action('plugins_loaded', 'init_tranzila_gateway_final', 0);

    function init_tranzila_gateway_final() {
        // Check if WooCommerce is active
        if (!class_exists('WC_Payment_Gateway')) {
            return;
        }

        // Check if the class is already defined
        if ( class_exists( 'WC_Tranzila_Gateway_Final' ) ) {
            return;
        }

        class WC_Tranzila_Gateway_Final extends WC_Payment_Gateway {

            public function __construct() {
                $this->id                   = 'tranzila';
                $this->method_title         = 'Credit Card (Tranzila)';
                $this->method_description   = 'Secure payment via Tranzila iFrame. Please ensure the Notify URL is configured in Tranzila settings.';
                $this->has_fields           = false;
                $this->supports             = array('products');

                $this->init_form_fields();
                $this->init_settings();

                $this->title        = $this->get_option('title', 'Credit Card');
                $this->description  = $this->get_option('description', 'Secure payment via Tranzila');

                // --- Hooks ---

                // Display the iFrame on the payment page
                add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

                // Handle the Notify (Server-to-server communication)
                // The generated URL: https://[YOUR_DOMAIN]/?wc-api=tranzila_notify
                add_action('woocommerce_api_tranzila_notify', array($this, 'handle_notify'));
                
                // Handle return after payment (Return URL)
                add_action('woocommerce_thankyou_' . $this->id, array($this, 'handle_return'));

                // Save settings
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

                // Display the Notify URL in admin (for easy setup)
                add_action('woocommerce_email_before_order_table', array($this, 'display_notify_url_admin'), 5);
            }
            
            // --- Helper Functions and UI ---
            
            // Removed debug_log function as requested.
            
            public function display_notify_url_admin() {
                // Only for admin display, to easily find the URL
                if (is_admin()) {
                    $notify_url = home_url( '/?wc-api=tranzila_notify' );
                    echo '<div class="notice notice-info is-dismissible">';
                    echo '<h4>Tranzila Configuration Required!</h4>';
                    echo '<p><strong>Notify URL:</strong> You must enter this address in the <strong>"Notify URL for external consumers"</strong> field in your Tranzila iFrame settings:</p>';
                    echo '<p><code>' . esc_url($notify_url) . '</code></p>';
                    echo '</div>';
                }
            }


            public function init_form_fields() {
                $this->form_fields = array(
                    'enabled' => array(
                        'title'     => 'Enable/Disable',
                        'type'      => 'checkbox',
                        'label'     => 'Enable Tranzila Payment Gateway',
                        'default'   => 'yes'
                    ),
                    'title' => array(
                        'title'         => 'Title',
                        'type'          => 'text',
                        'default'       => 'Credit Card'
                    ),
                    'description' => array(
                        'title'     => 'Description',
                        'type'      => 'textarea',
                        'default'   => 'Secure payment via Tranzila'
                    ),
                    'terminal' => array(
                        'title'         => 'Terminal Name',
                        'type'          => 'text',
                        'description'   => 'E.g.: cpdvet2022',
                        'desc_tip'      => true,
                    ),
                    'maxpay' => array(
                        'title'         => 'Max Payments',
                        'type'          => 'number',
                        'default'       => '12',
                        'desc_tip'      => true,
                    ),
                );
            }

            public function receipt_page($order_id) {
                echo '<p><strong>Loading secure Tranzila payment form...</strong></p>';
                echo $this->generate_iframe($order_id);
            }

            public function generate_iframe($order_id) {
                $order = wc_get_order($order_id);

                // Create a short description for the transaction in Tranzila
                $items = array();
                foreach ($order->get_items() as $item) {
                    $items[] = $item->get_name() . ' × ' . $item->get_quantity();
                }
                $pdesc = implode(' | ', $items);

                $args = array(
                    'sum'       => number_format($order->get_total(), 2, '.', ''),
                    'currency'  => 1,
                    'cred_type' => 8,
                    'maxpay'    => (int)$this->get_option('maxpay', 12),
                    'pdesc'     => mb_substr($pdesc, 0, 100, 'UTF-8'),
                    'contact'   => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
                    'phone'     => $order->get_billing_phone(),
                    'email'     => $order->get_billing_email(),
                    'orderid'   => $order_id,
                    'lang'      => 'il',
                );

                $query      = http_build_query($args, '', '&');
                $terminal   = trim($this->get_option('terminal'));
                $iframe_url = 'https://direct.tranzila.com/' . $terminal . '/iframe.php?' . $query;

                return '<iframe src="' . esc_url($iframe_url) . '" 
                                style="width:100%; height:720px; border:none;" 
                                frameborder="0" allowfullscreen></iframe>';
            }

            public function process_payment($order_id) {
                $order = wc_get_order($order_id);
                // Redirects the customer to the receipt page, where the iFrame is displayed
                return array(
                    'result'    => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
            }
            
            // --- Handle Redirect ---
            // Handles the redirect the customer experiences after payment (less critical, but good for backup)
            public function handle_return($order_id) {
                $order = wc_get_order($order_id);
                
                // If the order is already processed, do nothing - the Notify worked.
                if ($order && ($order->has_status('processing') || $order->has_status('completed'))) {
                    return;
                }
                
                // If the order is still "pending payment", it means the customer was redirected back
                // but Notify hasn't arrived/failed yet. We use GET data as backup.
                if (!empty($_GET['Response']) && !empty($_GET['orderid']) && $_GET['Response'] === '000') {
                    $this->handle_notify(); // Trigger the Notify function to update the status
                    wp_redirect($this->get_return_url($order));
                    exit;
                }
            }
            
            // --- Handle Notify (The Critical Part) ---

            public function handle_notify() {
                // Prevent HTML output before headers
                @ob_clean();
                
                // Use $_REQUEST to support both GET (Redirect) and POST (Notify)
                $data = wp_unslash($_REQUEST);
                
                // --- Validation and Order Closure ---

                // 1. Check essential data
                if (empty($data['Response']) || empty($data['orderid'])) {
                    http_response_code(400);
                    exit('Missing parameters');
                }

                $order_id = absint($data['orderid']);
                $order    = wc_get_order($order_id);

                // 2. Check Order
                if (!$order || $order->get_payment_method() !== 'tranzila') {
                    http_response_code(404);
                    exit('Order not found');
                }

                // 3. Prevent double processing
                if ($order->has_status(array('processing', 'completed'))) {
                    exit('OK (already processed)');
                }
                
                // 4. Validate amount (basic security requirement)
                $expected_sum = number_format($order->get_total(), 2, '.', '');
                if (isset($data['sum']) && $expected_sum !== number_format($data['sum'], 2, '.', '')) {
                    $order->update_status('failed', 'Tranzila: Amount mismatch. Sent: ' . $data['sum'] . ', Expected: ' . $expected_sum);
                    exit('Amount mismatch');
                }

                // 5. Check payment status
                if (in_array($data['Response'], array('000', '0000'))) {
                    // Payment approved

                    $tran_id = sanitize_text_field($data['ConfirmationCode'] ?? $data['index'] ?? $data['TranId'] ?? 'N/A');

                    $order->add_order_note('Tranzila payment approved. Ref: ' . $tran_id);

                    // Update status to "Processing"
                    $order->payment_complete($tran_id);

                    exit('OK');
                }

                // 6. Payment failed/declined
                $order->update_status('failed', 'Tranzila declined the transaction. Code: ' . $data['Response'] . ' (' . ($data['Tempref'] ?? 'N/A') . ')');
                exit('Failed');
            }
        }

        // Add the gateway to WooCommerce payment methods
        add_filter('woocommerce_payment_gateways', function($methods) {
            $methods[] = 'WC_Tranzila_Gateway_Final';
            return $methods;
        });
    }
}
?>