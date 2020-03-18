<?php

/**
 * Payment Helper
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Payment
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */
namespace App\Http\Helper;

class PaymentHelper
{
	/**
	 * Braintree Payment
	 *
	 * @param Array $payment_data [payment_data includes currency, amount]
	 * @param String $[nonce] [nonce get it from braintree gateway]
	 * @return Boolean
	 */
	public function BraintreePayment($payment_data,$nonce)
	{
		$payment_currency = site_settings('payment_currency');
		$payment_amount = currencyConvert($payment_data['currency_code'],$payment_currency,$payment_data['amount']);
		$gateway = resolve('braintree');
		try {
			$result = $gateway->transaction()->sale([
				'amount' => $payment_amount,
				'paymentMethodNonce' => $nonce,
				'options' => [
					'submitForSettlement' => True
				]
			]);
		}
		catch (\Exception $e) {
			return arrayToObject([
				'status' => false,
				'status_message' => $e->getMessage(),
			]);
		}

		$return_data['status'] = $result->success;
		$return_data['is_two_step'] = false;
		if($result->success) {
			$return_data['transaction_id'] = $result->transaction->id;
		}
		else {
			$return_data['status_message'] = $result->message;
		}
		return arrayToObject($return_data);
	}

	/**
	 * Paypal Payment
	 *
	 * @param Array $payment_data [payment_data includes currency, amount]
	 * @param String $[pay_key] [pay_key get it from paypal gateway]
	 * @return Boolean
	 */
	public function PaypalPayment($payment_data,$pay_key)
	{
		$gateway = resolve('paypal');
		try {
			$purchase_response = $gateway->fetchPurchase(['transactionReference' => $pay_key])->send();
			$transaction_id = $purchase_response->getTransactionReference() ?: '';
		}
		catch (\Exception $exception) {
			return arrayToObject([
				'status' => false,
				'status_message' => $exception->getMessage(),
			]);
		}

		return arrayToObject([
			'status' => true,
			'transaction_id' => $transaction_id,
			'is_two_step' => false,
		]);
	}

	/**
	 * Stripe Payment
	 *
	 * @param Array $payment_data [payment_data includes currency, amount]
	 * @param String $[intent_id] [intent_id get it from Stripe]
	 * @return Boolean
	 */
	public function StripePayment($payment_data,$intent_id = '')
	{
		$stripe_payment = resolve('App\Repositories\StripePayment');

		if($intent_id != '') {
			$payment_result = $stripe_payment->CompletePayment($intent_id);
		}
		else {
			$payment_result = $stripe_payment->createPaymentIntent($payment_data);
		}

		if($payment_result->status == 'success') {
			return arrayToObject([
				'status' => true,
				'is_two_step' => false,
				'transaction_id' => $payment_result->transaction_id,
			]);
		}
		else if($payment_result->is_two_step) {
			return arrayToObject([
				'status' => true,
				'is_two_step' => true,
				'status_message' => $payment_result->status,
				'two_step_id' => $payment_result->intent_client_secret,
			]);
		}

		return arrayToObject([
			'status' => false,
			'status_message' => $payment_result->status_message,
		]);
	}
}