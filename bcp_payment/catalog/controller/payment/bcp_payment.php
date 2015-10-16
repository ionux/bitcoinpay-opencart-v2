<?php
/**
 *  Copyright (C) Digito.cz, Digito Proprietary License
 **/
class ControllerPaymentBCPPayment extends Controller
{
    /**
     * @var string
     */
    private $bcpEndpoint = '/api/v1/payment/btc';

    /**
     * @var string
     */
    private $bcpTestUrl = 'https://bitcoinpaycom.apiary-mock.com';

    /**
     * @var string
     */
    private $bcpLiveUrl = 'https://www.bitcoinpay.com';

    /**
     * @var int
     */
    private $bcpTestMode = 0;

    public function index()
    {
        $this->language->load('payment/bcp_payment');
        $data['button_confirm'] = $this->language->get('button_confirm');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        if ($order_info) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bcp_payment.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/payment/bcp_payment.tpl', $data);
            } else {
                return $this->load->view('default/template/payment/bcp_payment.tpl', $data);
            }
        }
    }

    public function callback()
    {
        $inputData   = file_get_contents('php://input');
        $payResponse = json_decode($inputData);

        if ($payResponse === null) {
            die('Error: Could not decode the JSON response from the payment gateway. <br />Please contact the shop administrator...');
        }

        // callback password
        if (($callbackPass = $this->config->get('bcp_payment_password')) != null) {
            $paymentHeaders = getallheaders();
            $digest         = $paymentHeaders['Bpsignature'];
            $hashMsg        = $inputData . $callbackPass;
            $checkDigest    = hash('sha256', $hashMsg);
            $security       = (strcmp($digest, $checkDigest) == 0) ? 1: 0;
        } else {
            $security = 1;
        }

        // payment status
        $paymentStatus = $payResponse->status;

        // order id
        $preOrderId = json_decode($payResponse->reference);

        if ($preOrderId === null) {
            die('Error: Could not decode the JSON response from the payment gateway. <br />Please contact the shop administrator...');
        }

        $orderId = $preOrderId->order_number;

        // confirmation process
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($orderId);

        if ($order_info && $security) {
            if ($paymentStatus != null) {
                $order_status_id = $this->config->get('config_order_status_id');

                switch ($paymentStatus) {
                    case 'confirmed':
                        $order_status_id = $this->config->get('bcp_payment_confirmed_status_id');
                        break;
                    case 'pending':
                        $order_status_id = $this->config->get('bcp_payment_pending_status_id');
                        break;
                    case 'received':
                        $order_status_id = $this->config->get('bcp_payment_received_status_id');
                        break;
                    case 'insufficient_amount':
                        $order_status_id = $this->config->get('bcp_payment_insufficient_amount_status_id');
                        break;
                    case 'invalid':
                        $order_status_id = $this->config->get('bcp_payment_invalid_status_id');
                        break;
                    case 'timeout':
                        $order_status_id = $this->config->get('bcp_payment_timeout_status_id');
                        break;
                    case 'refund':
                        $order_status_id = $this->config->get('bcp_payment_refunded_status_id');
                        break;
                    case 'paid_after_timeout':
                        $order_status_id = $this->config->get('bcp_payment_pat_status_id');
                        break;
                }

                $this->model_checkout_order->addOrderHistory($orderId, $order_status_id);
            } else {
                $this->model_checkout_order->addOrderHistory($orderId, $this->config->get('config_order_status_id'));
            }
        }
    }

    public function paysend()
    {
        // Getting API-ID from config
        $apiID = $this->config->get('bcp_payment_api');

        // If set to 1, test mode will be set
        $payurl = ($this->bcpTestMode) ? $this->bcpTestUrl: $this->bcpLiveUrl;

        // Append the resource path
        $payurl .= $this->bcpEndpoint;

        // Data preparation
        $this->load->model('checkout/order');

        $order_info    = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $price         = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $idoforder     = intval($order_info['order_id']);
        $cname         = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
        $csurname      = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
        $cnamecomplete = $cname . ' ' . $csurname;
        $cemail        = html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8');

        // Additional customer data
        $customData = array(
            'customer_name'  => $cnamecomplete,
            'order_number'   => $idoforder,
            'customer_email' => $cemail
        );

        $jCustomData = json_encode($customData);

        // Data packing and additional checks
        $notiEmail = $this->config->get('bcp_payment_email');
        $lang      = $this->session->data['language'];
        $settCurr  = $this->config->get('bcp_payment_currency');

        if (strlen($settCurr) != 3) {
            $settCurr = 'BTC';
        }

        $postData = array(
            'settled_currency' => $settCurr,
            'return_url'       => $this->url->link('payment/bcp_payment/return_url'),
            'notify_url'       => $this->url->link('payment/bcp_payment/callback', '', 'SSL'),
            'price'            => floatval($price),
            'currency'         => $order_info['currency_code'],
            'reference'        => json_decode($jCustomData)
        );

        if (($notiEmail !== null) && (strlen($notiEmail) > 5)) {
            $postData['notify_email'] = $notiEmail;
        }

        if ((strcmp($lang, "cs") !== 0) || (strcmp($lang, "en") !== 0) || (strcmp($lang, "de") !== 0)) {
            $postData['lang'] = 'en';
        } else{
            $postData['lang'] = $lang;
        }

        $content = json_encode($postData);

        // Sending data via cURL
        $curlheaders = array(
            'Content-type: application/json',
            'Authorization: Token ' . $apiID,
        );

        $curl = curl_init($payurl);

        if (empty($curl)) {
            die('Error: could not initialize cURL.<br />Please contact the shop administrator...');
        }

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheaders);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //bypassing ssl verification, because of bad compatibility
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        //sending to server, and waiting for response
        $response = curl_exec($curl);

        if ($response === null) {
            die('Error: call to URL ' . $payurl . ' failed with cURL error "' . curl_error($curl) . '".<br />Please contact the shop administrator...');
        }

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $jHeader     = substr($response, 0, $header_size);
        $jBody       = substr($response, $header_size);
        $jHeaderArr  = $this->get_headers_from_curl_response($jHeader);

        //http response code
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        //callback password check
        if (($callbackPass = $this->config->get('bcp_payment_password')) != null) {
            $digest        =  $jHeaderArr[0]["BPSignature"];
            $hashMsg       = $jBody . $callbackPass;
            $checkDigest   = hash('sha256', $hashMsg);
            $security      = (strcmp($digest, $checkDigest) == 0) ? 1: 0;
        } else {
            $security = 1;
        }

        curl_close($curl);

        if ($status != 200) {
            die('Error: call to URL ' . $payurl . ' failed with status ' . $status . ', curl_error ' . curl_error($curl) . ', curl_errno ' . curl_errno($curl) . '<br /> Please contact the shop administrator...');
        } else if (!$security) {
            die('Error: Callback password does not match! <br />Please contact the shop administrator...');
        }

        $response = json_decode($jBody);

        if ($response === null) {
            die('Error: Could not decode the JSON response from the payment gateway. <br />Please contact the shop administrator...');
        }

        // Adding paymentID to payment method
        $BCPPaymentId     = $response->data->payment_id;
        $BCPInvoiceUrl    = '<br />Invoice: https://bitcoinpay.com/en/sci/invoice/btc/' . $BCPPaymentId;
        $finPaymentMethod = 'BitcoinPay' . '<br />PaymentID: ' . $BCPPaymentId . '<br />InvoiceURL: ' . $BCPInvoiceUrl;
        $paymentQuery     = $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $finPaymentMethod . "' WHERE `order_id` = " . $order_info['order_id']);

        // Redirect to pay gate
        $paymentUrl = $response->data->payment_url;
        header('Location: ' . $paymentUrl);
        exit;
    }

    private function get_headers_from_curl_response($headerContent)
    {
        $headers = array();

        // Split the string on every "double" new line.
        $arrRequests = explode("\r\n\r\n", $headerContent);

        // Loop of response headers. The "count() -1" is to
        // avoid an empty row for the extra line break before the body of the response.
        for ($index = 0; $index < count($arrRequests) -1; $index++) {
            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
                if ($i === 0) {
                    $headers[$index]['http_code'] = $line;
                } else {
                    list ($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return $headers;
    }

    public function return_url()
    {
        $returnStatus = strtolower(trim($this->request->get['bitcoinpay-status']));

        switch ($returnStatus) {
            case 'true':
                $this->load->language('checkout/success');
                break;
            case 'received':
                $this->load->language('payment/bcp_payment_received');
                break;
            case 'cancel':
                $this->load->language('payment/bcp_payment_cancel');
                break;
            default:
                $this->load->language('payment/bcp_payment_fail');
        }
        
        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();

            // Add to activity log
            $this->load->model('account/activity');
            $this->isCustomerLoggedBcpActivity();

            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
            unset($this->session->data['totals']);
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_basket'),
            'href' => $this->url->link('checkout/cart')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('checkout/checkout', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_success'),
            'href' => $this->url->link('checkout/success')
        );

        $data['heading_title']   = $this->language->get('heading_title');
        $data['text_message']    = $this->isCustomerLoggedBcpData();
        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue']        = $this->url->link('common/home');
        $data['column_left']     = $this->load->controller('common/column_left');
        $data['column_right']    = $this->load->controller('common/column_right');
        $data['content_top']     = $this->load->controller('common/content_top');
        $data['content_bottom']  = $this->load->controller('common/content_bottom');
        $data['footer']          = $this->load->controller('common/footer');
        $data['header']          = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
        }
    }

    private function isCustomerLoggedBcpData()
    {
        if ($this->customer->isLogged()) {
            return sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
        }

        return sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
    }

    private function isCustomerLoggedBcpActivity()
    {
        if ($this->customer->isLogged()) {
            $this->model_account_activity->addActivity(
                'order_account',
                array(
                    'customer_id' => $this->customer->getId(),
                    'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
                    'order_id'    => $this->session->data['order_id']
                )
            );
        } else {
            $this->model_account_activity->addActivity(
                'order_guest',
                array(
                    'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
                    'order_id' => $this->session->data['order_id']
                )
            );
        }
    }
}
