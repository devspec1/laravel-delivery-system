<?php

/**
 * Stripe Payment Repository
 *
 * @package     GoferEats
 * @subpackage  Repositories
 * @category    Stripe
 * @author      Trioangle Product Team
 * @version     1.2
 * @link        http://trioangle.com
*/

namespace App\Repositories;

class StripePayment
{
	// Set Formatted Return Data With Default Values
	protected $return_data = array('status' => 'success', 'status_message' => '','is_two_step' => false);
	/**
     * Intialize Stripe with Secret key
     *
     */	
    public function __construct()
    {
    	$stripe_key = payment_gateway('secret','Stripe');
		$api_version = payment_gateway('api_version','Stripe');
		\Stripe\Stripe::setApiKey($stripe_key);
		\Stripe\Stripe::setApiVersion($api_version);
    }

    /**
     * Create New Payment Method
     *
     * @param array $stripe_card Card Details
     *
     * @return Object $return_data With Status, error message or payment method id
     */
    public function createPaymentMethod($card_details)
    {
    	$this->resetReturnData();
    	try {
            $payment_method = \Stripe\PaymentMethod::create(array(
                "card" => $card_details,
                'type' => 'card'
            ));
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
        	$this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }

        $this->updateReturnData('id', $payment_method->id);
        return $this->getReturnData();
    }

    /**
     * attach Payment Method To Customer
     *
     * @param array $stripe_card Card Details
     *
     * @return Object $return_data With Status, error message or payment method id
     */
    public function attachPaymentToCustomer($customer_id,$payment_method_id)
    {
        $this->resetReturnData();
        try {
            $payment_method = $this->getPaymentMethod($payment_method_id);
            $payment_method->attach([
              'customer' => $customer_id,
            ]);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }

        $this->updateReturnData('id', $payment_method->id);
        return $this->getReturnData();
    }

    /**
     * detach Payment Method To Customer
     *
     * @param array $payment_method_id Payment Method ID
     *
     * @return Object $return_data With Status, status message
     */
    public function detachPaymentToCustomer($payment_method_id)
    {
        $this->resetReturnData();
        try {
            $payment_method = $this->getPaymentMethod($payment_method_id);
            if($payment_method->status != 'failed') {
                $payment_method->detach();
            }
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }

        $this->updateReturnData('status_message', 'removed successfully');
        return $this->getReturnData();
    }

    /**
     * Create New Setup Intent
     *
     * @param String $customer_id
     *
     * @return Object $return_data With Status, error message or setup intent data
     */
    public function createSetupIntent($customer_id)
    {
    	$this->resetReturnData();
    	try {
            $setup_intent = \Stripe\SetupIntent::create([
				'payment_method_types' 	=> ['card'],
			  	'customer'				=> $customer_id,
			  	'usage'					=> 'off_session'
			]);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('intent_client_secret', '');
        	$this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }

        $this->updateReturnData('intent_id', $setup_intent->id);
        $this->updateReturnData('intent_client_secret', $setup_intent->client_secret);
        $this->updateReturnData('status', $setup_intent->status);
        return $this->getReturnData();
    }

    /**
     * attach Payment Method To Setup Intent
     *
     * @param array $setup_intent Setup Intent Id
     * @param array $payment_method Payment Method Id
     *
     * @return Object $return_data With Status, intent_id, intent_client_secret
     */
    public function attachPaymentToSetupIntent($setup_intent,$payment_method)
    {
        $this->resetReturnData();
        try {
            $setup_intent = \Stripe\SetupIntent::update(
                $setup_intent,
                ['payment_method' => $payment_method]
            );
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }

        $this->updateReturnData('intent_id', $setup_intent->id);
        $this->updateReturnData('intent_client_secret', $setup_intent->client_secret);
        $this->updateReturnData('status', $setup_intent->status);
        return $this->getReturnData();
    }

    /**
     * Create New Payment Intent
     *
     * @param array $purchaseData Related Purchase Data such as currency, amount, etc.,
     *
     * @return Object $return_data With Status, error message or payment intent id
     */
    public function createPaymentIntent($purchaseData)
    {
        $this->resetReturnData();
        try {
            $payment_intent = \Stripe\PaymentIntent::create($purchaseData);
            $this->updatePaymentIntent($payment_intent);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            if($e->getError()->code == 'authentication_required') {
                $payment_intent_id = $e->getError()->payment_intent->id;
                $this->updatePaymentIntentPaymentMethod($payment_intent_id,$purchaseData['payment_method']);
                $this->confirmPaymentIntent($payment_intent_id);
                $this->updateReturnData('is_two_step', true);
            }
            $this->updateReturnData('status_message', $e->getMessage());
        }

        return $this->getReturnData();
    }

    /**
     * Complete Payment Intent Based on Payment Intent Id
     *
     * @param String $payment_intent_id Id
     *
     * @return Object $return_data With Status, error message or transaction_id id
     */
    public function CompletePayment($payment_intent_id)
    {
        $this->resetReturnData();
        try {
            // Retrieve the PaymentIntent
            $payment_intent = $this->getPaymentIntent($payment_intent_id);
            $this->updatePaymentIntent($payment_intent);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
        }

        return $this->getReturnData();
    }

    /**
     * Get Payment Intent Object by id
     *
     * @param String $payment_intent_id Payment Intent Id
     *
     * @return Object $paymentIntent
     */
    protected function getPaymentIntent($payment_intent_id)
    {
        try {
            $intent = \Stripe\PaymentIntent::retrieve(
                $payment_intent_id
            );
            return $intent;
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }
    }

    /**
     * update Payment Intent, If Payment Intent Need confirmation then confirm payment
     *
     * @param Object $payment_intent Payment Intent
     *
     */
    protected function updatePaymentIntent($payment_intent)
    {
        if($payment_intent->status == 'succeeded') {
            $this->updateReturnData('status', 'success');
            $this->updateReturnData('transaction_id', $payment_intent->id);
            return $this->getReturnData();
        }

        $this->confirmPaymentIntent($payment_intent->id);
    }

    /**
     * update Payment Intent, If Payment Intent Need confirmation then confirm payment
     *
     * @param Object $payment_intent Payment Intent
     *
     */
    protected function confirmPaymentIntent($payment_intent_id)
    {
        $intent = $this->getPaymentIntent($payment_intent_id);
        $intent->confirm();

        $this->updatePaymentResponse($intent);
    }

    /**
     * update Payment Intent, If Payment Intent Need confirmation then confirm payment
     *
     * @param Object $payment_intent Payment Intent
     * @param String $payment_method_id Payment Method Id
     *
     */
    protected function updatePaymentIntentPaymentMethod($payment_intent,$payment_method_id)
    {
        $update_data = array(
            'payment_method' => $payment_method_id
        );
        try {
            \Stripe\PaymentIntent::update($payment_intent,$update_data);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
        }
        return $this->getReturnData();
    }

    /**
     * update Payment Intent Response
     *
     * @param Object $intent Stripe PaymentIntentDetails
     *
     */
    protected function updatePaymentResponse($intent)
    {
        # Note that if your API version is before 2019-02-11, 'requires_action'
        # appears as 'requires_source_action'.
        if ($intent->status == 'requires_action' && $intent->next_action->type == 'use_stripe_sdk') {
            # Tell the client to handle the action
            $this->updateReturnData('status', 'requires_action');
            $this->updateReturnData('intent_client_secret', $intent->client_secret);
        }
        else if ($intent->status == 'succeeded') {
            # The payment didn’t need any additional actions and completed!
            # Handle post-payment fulfillment
            $this->updateReturnData('status', 'success');
            $this->updateReturnData('transaction_id', $intent->id);
        }
        else {
            # Invalid status
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', 'Something went wrong with Secure Payment, Please Try again later.');
        }
    }

    /**
     * Get Customer Data
     *
     * @param String $customer_id
     *
     * @return Object $customer_details
     */
    public function getCustomer($customer_id)
    {
        try {
            $customer_details = \Stripe\Customer::retrieve($customer_id);
            $this->updateReturnData('status', 'success');
            $this->updateReturnData('customer_details', $customer_details);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getError()->code);
        }
        return $this->getReturnData();
    }

    /**
     * Get Setup Intent
     *
     * @param String $intent_id
     *
     * @return Object $setup_intent
     */
    public function getSetupIntent($intent_id)
    {
    	$this->resetReturnData();
        try {
            $this->updateReturnData('status', 'success');
            $setup_intent = \Stripe\SetupIntent::retrieve($intent_id);
            return $setup_intent;
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
        }
        return $this->getReturnData();
    }

    /**
     * Get Payment Method
     *
     * @param String $method_id
     *
     * @return Object $payment_method
     */
    public function getPaymentMethod($method_id)
    {
        try{
            $payment_method = \Stripe\PaymentMethod::retrieve($method_id);
            return $payment_method;
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
            return $this->getReturnData();
        }
    }

    /**
     * create Customer with current Email
     *
     * @param String $email
     *
     * @return Object $customer
     */
    public function createCustomer($email)
    {
    	$this->resetReturnData();
        try {
            $customer = \Stripe\Customer::create(
                array(
                    "description" => "Customer for ".$email,
                )
            );
            $this->updateReturnData('customer', $customer);
            $this->updateReturnData('customer_id', $customer->id);
        }
        catch(\Exception $e) {
            $this->updateReturnData('status', 'failed');
            $this->updateReturnData('status_message', $e->getMessage());
        }
        return $this->getReturnData();
    }

    /**
     * Refund Payment
     *
     * @param String $payment_intent_id Id
     *
     * @return Object $return_data With Status, error message or transaction_id id
     */
    public function refundPayment($payment_intent_id,$amount = '')
    {
        $this->resetReturnData();
        $this->updateReturnData('status', 'failed');
        try {
            $refund_data = array('payment_intent' => $payment_intent_id);

            if($amount != '') {
                $refund_data['amount'] = $amount;
            }
            $refund = \Stripe\Refund::create($refund_data);

            if($refund->status == 'succeeded') {
                $this->updateReturnData('status', 'success');
                $this->updateReturnData('currency', strtoupper($refund->currency));
                $this->updateReturnData('intent_id', $payment_intent_id);
            }
            else {
                $this->updateReturnData('status_message', 'Refund failed : Please try again.');
            }
        }
        catch(\Exception $e) {
            $this->updateReturnData('status_message', $e->getMessage());
        }
        
        return $this->getReturnData();
    }

    /**
     * Reset Return data to default
     *
     * @return null
     */
    protected function resetReturnData()
    {
    	$this->return_data = array('status' => 'success', 'status_message' => '','is_two_step' =>false);
    }

    /**
     * Create New Payment Method
     *
     * @param String $key Key in Array
     * @param String $value Value in Array
     *
     * @return null
     */
    protected function updateReturnData($key, $value = '')
    {
    	$this->return_data[$key] = $value;
    }

    /**
     * Get Formatted Return Data
     *
     * @return Object $return_data With return_data Array 
     */
    protected function getReturnData()
    {
    	return json_decode(json_encode($this->return_data));
    }
}