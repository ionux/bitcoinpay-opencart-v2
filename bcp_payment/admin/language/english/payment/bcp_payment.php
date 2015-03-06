<?php
/**
 *  Copyright (C) Digito.cz, Digito Proprietary License
 * */
// Heading
$_['heading_title']					 = 'BitcoinPay Payment';

// Text
$_['text_payment']					 = 'Payment';
$_['text_success']					 = 'Success: You have modified BitcoinPay payment details!';
$_['text_edit']              = 'Edit BitcoinPay';
$_['text_bcp_payment']				 = '<a target="_BLANK" href="https://bitcoinpay.com"><img src="view/image/payment/bitcoinpay-small.jpg" alt="BitcoinPay payment" title="BitcoinPay payment" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_authorization']			 = 'Authorization';
$_['text_sale']						 = 'Sale';

// Entry
$_['entry_api']					 = 'API key:';
$_['entry_password']     = 'Callback password:';
$_['entry_email']				 = 'E-Mail:';
$_['entry_currency']		 = 'Payout currency:';

$_['entry_buttons']					 = 'Button:';
$_['entry_buttons_text']		 = 'only text';
$_['entry_geo_zone']				 = 'Geo Zone:';
$_['entry_status']					 = 'Status:';
$_['entry_sort_order']				 = 'Sort Order:';

// Statusy
$_['entry_confirmed_status'] = 'Confirmed Status';
$_['entry_pending_status']		 = 'Pending Status';
$_['entry_received_status']			 = 'Received Status';
$_['entry_insufficient_amount_status']			 = 'Insufficient Amount';
$_['entry_invalid_status']			 = 'Invalid';
$_['entry_timeout_status']			 = 'Timeout Status';
$_['entry_refunded_status']			 = 'Refunded';
$_['entry_pat_status']			 = 'Paid after timeout';

//Status help
$_['help_confirmed_status'] = 'This is THE ONLY payment status, you can consider as final. Payment is credited into balance and will be settled';
$_['help_pending_status']		 = 'Waiting for payment';
$_['help_received_status']			 = 'Payment has been received but not confirmed yet';
$_['help_insufficient_amount_status']			 = 'Customer sent amount lower than required. Customer can ask for the refund directly from the invoice url';
$_['help_invalid_status']			 = 'An error has occured';
$_['help_timeout_status']			 = 'Payment has not been paid in given time period and has expired';
$_['help_refunded_status']			 = 'Payment has been returned to customer';
$_['help_pat_status']			 = 'Payment has been paid too late. Customer can ask for refund directly from the invoice url';

// Tab
$_['tab_general']					 = 'General';
$_['tab_order_status']     = 'Order Status';

// Help
$_['help_api']			    = 'API key is used for backed authentication and you should keep it private. You will find your API key in your account under settings > API';
$_['help_password']				= 'We recommend using a callback password. It is used as a data validation for stronger security. Callback password can be set under Settings > API in your account at BitcoinPay.com';
$_['help_email']			    = 'Email where notifications about Payment changes are sent.';
$_['help_currency']				= 'Currency of settlement. You must first set a payout for currency in your account Settings > Payout in your account at BitcoinPay.com. If the currency is not set in payout, the request will return an error.';


// Errors
$_['error_permission']				 = 'Warning: You do not have permission to modify this payment module!';
$_['error_email']					 = 'E-Mail required!';
$_['error_api']					 = 'API key required!';
$_['error_api_wrong']					 = 'API key is not valid!';
$_['error_api_general']					 = 'Genaral API ERROR, please refer to support, with error code: ';
$_['error_currency']					 = 'Payout currency required!';
$_['error_currency_invalid']					 = 'Bad currency... Folowing currencies are supported:';
$_['error_currency_format']					 = 'Bad currency format, use 3 letters';
$_['error_currency_set']					 = 'You dont have any currency added. You need to add at least one payout in your account. Go to your BitcoinPay account Settings > Payout add the payout account.';
?>