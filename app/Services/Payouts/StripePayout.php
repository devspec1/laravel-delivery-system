<?php

/**
 * Stripe Payout Service
 *
 * @package     Gofer
 * @subpackage  Services\Payouts
 * @category    Stripe
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
*/

namespace App\Services\Payouts;

use App\Models\Country;

class StripePayout
{
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

    protected function getCurrentUser()
    {
        if(isApiRequest()) {
            return \JWTAuth::parseToken()->authenticate();
        }
        if(request()->segment(1) == 'company') {
            return auth('company')->user();
        }
        return auth()->user();
    }

    public function validateRequest($request)
    {
    	$country = $request->payout_country ?? '';
        $user = $this->getCurrentUser();

        $country_data = Country::where('short_name', $country)->first();
        $status_code = "1";
        if (!$country_data) {
            $status_code = "0";
            $status_message = trans('messages.service_not_available');
        }

        if($status_code == "0") {
            if(isApiRequest()) {
                return response()->json(compact('status_code','status_message'));
            }

            flashMessage('danger', $status_message);
            return back();
        }

        $rules = array(
            'payout_country'=> 'required',
            'account_number'=> 'required',
            'address1'      => 'required',
            'city'          => 'required',
            'postal_code'   => 'required',
            'document'      => 'required|mimes:png,jpeg,jpg',
        );

        if($country == 'US') {
            $rules['ssn_last_4'] = 'required';
        }

        if($country == 'JP') {
            $rules['phone_number'] = 'required';
            $rules['bank_name'] = 'required';
            $rules['branch_name'] = 'required';
            $rules['address1'] = 'required';
            $rules['kanji_address1'] = 'required';
            $rules['kanji_address2'] = 'required';
            $rules['kanji_city'] = 'required';
            $rules['kanji_state'] = 'required';
            $rules['kanji_postal_code'] = 'required';
        }

        if (!isApiRequest()) {
            $rules['stripe_token'] = 'required';
        }

        $attributes = array(
            'payout_country'    => trans('messages.profile.country'),
        );

        $messages = array(
            'required' => ':attribute '.trans('messages.home.field_is_required'),
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
            return back()->withErrors($validator)->withInput();
        }
        return false;
    }

    protected function getVerificationData($request)
    {
        $user = $this->getCurrentUser();
    	$account_holder_type = 'individual';
    	$country = $request->payout_country;

    	if($country  == 'JP') {
            $address_kana = array(
                'line1'       	=> $request->address1,
                'town'         	=> $request->address2,
                'city'          => $request->city,
                'state'         => $request->state,
                'postal_code'   => $request->postal_code,
                'country'      => $country,
            );
            $address_kanji = array(
                'line1'         => $request->kanji_address1,
                'town'         	=> $request->kanji_address2,
                'city'          => $request->kanji_city,
                'state'         => $request->kanji_state,
                'postal_code'   => $request->kanji_postal_code,
                'country'       => $country,
            );
            $individual = array(
                "first_name_kana" 	=> $user->first_name,
                "last_name_kana" 	=> $user->last_name,
                "first_name_kanji"	=> $user->first_name,
                "last_name_kanji" 	=> $user->last_name,
                "address" => array(
                    "line1" 	=> $request->address1,
                    "line2" 	=> $request->address2 ?? null,
                    "city" 		=> $request->city,
                    "country" 	=> $country,
                    "state" 	=> $request->state ?? null,
                    "postal_code" => $request->postal_code,
                ),
                "address_kana" 	=> $address_kana,
                "address_kanji" => $address_kanji,
            );
        }
        else {
        	$individual = [ 
                "address" => array(
                    "line1" 	=> $request->address1,
                    "city" 		=> $request->city,
                    "postal_code"=> $request->postal_code,
                    "state" 	=> $request->state
                ),
                "dob" => array(
                    "day" 	=> "15",
                    "month" => "04",
                    "year" 	=> "1996",
                ),
                "first_name" 	=> $user->first_name,
                "last_name" 	=> $user->last_name,
                "phone" 		=> '+' . $user->country_code . $user->mobile_number,
                "email"			=> $user->email,
            ];

            if($country == 'US') {
                $individual['ssn_last_4'] = $request->ssn_last_4;
            }

            if(in_array($country,['SG','CA'])) {
                $individual['id_number'] =  $request->personal_id;
            }
        }

        $capability_countries = ['US','AU','AT','BE','CZ','DK','EE','FI','FR','DE','GR','IE','IT','LV','LT','LU','NL','NZ','NO','PL','PT','SK','SI','ES','SE','CH','GB'];
        $url = url('/');
        if(strpos($url, "localhost") > 0) {
        	$url = 'http://gofer.trioangle.com';
        }

        $verification = array(
          	"country" 		=> $country,
          	"business_type" => "individual",
          	"business_profile" => array(
          		'mcc' => 4121,
          		'url' => $url,
          	),
          	"tos_acceptance"=> array(
                "date" 	=> time(),
                "ip"    => $_SERVER['REMOTE_ADDR']
            ),
          	"type"    		=> "custom",
          	"individual"	=> $individual,
        );

        if(in_array($country, $capability_countries)) {
            $verification["requested_capabilities"] = ["transfers","card_payments"];
        }

        return $verification;
    }

    protected function createStripeAccount($verification)
    {
    	try {
	    	$recipient = \Stripe\Account::create($verification);
	    	return array(
	    		'status' => true,
	    		'recipient' => $recipient,
	    	);
	    }
	    catch(\Exception $e) {
        	return array(
        		'status' => false,
        		'status_message' => $e->getMessage(),
        	);
        }
    }

    public function uploadDocument($document_path,$recipient_id)
    {
        try {
            $stripe_file = \Stripe\File::create(
            	array(
                	"purpose" 	=> "identity_document",
                	"file" 		=> fopen($document_path, 'r')
              	),
              	array('stripe_account' => $recipient_id)
            );

            $stripe_document = $stripe_file->id;

            return array(
	    		'status'			=> true,
	    		'status_message' 	=> 'document uploaded',
	    		'stripe_document' 	=> $stripe_document,
	    	);
        }
        catch(\Exception $e) {
        	return array(
        		'status' => false,
        		'status_message' => $e->getMessage(),
        	);
        }
    }

    public function attachDocumentToRecipient($recipient_id,$individual_id,$document_id,$document_type)
    {
        try {
            $update_data = array(
                'verification' => [
                    $document_type => [
                       'front' => $document_id       
                    ]
                ]
            );
            \Stripe\Account::updatePerson($recipient_id,$individual_id,$update_data);
            return array(
	    		'status'			=> true,
	    		'status_message' 	=> 'document attached',
	    	);
        }
        catch(\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
    }

    public function createStripeToken($bank_account)
    {
        try {
            $stripe_token = \Stripe\Token::create(
                array("bank_account" => $bank_account),
            );
            return [
                'status' => true,
                'token'  => $stripe_token,
            ];
        }
        catch(\Exception $e) {
            return [
                'status'         => false,
                'status_message' => $e->getMessage(),
            ];
        }        
    }

    public function createPayoutPreference($request)
    {
    	$verification = $this->getVerificationData($request);
    	$recipient_data = $this->createStripeAccount($verification);
    	if(!$recipient_data['status']) {
    		return array(
	    		'status' => false,
	    		'status_message' => $recipient_data['status_message'],
	    	);
    	}
    	$recipient = $recipient_data['recipient'];
        $user = $this->getCurrentUser();
    	$recipient->email = $user->email;

        try {
            $recipient->external_accounts->create(
            	array("external_account" => $request->stripe_token)
            );
        }
        catch(\Exception $e) {
        	return array(
	    		'status' => false,
	    		'status_message' => $e->getMessage(),
	    	);
        }
        $recipient->save();

    	return array(
    		'status'			=> true,
    		'recipient' 		=> $recipient,
    	);
    }

    public function makeTransfer($pay_data)
    {
        try {
            $response = \Stripe\Transfer::create($pay_data);
        }
        catch (\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }
        return array('status' => true);
    }

    public function makePayout($payout_account,$pay_data)
    {
        try {
            $response = \Stripe\Transfer::create(array(
                "amount" => $pay_data['amount'] * 100,
                "currency" => $pay_data['currency'],
                "destination" => $payout_account,
                "source_type" => "card"
            ));
        }
        catch (\Exception $e) {
            return array(
                'status' => false,
                'status_message' => $e->getMessage(),
            );
        }

        return array(
            'status' => true,
            'status_message' => 'Payout amount has transferred successfully',
            'transaction_id' => $response->id,
        );
    }
}