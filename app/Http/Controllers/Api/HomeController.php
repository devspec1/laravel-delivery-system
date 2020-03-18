<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use JWTAuth;

class HomeController extends Controller
{
	/**
     * Get Common Data
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function commonData(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

        $site_settings = resolve('site_settings');
        $api_credentials = resolve('api_credentials');
        $payment_gateway = resolve('payment_gateway');
        $fees = resolve('fees');

        $return_data = [            
            'status_code'       => '1',
            'status_message'    => __('messages.api.listed_successfully'),
        ];

        $heat_map = $site_settings->where('name','heat_map')->first()->value;
        $heat_map = ($heat_map == 'On') ? 1:0;

        $sinch_key = $api_credentials->where('name','sinch_key')->first()->value;
        $sinch_secret_key = $api_credentials->where('name','sinch_secret_key')->first()->value;

        $braintree_env = payment_gateway('mode','Braintree');
        $braintree_public_key = payment_gateway('public_key','Braintree');

        $paypal_client = payment_gateway('client','Paypal');
        $paypal_mode = payment_gateway('mode','Paypal');
        $paypal_mode = ($paypal_mode == 'sandbox') ? 0 : 1;
        $stripe_publish_key = payment_gateway('publish','Stripe');

        $referral_settings = resolve('referral_settings');
        $referral_settings = $referral_settings->where('user_type',ucfirst($request->user_type))->where('name','apply_referral')->first();

        $enable_referral = (@$referral_settings->value == "1");

        $apply_extra_fee = @$fees->where('name','additional_fee')->first()->value;
        $apply_trip_extra_fee = ($apply_extra_fee == 'Yes');

        $admin_contact  = MANUAL_BOOK_CONTACT;
        $google_map_key = MAP_KEY;
        $fb_id          = FB_CLIENT_ID;

        $status = $user_details->status ?? 'Inactive';

        $gateway_type = "Stripe";

        $payment_details = PaymentMethod::where('user_id', $user_details->id)->first();
        $brand  = optional($payment_details)->brand ?? '';
        $last4  = (string)optional($payment_details)->last4 ?? '';

        $update_loc_interval = site_settings('update_loc_interval');
        
        $trip_default = payment_gateway('trip_default','Common');
        $wallet_default = payment_gateway('wallet_default','Common');

        $common_data = compact(
            'heat_map',
            'sinch_key',
            'sinch_secret_key',
            'apply_trip_extra_fee',
            'admin_contact',
            'status',
            'braintree_env',
            'braintree_public_key',
            'google_map_key',
            'fb_id',
            'paypal_client',
            'paypal_mode',
            'stripe_publish_key',
            'gateway_type',
            'brand',
            'last4',
            'update_loc_interval',
            'trip_default',
            'wallet_default'
        );

        $driver_data = array();
        if($user_details->user_type == 'Driver' ) {
            $payout_methods = getPayoutMethods($user_details->company_id);
            
            foreach ($payout_methods as $payout_method) {
                $payout_list[] = ["key" => $payout_method, 'value' => snakeToCamel($payout_method)];
            }

            $driver_data = compact('payout_list');
        }

        return response()->json(array_merge($return_data,$common_data,$driver_data));
    }

    /**
     * Get Payment List
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function getPaymentList(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

        $payment_methods = collect(PAYMENT_METHODS);
        $payment_methods = $payment_methods->reject(function($value) {
            $is_enabled = payment_gateway('is_enabled',ucfirst($value['key']));
            return ($is_enabled != '1');
        });

        $is_wallet = $request->is_wallet == "1";

        $default_paymode = payment_gateway('trip_default','Common');

        $payment_list = array();

        $payment_methods->each(function($payment_method) use (&$payment_list, $default_paymode, $user_details, $is_wallet) {

            if($payment_method['key'] == 'cash' && $is_wallet) {
                $skip_payment = true;
            }
            if($payment_method['key'] == 'stripe') {
                $payment_details = PaymentMethod::where('user_id', $user_details->id)->first();

                if($payment_details != '') {
                    $last4  = strval($payment_details->last4);
                    $payment_method['value'] = 'xxxx xxxx xxxx '.$last4;

                    $stripe_card = array(
                        "key"           => "stripe_card",
                        "value"         => \Lang::get('messages.api.change_debit_card'),
                        "is_default"    => false,
                        "icon"          => asset("images/icon/card.png"),
                    );
                }
                else {
                    $stripe_card = array(
                        "key"           => "stripe_card",
                        "value"         => \Lang::get('messages.api.add_debit_card'),
                        "is_default"    => true,
                        "icon"          => asset("images/icon/card.png"),
                    );
                    $skip_payment = true;
                }
            }

            if(!isset($skip_payment)) {
                $payMethodData = array(
                    "key"       => $payment_method['key'],
                    "value"     => \Lang::get('messages.api.'.$payment_method['value']),
                    "icon"      => $payment_method['icon'],
                    "is_default"=> ($default_paymode == $payment_method['key']),
                );
                array_push($payment_list, $payMethodData);
            }
            
            if(isset($stripe_card)) {
                array_push($payment_list, $stripe_card);
            }
        });

    	$return_data = array(            
            'status_code'       => '1',
            'status_message'    => __('messages.api.listed_successfully'),
            'payment_list'    => $payment_list,
        );

    	return response()->json($return_data);
    }
}