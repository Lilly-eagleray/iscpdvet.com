<?php
/*
Plugin Name: Tranzila iFrame Gateway
Description: Tranzila iFrame payment gateway for WooCommerce, with full handling of Notify and Redirect responses to prevent automatic order cancellations and status issues.
Version: 2.8.0
Author: Pixel built
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'init_tranzila_gateway_final' ) ) {
    add_action('plugins_loaded', 'init_tranzila_gateway_final', 0);

    function init_tranzila_gateway_final() {
        if (!class_exists('WC_Payment_Gateway')) return;
        if ( class_exists( 'WC_Tranzila_Gateway_Final' ) ) return;

        class WC_Tranzila_Gateway_Final extends WC_Payment_Gateway {

            public function __construct() {
                $this->id                   = 'tranzila';
                $this->method_title         = 'Credit Card (Tranzila)';
                $this->method_description   = 'Secure payment via Tranzila iFrame. Includes automatic redirection out of the Checkout page.';
                $this->has_fields           = false;
                $this->supports             = array('products');

                $this->init_form_fields();
                $this->init_settings();

                $this->title        = $this->get_option('title', 'Credit Card');
                $this->description  = $this->get_option('description', 'Secure payment via Tranzila');

                // Hooks
                add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
                add_action('woocommerce_api_tranzila_notify', array($this, 'handle_notify'));
                add_action('woocommerce_api_tranzila_return', array($this, 'handle_browser_return'));

                // Error Display Hooks
                add_action('woocommerce_before_checkout_form', array($this, 'display_tranzila_error'));
                add_action('before_woocommerce_pay', array($this, 'display_tranzila_error'));

                add_filter('woocommerce_thankyou_order_received_text', array($this, 'custom_thank_you_text'), 10, 2);
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('admin_notices', array($this, 'display_notify_url_admin'));
            }

            public function display_notify_url_admin() {
                $screen = get_current_screen();
                if ($screen && strpos($screen->id, 'woocommerce_page_wc-settings') !== false) {
                    $notify_url = home_url( '/?wc-api=tranzila_notify' );
                    $return_url = home_url( '/?wc-api=tranzila_return' );
                    echo '<div class="notice notice-info is-dismissible"><h4>הגדרות טרנזילה נדרשות</h4>';
                    echo '<p>יש להזין את הכתובת הבאה בשדה Notify URL בממשק טרנזילה: <code>' . esc_url($notify_url) . '</code></p>';
                    echo '<p>יש להזין את הכתובת הבאה בשדה דף הצלחה (Success URL) בממשק טרנזילה: <code>' . esc_url($return_url) . '</code></p>';
                    echo '<p>יש להזין את הכתובת <strong>אותה</strong> גם בשדה דף כישלון (Fail URL): <code>' . esc_url($return_url) . '</code></p>';
                    echo '</div>';
                }
            }

            public function init_form_fields() {
                $this->form_fields = array(
                    'enabled' => array('title' => 'Enable/Disable', 'type' => 'checkbox', 'label' => 'Enable Tranzila', 'default' => 'yes'),
                    'title' => array('title' => 'Title', 'type' => 'text', 'default' => 'Credit Card'),
                    'description' => array('title' => 'Description', 'type' => 'textarea', 'default' => 'Secure payment via Tranzila'),
                    'terminal' => array('title' => 'Terminal Name', 'type' => 'text'),
                    'maxpay' => array('title' => 'Max Payments', 'type' => 'number', 'default' => '12'),
                );
            }

            public function receipt_page($order_id) {
                echo $this->generate_iframe($order_id);
            }

public function generate_iframe($order_id) {
    $order = wc_get_order($order_id);

    // בדיקה משולבת - גם POST וגם GET וגם טיפול בקידוד &amp;
    $all_params = array_merge($_GET, $_POST);

    // ניסיון לחלץ את השגיאה גם אם הפרמטר הגיע משובש בגלל קידוד
    $has_error = isset($all_params['tranzila_error']) || isset($all_params['amp;tranzila_error']);
    $response_code = '';

    if (isset($all_params['tranzila_response'])) {
        $response_code = sanitize_text_field($all_params['tranzila_response']);
    } elseif (isset($all_params['amp;tranzila_response'])) {
        $response_code = sanitize_text_field($all_params['amp;tranzila_response']);
    }

    $error_html = '';
    if ($has_error || ($response_code !== '' && !in_array($response_code, array('000', '0000')))) {
        $message = 'התשלום נכשל או בוטל. נסה שוב או בחר שיטת תשלום אחרת.';

        if ($response_code) {
            switch ($response_code) {
                case '001': $message = 'העסקה נדחתה. פנה למנפיק הכרטיס.'; break;
                case '004': $message = 'העסקה סורבה (004). יש ליצור קשר עם חברת האשראי או להשתמש בכרטיס אחר.'; break;
                case '014': $message = 'פרטי כרטיס האשראי אינם תקינים.'; break;
                case '051': $message = 'אין מספיק מסגרת או יתרה בכרטיס.'; break;
                default: $message .= ' (קוד שגיאה: ' . esc_html($response_code) . ')'; break;
            }
        }

        $error_html = '<div class="tranzila-error-box" style="background-color: #fcf2f2; border: 2px solid #d9534f; color: #d9534f; padding: 20px; margin-bottom: 25px; border-radius: 4px; font-weight: bold; text-align: right; direction: rtl; font-size: 16px;">' . $message . '</div>';
    }

    $items = array();
    foreach ($order->get_items() as $item) {
        $items[] = $item->get_name() . ' × ' . $item->get_quantity();
    }
    $pdesc = implode(' | ', $items);

    $args = array(
        'sum'         => number_format($order->get_total(), 2, '.', ''),
        'currency'    => 1,
        'cred_type'   => 8,
        'maxpay'      => (int)$this->get_option('maxpay', 12),
        'pdesc'       => mb_substr($pdesc, 0, 100, 'UTF-8'),
        'contact'     => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
        'phone'       => $order->get_billing_phone(),
        'email'       => $order->get_billing_email(),
        'orderid'     => $order_id,
        'lang'        => 'il',
    );

    $query      = http_build_query($args, '', '&');
    $terminal   = trim($this->get_option('terminal'));
    $iframe_url = 'https://direct.tranzila.com/' . $terminal . '/iframe.php?' . $query;

    ob_start();
    echo $error_html;
    ?>
    <p><strong>מעבר לעמוד תשלום מאובטח...</strong></p>
    <script type="text/javascript">
        window.addEventListener('message', function(event) {
            if (event.data === 'success' || (typeof event.data === 'string' && event.data.indexOf('order-received') > -1)) {
                window.top.location.href = '<?php echo $this->get_return_url($order); ?>';
            }
        }, false);
    </script>
    <iframe src="<?php echo esc_url($iframe_url); ?>"
            id="tranzila_iframe"
            style="width:100%; height:720px; border:none;"
            frameborder="0" allowfullscreen></iframe>
    <?php
    return ob_get_clean();
}

            public function process_payment($order_id) {
                $order = wc_get_order($order_id);
                return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url(true));
            }

            public function custom_thank_you_text($text, $order) {
                if ($order && $order->get_payment_method() === $this->id) {
                    $extra_text = '<div class="tranzila-extra-info" style="margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-right: 5px solid #007cba; background: #f9f9f9; direction: rtl; text-align: right; line-height: 1.6;">';
                    $extra_text .= '<p><strong>אישור הזמנה וחשבונית נשלחים ישירות למייל. במידה ולא התקבלו, יש לבדוק בספאם.</strong></p>';
                    $extra_text .= '<p>• כשבוע לפני תחילת קורס מעשי תישלח הודעה עם פרטים לנרשם.</p>';
                    $extra_text .= '<p>• מומלץ לסמן את מייל המרכז בשולחים הבטוחים: <a href="mailto:iscpdvet@gmail.com">iscpdvet@gmail.com</a></p>';
                    $extra_text .= '</div>';
                    return $text . $extra_text;
                }
                return $text;
            }

            public function display_tranzila_error() {
                if (isset($_GET['tranzila_error'])) {
                    $response_code = isset($_GET['tranzila_response']) ? sanitize_text_field($_GET['tranzila_response']) : '';
                    if (in_array($response_code, array('000', '0000'))) return;

                    $message = 'התשלום נכשל או בוטל. נסה שוב או בחר שיטת תשלום אחרת.';
                    if ($response_code) {
                        $message .= ' (קוד שגיאה: ' . esc_html($response_code) . ')';
                    }
                    wc_add_notice($message, 'error');
                }
            }

            public function handle_browser_return() {
                @ob_clean();
                $data = wp_unslash($_REQUEST);
                $order_id = absint($data['orderid'] ?? 0);
                $order = wc_get_order($order_id);

                if (!$order) {
                    echo '<script>window.top.location.href = "' . esc_js(wc_get_checkout_url()) . '";</script>';
                    exit;
                }

                $response_code = isset($data['Response']) ? $data['Response'] : '';

                if (in_array($response_code, array('000', '0000'))) {
                    $thankyou_url = $this->get_return_url($order);
                    echo '<script>window.top.location.href = "' . esc_js($thankyou_url) . '";</script>';
                } else {
                    $query_args = array('tranzila_error' => '1');
                    if ($response_code) {
                        $query_args['tranzila_response'] = $response_code;
                    }
                    $payment_url = add_query_arg($query_args, $order->get_checkout_payment_url(true));
                    echo '<script>window.top.location.href = "' . esc_js($payment_url) . '";</script>';
                }
                exit;
            }

            public function handle_notify() {
                @ob_clean();
                $data = wp_unslash($_REQUEST);
                if (empty($data['Response']) || empty($data['orderid'])) {
                    http_response_code(400);
                    exit('Missing parameters');
                }
                $order_id = absint(sanitize_text_field($data['orderid']));
                $order = wc_get_order($order_id);
                if (!$order) {
                    http_response_code(404);
                    exit('Order not found');
                }
                if ($order->get_payment_method() !== 'tranzila') {
                    http_response_code(400);
                    exit('Invalid payment method');
                }
                if ($order->has_status(array('processing', 'completed'))) {
                    exit('OK (already processed)');
                }

                $expected_sum = number_format($order->get_total(), 2, '.', '');
                if (isset($data['sum']) && $expected_sum !== number_format($data['sum'], 2, '.', '')) {
                    $order->update_status('failed', 'Tranzila: Amount mismatch. Sent: ' . sanitize_text_field($data['sum']) . ', Expected: ' . $expected_sum);
                    exit('Amount mismatch');
                }
                if (in_array($data['Response'], array('000', '0000'))) {
                    $tran_id = sanitize_text_field($data['ConfirmationCode'] ?? $data['index'] ?? $data['TranId'] ?? 'N/A');
                    $order->add_order_note('Tranzila payment approved. Ref: ' . $tran_id);
                    $order->payment_complete($tran_id);
                    exit('OK');
                } else {
                    $order->update_status('failed', 'Tranzila declined the transaction. Code: ' . sanitize_text_field($data['Response']) . ' (' . sanitize_text_field($data['Tempref'] ?? 'N/A') . ')');
                    exit('Failed');
                }
            }
        }

        add_filter('woocommerce_payment_gateways', function($methods) {
            $methods[] = 'WC_Tranzila_Gateway_Final';
            return $methods;
        });
    }
}
?>
