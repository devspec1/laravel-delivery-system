<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Start\Helpers;
use App\Http\Helper\RequestHelper;
use App\Models\StripeSubscriptions;
use App\Models\User;
use Auth;
use App;
use DateTime;
use Session;
use Validator;
use DB;
use JWTAuth;



class SubscriptionController extends Controller
{
    // Global variable for Helpers instance
	protected $request_helper;

    public function __construct(RequestHelper $request)
    {
    	$this->request_helper = $request;
		$this->helper = new Helpers;
	}
    
    /**
     * View subscription information for driver
     *
     * @param array $request  Input values
     * @return Static page view file
     */
	public function index(Request $request) {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            $subscription = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();
            
            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.api.listed_successfully'),
                'subscription'      => $subscription, 
            );

            return response()->json($sub_data);
        }
    }

    /**
     * Create stripe subscription for driver
     *
     * @param array $request  Input values
     * @return Json
     */
	public function createCustomer(Request $request){

        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            //Set key and api_version from config (PaymentGateway)
            $stripe_skey = payment_gateway('secret','Stripe');
            $api_version = payment_gateway('api_version','Stripe');
            \Stripe\Stripe::setApiKey($stripe_skey);
            \Stripe\Stripe::setApiVersion($api_version);

            $country = $request->country;
            $card_name = $request->card_name;

            // This creates a new Customer and attaches the default PaymentMethod in one API call.
            $customer = \Stripe\Customer::create([
                'payment_method' => $request->payment_method,
                'email' => $request->email,
                'invoice_settings' => [
                'default_payment_method' => $request->payment_method
                ]
            ]);

            $plan_id = "plan_GQJPw5BNB3TXTP"; // FOUNDER -- need to move to the model

            $subscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    [
                        'plan' =>  $plan_id,
                    ],
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);


            $subscription_row = new StripeSubscriptions;
            $subscription_row->user_id      = $user->id;
            $subscription_row->stripe_id    = $subscription->id;
            $subscription_row->status       = 'subscribed';
            $subscription_row->email        = $request->email;
            $subscription_row->plan_id      = $plan_id;
            $subscription_row->plan         = 'Founder';
            $subscription_row->country      = $country;
            $subscription_row->card_name    = $card_name;   
            $subscription_row->save();         

            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.subscriptions.subscribed'),
                'subscription'      => $subscription, 
            );

            return response()->json($sub_data);
        }
    }

    /**
     * Cancel driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function cancelSubscription(Request $request) {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            //Set key and api_version from config (PaymentGateway)
            $stripe_skey = payment_gateway('secret','Stripe');
            $api_version = payment_gateway('api_version','Stripe');
            \Stripe\Stripe::setApiKey($stripe_skey);
            \Stripe\Stripe::setApiVersion($api_version);

            $subscription_row = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();

            $subscription = \Stripe\Subscription::retrieve($subscription_row->stripe_id);
            $subscription->cancel();

            $subscription_row->status = 'canceled';
            $subscription_row->save();

            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.subscriptions.cancelled'),
                'subscription'      => $subscription_row, 
            );

            return response()->json($sub_data);
        }
    }

    /**
     * Reactivate driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function reactivateSubscription(Request $request) {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            //Set key and api_version from config (PaymentGateway)
            $stripe_skey = payment_gateway('secret','Stripe');
            $api_version = payment_gateway('api_version','Stripe');
            \Stripe\Stripe::setApiKey($stripe_skey);
            \Stripe\Stripe::setApiVersion($api_version);

            $subscription_row = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();

            $subscription = \Stripe\Subscription::retrieve($subscription_row->stripe_id);
            \Stripe\Subscription::update($subscription_row->stripe_id, [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $subscription_row->plan_id,
                    ],
                ],
            ]);

            $subscription_row->save();

            $subscription_row->status = 'subscribed';
            $subscription_row->save();

            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.subscriptions.resumed'),
                'subscription'      => $subscription_row, 
            );

            return response()->json($sub_data);
        }
    }

    /**
     * Pause driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function pauseSubscription(Request $request) {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            //Set key and api_version from config (PaymentGateway)
            $stripe_skey = payment_gateway('secret','Stripe');
            $api_version = payment_gateway('api_version','Stripe');
            \Stripe\Stripe::setApiKey($stripe_skey);
            \Stripe\Stripe::setApiVersion($api_version);

            $subscription_row = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled','paused'])
                ->first();

            \Stripe\Subscription::update(
                $subscription_row->stripe_id, 
                [
                    'pause_collection' => [
                        'behavior' => 'mark_uncollectible',
                    ],
                ]
            );

            $subscription_row->status = 'paused';
            $subscription_row->save();

            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.subscriptions.paused'),
                'subscription'      => $subscription_row, 
            );

            return response()->json($sub_data);
        }
    }

    /**
     * Resume paused driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function resumeSubscription(Request $request) {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            //Set key and api_version from config (PaymentGateway)
            $stripe_skey = payment_gateway('secret','Stripe');
            $api_version = payment_gateway('api_version','Stripe');
            \Stripe\Stripe::setApiKey($stripe_skey);
            \Stripe\Stripe::setApiVersion($api_version);

            $subscription_row = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();

            \Stripe\Subscription::update(
                $subscription_row->stripe_id, 
                [
                    'pause_collection' => '',
                ]
            );           

            $subscription_row->status = 'subscribed';
            $subscription_row->save();

            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.subscriptions.resumed'),
                'subscription'      => $subscription_row, 
            );

            return response()->json($sub_data);
        }
    }


    /**
     * Change driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function switchSubscription(Request $request) {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();
        
        if(!$user) {
            return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
        }
        else{
            $subscription_row = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();
            
            $type = $subscription_row->plan;
            switch($type) {
                case "Founder":
                    $plan = "Regular";
                    $pid = "plan_GQJRSjXx14TuLc"; // -- move to the model
                break;
                case "Regular":
                    $plan = "Founder";
                    $pid = "plan_GQJPw5BNB3TXTP"; // -- move to the model
                break;
            }

            $stripe_skey = payment_gateway('secret','Stripe');
            $api_version = payment_gateway('api_version','Stripe');
            \Stripe\Stripe::setApiKey($stripe_skey);
            \Stripe\Stripe::setApiVersion($api_version);
            
            $subscription = \Stripe\Subscription::retrieve($subscription_row->stripe_id);
            \Stripe\Subscription::update($subscription_row->stripe_id, [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $pid,
                    ],
                ],
            ]);

            $subscription_row->plan_id  = $pid;
            $subscription_row->plan     = $plan;
            $subscription_row->save();
                
            $sub_data = array(
                'status_code'		=> '1',
				'status_message'	=> trans('messages.subscriptions.upgraded'),
                'subscription'      => $subscription_row, 
            );

            return response()->json($sub_data);
        }
    }
}