<?php

/**
 * Paypal Payout Service
 *
 * @package     Gofer
 * @subpackage  Services\Payouts
 * @category    Paypal
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
*/

namespace App\Services\Payouts;

use App\Models\Country;

class PaypalPayout
{
	/**
     * Constructor
     *
     */	
    public function __construct()
    {
        $paymode  = payment_gateway('mode','Paypal');
        $environment = ($paymode == 'sandbox') ? 'sandbox' : '';

        $this->base_url = "https://api.$environment.paypal.com/v1/";
    }

    protected function getCurrentUser()
    {
        if(isApiRequest()) {
            return \JWTAuth::parseToken()->authenticate();
        }
        return auth()->user();
    }

    public function validateRequest($request)
    {
        $rules = array(
            'country'       => 'required',
            'email'         => 'required|email',
            'address1'      => 'required',
            'city'          => 'required',
            'postal_code'   => 'required',
        );

        $attributes = array(
            'country'    => trans('messages.profile.country'),
        );

        $messages = array(
            'required' => ':attribute '.trans('messages.home.field_is_required'),
            'email.email' => trans('messages.account.valid_email'),
            'mimes' => trans('validation.mimes', ['attribute' => trans('messages.account.legal_document'),'values' => "png,jpeg,jpg"]),
        );
        $validator = \Validator::make($request->all(), $rules, $messages,$attributes);

        if ($validator->fails()) {
            if(isApiRequest()) {
                return response()->json([
                    'status_code' => '0',
                    'status_message' => $validator->messages()->first(),
                ]);
            }
            flashMessage('danger', $validator->messages()->first());
            return back();
        }
        return false;
    }

    public function createPayoutPreference($request)
    {
        $recipient['email'] = $request->email;
        $recipient['id'] = $request->email;
    	return array(
    		'status'			=> true,
    		'recipient' 		=> arrayToObject($recipient),
    	);
    }

    protected function getAuthorizationHeader()
    {
        $client  = payment_gateway('client','Paypal');
        $secret  = payment_gateway('secret','Paypal');

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->base_url."oauth2/token");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_USERPWD, $client.":".$secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);
        
        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }
        return array('status' => true, "access_token" => $response->access_token);
    }

    protected function sendBatchRequest($pay_data,$access_token)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->base_url."payments/payouts");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pay_data); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$access_token,""));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }

        return array('status' => true, "data" => $response);
    }

    public function makePayout($payout_account,$pay_data)
    {
        try {
            $authorization = $this->getAuthorizationHeader();
            if(!$authorization['status']) {
                return array(
                    'status' => false,
                    'status_message' => $authorization['status_message'],
                );
            }

            $batch_response = $this->sendBatchRequest($pay_data,$authorization['access_token']);
            if(!$batch_response['status']) {
                return array(
                    'status' => false,
                    'status_message' => $batch_response['status_message'],
                );
            }

            $payout_response = $batch_response['data'];

            if(@$payout_response->batch_header->batch_status == "PENDING") {
                $payout_batch_id = $payout_response->batch_header->payout_batch_id;
                
                $payout_data = $this->fetchPayoutViaBatchId($payout_batch_id,$authorization['access_token']);
                if(!$payout_data['status']) {
                    return array(
                        'status' => false,
                        'status_message' => $payout_data['status_message'],
                    );
                }

                return array(
                    'status' => true,
                    'is_pending' => true,
                    'transaction_id' => $payout_batch_id,
                    'status_message' => "Payout Process initiated",
                );
            }

            return array(
                'status' => false,
                'status_message' => $payout_response->name,
            );
        }
        catch (\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function fetchPayoutViaBatchId($batch_id, $access_token = '')
    {
        if($access_token == '') {
            $authorization = $this->getAuthorizationHeader();
            if(!$authorization['status']) {
                return array(
                    'status' => false,
                    'status_message' => $authorization['status_message'],
                );
            }
            $access_token = $authorization['access_token'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->base_url."payments/payouts/$batch_id");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$access_token,""));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }

        return array('status' => true, "data" => $response);
    }

    public function fetchPayoutViaItemId($item_id, $access_token = '')
    {
        if($access_token == '') {
            $authorization = $this->getAuthorizationHeader();
            if(!$authorization['status']) {
                return array(
                    'status' => false,
                    'status_message' => $authorization['status_message'],
                );
            }
            $access_token = $authorization['access_token'];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->base_url."payments/payouts-item/$item_id");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Authorization: Bearer ".$access_token,""));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        if(isset($response->error)) {
            return array('status' => false,"status_message" => $response->error_description);
        }

        return array('status' => true, "data" => $response);
    }

    public function getPayoutStatus($payout_data)
    {
        if(!isset($payout_data->items[0])) {
            return array('status' => false, "status_message" => "Requested Payment Not Found");
        }

        return array(
            'status' => true,
            'payout_status' => $payout_data->items[0]->transaction_status,
            'transaction_id' => $payout_data->items[0]->transaction_id,
            'status_message' => "Payout Processed",
        );
    }
}