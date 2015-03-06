<?php
/**
 *  Copyright (C) Digito.cz, Digito Proprietary License
 * */
class ControllerPaymentBCPPayment extends Controller {
	public function index() {
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

	public function callback() {
    $inputData = file_get_contents('php://input');
    $payResponse = json_decode($inputData);

    //callback password
    if(($callbackPass = $this->config->get('bcp_payment_password'))!= NULL){
      $paymentHeaders = getallheaders();
      $digest =  $paymentHeaders["Bpsignature"];

      $hashMsg = $inputData . $callbackPass;
      $checkDigest = hash('sha256', $hashMsg);

      if (strcmp($digest, $checkDigest) == 0){
        $security = 1;
      }
      else{
        $security = 0;
      }
    }
    else{
      $security = 1;
    }

    //payment status
    $paymentStatus = $payResponse -> status;

    //order id
    $preOrderId = json_decode($payResponse -> reference);
    $orderId =  $preOrderId -> order_number;

    //confirmation process
    $this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($orderId);

    if ($order_info && $security) {


    if ($paymentStatus != NULL) {
				$order_status_id = $this->config->get('config_order_status_id');

				switch($paymentStatus) {
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

      if (!$order_info['order_status_id']) {
					$this->model_checkout_order->addOrderHistory($orderId, $order_status_id);
				} else {
					$this->model_checkout_order->addOrderHistory($orderId, $order_status_id);
				}
			} else {
				$this->model_checkout_order->addOrderHistory($orderId, $this->config->get('config_order_status_id'));
			}

    }
	}
  public function paysend() {

    //Getting API-ID from config
    $apiID = $this->config->get('bcp_payment_api');

    //test mode check
    $testMode = 0; //if set to 1, test mode will be set
    if (!$testMode) {
			$payurl = 'https://www.bitcoinpay.com/api/v1/payment/btc';
		} else {
			$payurl = 'https://bitcoinpaycom.apiary-mock.com/api/v1/payment/btc';
		}

    //data preparation
    $this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

    $price = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
    $idoforder = $order_info['order_id'];
    $cname = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
    $csurname = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
    $cnamecomplete = "{$cname} {$csurname}";
    $cemail = html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8');

    //additional customer data
    $customData = array(
        'customer_name' => $cnamecomplete,
        'order_number' => intval($idoforder),
        'customer_email' => $cemail
    );
    $jCustomData = json_encode($customData);

    //data packing
    //additional checks
    $notiEmail = $this->config->get('bcp_payment_email');
    $lang = $this->session->data['language'];
    $settCurr = $this->config->get('bcp_payment_currency');

    if(strlen($settCurr)!=3){
      $settCurr = "BTC";
    }

    $postData = array(
        'settled_currency' => $settCurr,
        'return_url' => $this->url->link('payment/bcp_payment/return_url'),
        'notify_url' => $this->url->link('payment/bcp_payment/callback', '', 'SSL'),
        'price' => floatval($price),
        'currency' => $order_info['currency_code'],
        'reference' => json_decode($jCustomData)
    );

    if (($notiEmail !== NULL) && (strlen($notiEmail) > 5)){
        $postData['notify_email'] = $notiEmail;
        }
    if ((strcmp($lang, "cs") !== 0)||(strcmp($lang, "en") !== 0)||(strcmp($lang, "de") !== 0)){
        $postData['lang'] = "en";
    }
    else{
        $postData['lang'] = $lang;
    }

    $content = json_encode($postData);

    //sending data via cURL
    $curlheaders = array(
    "Content-type: application/json",
    "Authorization: Token {$apiID}",
    );
    $curl = curl_init($payurl);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,$curlheaders);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //bypassing ssl verification, because of bad compatibility
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);


    //sending to server, and waiting for response
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $jHeader = substr($response, 0, $header_size);
    $jBody = substr($response, $header_size);

    $jHeaderArr = $this -> get_headers_from_curl_response($jHeader);

    //http response code
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    //callback password check
    if(($callbackPass = $this->config->get('bcp_payment_password'))!= NULL){
      $digest =  $jHeaderArr[0]["BPSignature"];


      $hashMsg = $jBody . $callbackPass;
      $checkDigest = hash('sha256', $hashMsg);

      if (strcmp($digest, $checkDigest) == 0){
        $security = 1;
      }
      else{
        $security = 0;
      }
    }
    else{
      $security = 1;
    }

    if ( $status != 200 ) {
        die("Error: call to URL {$payurl} failed with status {$status}, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl) . "<br /> Please contact shop administrator...");
        curl_close($curl);
    }
    elseif(!$security){
      die("Error: Callback password does not match! <br />Please contact shop administrator...");
      curl_close($curl);
    }
    else{
      curl_close($curl);

      $response = json_decode($jBody);

      //adding paymentID to payment method
      $BCPPaymentId = $response -> data -> payment_id;
      $BCPInvoiceUrl = "<br>Invoice: https://bitcoinpay.com/en/sci/invoice/btc/". $BCPPaymentId;
      //$prePaymentMethod = html_entity_decode($order_info['payment_method'], ENT_QUOTES, 'UTF-8');
      $finPaymentMethod = "BitcoinPay". "<br>PaymentID: " . $BCPPaymentId . $BCPInvoiceUrl;

      $paymentQuery = $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $finPaymentMethod . "' WHERE `order_id` = " . $order_info['order_id']);

      //redirect to pay gate
      $paymentUrl = $response -> data -> payment_url;
      header("Location: {$paymentUrl}");
      die();
    }
  }

  private function get_headers_from_curl_response($headerContent){
        $headers = array();

        // Split the string on every "double" new line.
        $arrRequests = explode("\r\n\r\n", $headerContent);

        // Loop of response headers. The "count() -1" is to
        //avoid an empty row for the extra line break before the body of the response.
        for ($index = 0; $index < count($arrRequests) -1; $index++) {

            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
            {
                if ($i === 0)
                    $headers[$index]['http_code'] = $line;
                else
                {
                    list ($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return $headers;
    }
    public function return_url(){
      $returnStatus = $this->request->get['bitcoinpay-status'];

      if(strcmp($returnStatus,"true") == 0)
        $this->load->language('checkout/success');
      elseif(strcmp($returnStatus,"received") == 0)
        $this->load->language('payment/bcp_payment_received');
      elseif(strcmp($returnStatus,"cancel") == 0)
        $this->load->language('payment/bcp_payment_cancel');
      else
        $this->load->language('payment/bcp_payment_fail');


      if (isset($this->session->data['order_id'])) {
    			$this->cart->clear();

    			// Add to activity log
    			$this->load->model('account/activity');

    			if ($this->customer->isLogged()) {
    				$activity_data = array(
    					'customer_id' => $this->customer->getId(),
    					'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
    					'order_id'    => $this->session->data['order_id']
    				);

    				$this->model_account_activity->addActivity('order_account', $activity_data);
    			} else {
    				$activity_data = array(
    					'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
    					'order_id' => $this->session->data['order_id']
    				);

    				$this->model_account_activity->addActivity('order_guest', $activity_data);
    			}


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

		$data['heading_title'] = $this->language->get('heading_title');

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
		}
    }
}
?>