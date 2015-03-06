<?php
/**
 *  Copyright (C) Digito.cz, Digito Proprietary License
 * */

class ControllerPaymentBCPPayment extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/bcp_payment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('bcp_payment', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');

    $data['text_edit'] = $this->language->get('text_edit');
    $data['text_bcp_payment'] = $this->language->get('text_bcp_payment');



		$data['entry_email'] = $this->language->get('entry_email');
    $data['entry_api'] = $this->language->get('entry_api');
    $data['entry_password'] = $this->language->get('entry_password');
    $data['entry_currency'] = $this->language->get('entry_currency');

    $data['entry_buttons'] = $this->language->get('entry_buttons');
    $data['entry_buttons_text'] = $this->language->get('entry_buttons_text');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

    //tabs
    $data['tab_general'] = $this->language->get('tab_general');
    $data['tab_order_status'] = $this->language->get('tab_order_status');

    //helps
    $data['help_api'] = $this->language->get('help_api');
    $data['help_password'] = $this->language->get('help_password');
    $data['help_email'] = $this->language->get('help_email');
    $data['help_currency'] = $this->language->get('help_currency');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

    //payment statuses
    $data['entry_confirmed_status'] = $this->language->get('entry_confirmed_status');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_received_status'] = $this->language->get('entry_received_status');
		$data['entry_insufficient_amount_status'] = $this->language->get('entry_insufficient_amount_status');
		$data['entry_invalid_status'] = $this->language->get('entry_invalid_status');
		$data['entry_timeout_status'] = $this->language->get('entry_timeout_status');
    $data['entry_refunded_status'] = $this->language->get('entry_refunded_status');
    $data['entry_pat_status'] = $this->language->get('entry_pat_status');

    //payment helps
    $data['help_confirmed_status'] = $this->language->get('help_confirmed_status');
		$data['help_pending_status'] = $this->language->get('help_pending_status');
		$data['help_received_status'] = $this->language->get('help_received_status');
		$data['help_insufficient_amount_status'] = $this->language->get('help_insufficient_amount_status');
		$data['help_invalid_status'] = $this->language->get('help_invalid_status');
		$data['help_timeout_status'] = $this->language->get('help_timeout_status');
    $data['help_refunded_status'] = $this->language->get('help_refunded_status');
    $data['help_pat_status'] = $this->language->get('help_pat_status');




		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

    if (isset($this->error['api'])) {
			$data['error_api'] = $this->error['api'];
		} else {
			$data['error_api'] = '';
		}

    //payout currency error
    if (isset($this->error['currency'])) {
			$data['error_currency'] = $this->error['currency'];
		}
    elseif (isset($this->error['currency_invalid'])) {
			$data['error_currency'] = $this->error['currency_invalid'];
		}
    else {
			$data['error_currency'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/bcp_payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/bcp_payment', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['bcp_payment_email'])) {
			$data['bcp_payment_email'] = $this->request->post['bcp_payment_email'];
		} else {
			$data['bcp_payment_email'] = $this->config->get('bcp_payment_email');
		}

    if (isset($this->request->post['bcp_payment_api'])) {
			$data['bcp_payment_api'] = $this->request->post['bcp_payment_api'];
		} else {
			$data['bcp_payment_api'] = $this->config->get('bcp_payment_api');
		}

    if (isset($this->request->post['bcp_payment_password'])) {
			$data['bcp_payment_password'] = $this->request->post['bcp_payment_password'];
		} else {
			$data['bcp_payment_password'] = $this->config->get('bcp_payment_password');
		}

    if (isset($this->request->post['bcp_payment_currency'])) {
			$data['bcp_payment_currency'] = $this->request->post['bcp_payment_currency'];
		} else {
			$data['bcp_payment_currency'] = $this->config->get('bcp_payment_currency');
		}


    if (isset($this->request->post['bcp_payment_confirmed_status_id'])) {
			$data['bcp_payment_confirmed_status_id'] = $this->request->post['bcp_payment_confirmed_status_id'];
		} else {
			$data['bcp_payment_confirmed_status_id'] = $this->config->get('bcp_payment_confirmed_status_id');
		}

		if (isset($this->request->post['bcp_payment_pending_status_id'])) {
			$data['bcp_payment_pending_status_id'] = $this->request->post['bcp_payment_pending_status_id'];
		} else {
			$data['bcp_payment_pending_status_id'] = $this->config->get('bcp_payment_pending_status_id');
		}

		if (isset($this->request->post['bcp_payment_received_status_id'])) {
			$data['bcp_payment_received_status_id'] = $this->request->post['bcp_payment_received_status_id'];
		} else {
			$data['bcp_payment_received_status_id'] = $this->config->get('bcp_payment_received_status_id');
		}

		if (isset($this->request->post['bcp_payment_insufficient_amount_status_id'])) {
			$data['bcp_payment_insufficient_amount_status_id'] = $this->request->post['bcp_payment_insufficient_amount_status_id'];
		} else {
			$data['bcp_payment_insufficient_amount_status_id'] = $this->config->get('bcp_payment_insufficient_amount_status_id');
		}

		if (isset($this->request->post['bcp_payment_invalid_status_id'])) {
			$data['bcp_payment_invalid_status_id'] = $this->request->post['bcp_payment_invalid_status_id'];
		} else {
			$data['bcp_payment_invalid_status_id'] = $this->config->get('bcp_payment_invalid_status_id');
		}

		if (isset($this->request->post['bcp_payment_timeout_status_id'])) {
			$data['bcp_payment_timeout_status_id'] = $this->request->post['bcp_payment_timeout_status_id'];
		} else {
			$data['bcp_payment_timeout_status_id'] = $this->config->get('bcp_payment_timeout_status_id');
		}

    if (isset($this->request->post['bcp_payment_refunded_status_id'])) {
			$data['bcp_payment_refunded_status_id'] = $this->request->post['bcp_payment_refunded_status_id'];
		} else {
			$data['bcp_payment_refunded_status_id'] = $this->config->get('bcp_payment_refunded_status_id');
		}

    if (isset($this->request->post['bcp_payment_pat_status_id'])) {
			$data['bcp_payment_pat_status_id'] = $this->request->post['bcp_payment_pat_status_id'];
		} else {
			$data['bcp_payment_pat_status_id'] = $this->config->get('bcp_payment_pat_status_id');
		}



		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['bcp_payment_geo_zone_id'])) {
			$data['bcp_payment_geo_zone_id'] = $this->request->post['bcp_payment_geo_zone_id'];
		} else {
			$data['bcp_payment_geo_zone_id'] = $this->config->get('bcp_payment_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['bcp_payment_status'])) {
			$data['bcp_payment_status'] = $this->request->post['bcp_payment_status'];
		} else {
			$data['bcp_payment_status'] = $this->config->get('bcp_payment_status');
		}

    if (isset($this->request->post['bcp_payment_buttons'])) {
			$data['bcp_payment_buttons'] = $this->request->post['bcp_payment_buttons'];
		} else {
			$data['bcp_payment_buttons'] = $this->config->get('bcp_payment_buttons');
		}

		if (isset($this->request->post['bcp_payment_sort_order'])) {
			$data['bcp_payment_sort_order'] = $this->request->post['bcp_payment_sort_order'];
		} else {
			$data['bcp_payment_sort_order'] = $this->config->get('bcp_payment_sort_order');
		}

    $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/bcp_payment.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/bcp_payment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

    if (!$this->request->post['bcp_payment_api']) {
			$this->error['api'] = $this->language->get('error_api');
		}

    if (!$this->request->post['bcp_payment_currency']) {
			$this->error['currency'] = $this->language->get('error_currency');
		}
    else {
      $user_curr = $this->request->post['bcp_payment_currency'];
      $apiID = $this->request->post['bcp_payment_api'];

      if(strlen($user_curr)!=3){
        $this->error['currency_invalid'] = $this->language->get('error_currency_format');
      }
      elseif(strlen($apiID) != 24){
          $this->error['api'] = $this->language->get('error_api_wrong');
      }
      else{
        $isValid = false;
        $settlement_url = 'https://www.bitcoinpay.com/api/v1/settlement/';

        $curlheaders = array(
        "Content-type: application/json",
        "Authorization: Token {$apiID}",
        );

        $curl = curl_init($settlement_url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,$curlheaders);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //bypassing ssl verification, because of bad compatibility

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $jHeader = substr($response, 0, $header_size);
        $jBody = substr($response, $header_size);

        //http response code
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if($status != 200){
          $this->log->write("BitCoinPay payment module API ERROR. Returned status code: " . $status);

          if($status == 401){
            $this->error['api'] = $this->language->get('error_api_wrong');
          }
          else{
            $this->error['api'] = $this->language->get('error_api_general') . "HTTP/" . $status ;
          }
          curl_close($curl);
          return !$this->error;


        }


        $answer = json_decode($jBody);
        $active_currencies = $answer -> data -> active_settlement_currencies;

        if(count($active_currencies) == 0){
          $this->error['currency_invalid'] = $this->language->get('error_currency_set');
          curl_close($curl);
          return !$this->error;
        }

        foreach ($active_currencies as $value) {
          if(strcmp($value,$user_curr)==0){
            $isValid = true;
            break;
          }
        }
        if (!$isValid){
        $valid_currencies = '';
          foreach ($active_currencies as $value) {
            $valid_currencies .= '<br/ >' . $value;
          }

          $this->error['currency_invalid'] = $this->language->get('error_currency_invalid') . $valid_currencies;
        }


        curl_close($curl);
      }
		}



    return !$this->error;
	}
}
?>