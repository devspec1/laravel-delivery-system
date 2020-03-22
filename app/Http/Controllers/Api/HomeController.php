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
                    $brand  = strtoupper(strval($payment_details->brand));
                    $payment_method['value'] = $brand.' xxxx xxxx xxxx '.$last4;

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
                    "value"     => $payment_method['value'],
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

    /**
     * Get FAQ array
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function faq(Request $request)
    {

        // if (!$request->timezone) {
        //     return response()->json([
        //         'status_code' => '0' , 
        //         'status_message' => 'timezone is required',
        //     ]); 
        // }
        // $timezone = isValidTimezone($request->timezone) ? $request->timezone : 'UTC';
        

        // $user_details = JWTAuth::parseToken()->authenticate();
        
        // $heat_map_hours = site_settings('heat_map_hours');
        // $date_obj = Carbon::now()->setTimezone($timezone);


         $faq_array_1 = array("question" => "What is the booking commission for drivers?" ,
                        "answer" => "Zero. Zip. Nada. Drivers earn a combination of fares, profit bonuses and residual income for their membership fee of $9.95 per week." );

         $faq_array_2 = array("question" => "When do we launch / start in my city?" ,
                        "answer" => "As a driver owned enterprise, we are in the pre-launch phase in 3 countries which means we are recruiting our base of RODO (driver owners) and complying with all the regulatory requirements in each market. Each country and city has different expected launch dates. Keep connected to learn more as and when we know." );

         $faq_array_3 = array("question" => "Who or what are RODOs?" ,
                        "answer" => "RODO is an abbreviation of Ride On Driver Owners. RODOs are member owners of The Ride On Driver Owner Group Pty Ltd (a company registered specifically for drivers). RODOs have all the freedoms to change their workload anytime earning as much as they want." );

         $faq_array_4 = array("question" => "What's the benefit of joining pre-launch?" ,
                              "answer" => "We offer RODOs that join pre-launch a range of benefits that include Founder Status, a free BLACK RIDE ON Card and the maximum earning capacity from our compensation plan including double the residuals on driver team referrals." );



        $faq_array_5 = array("question" => "What is a RODO's compensation plan?" ,
                        "answer" => "Earning opportunities at Ride On are exceptional. There are four income streams. First, all drivers will earn the usual fare income for distance and time traveled by a passenger. Secondly, all qualified RODOs can earn up to 10% from the revenue of the owners group through a monthly proﬁt share. This depends on such factors as performance, customer reviews and a number of hours they worked. Thirdly, RODOs who recruit or refer other drivers to Ride On can earn a residual income from a 1% rider on their team's booking income for as long as they continue to drive for Ride On. You can listen to the Compensation Plan here: https://tinyurl.com/w9d93e6" );


        $faq_array_6 = array("question" => "Car inspections, medical check ups, free coffee?" ,
                              "answer" => "Like all other companies operating in your market, we comply with the regulatory requirements of each country, city and state. Car inspections and medical check ups are done by the same 3rd party providers such as Red Books, Job Fit etc. If you already drive for Uber, you will already have those documents and they can be used in your application with Ride On. We don't spend money on fancy offices with free coffee and kids in black t-shirts. We would rather give that money back to our RODOs in profit bonuses. If you want the free coffee, stick to Uber." );

        $faq_array_7 = array("question" => "What is your company registration number?" ,
                              "answer" => "In Australia we trade as Ride On Driver Owners Group under ABN 21 554 054 343 Intercargo Logistics Pty Ltd. This is a 10 year old company originally incorporated by Yossi Lavy, our co-founder." );

        $faq_array_8 = array("question" => "Why did you delete my posts on Facebook?" ,
                              "answer" => "We have a zero tolerance policy for rude, whinny, trolls. If you don't like what we are doing, how we're doing it or if you feel entitled to things and feel you can dump on us and other people's dreams of building something new; you're not for us and more importantly, our customers. Thank you. Love and kisses." );

        $faq = array( $faq_array_1 , $faq_array_2 , $faq_array_3 , $faq_array_4 , $faq_array_5 , $faq_array_6 , $faq_array_7 , $faq_array_8);

        return response()->json([
            'status_code'       => '1' , 
            'status_message'    => 'Success',
            'faq'        => $faq
            ]);   
    }

}