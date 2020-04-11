<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\StripeSubscriptions;
use App\Models\User;
use Auth;
use App;
use DateTime;
use Session;
use Validator;
use DB;



class SubscriptionController extends Controller
{

	public function __construct()
	{
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
		$this->otp_helper = resolve('App\Http\Helper\OtpHelper');
		$this->helper = resolve('App\Http\Start\Helpers');
    }
    
    /**
     * View subscription information for driver
     *
     * @param array $request  Input values
     * @return Static page view file
     */
	public function index(Request $request) {
        $user = User::find(@Auth::user()->id);
        
        if(!$user) {
            return redirect('signin_driver');
        }
        else{
            $subscription = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();

            $subMessage = Session::get("SubMessage");
            
            $sub_data = array(
                "subscription" => $subscription, 
                "subMessage" => $subMessage,
            );
							
	        return view('subscription.index', $sub_data);
        }
    }
    
    /**
     * Load subscription plan for Driver
     *
     * @param string $plan  Plan id of driver's subscription
     * @return Static page view file
     */
	public function getSubscriptionPlan($plan) {
        $user = User::find(@Auth::user()->id);
        
        if(!$user) {
            return redirect('signin_driver');
        }
        else{
            $subscription = StripeSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();

            if(!$subscription){
                return view('subscription.subplan')->with(compact("plan"));
            }
	    	else{
                return redirect('subscription');
            }
        }
    }

    /**
     * Create stripe subscription for driver
     *
     * @param array $request  Input values
     * @return Json
     */
	public function createCustomer(Request $request){

        $user = User::find(@Auth::user()->id);
        
        if(!$user) {
            return redirect('signin_driver');
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

            return response()->json(['status' => '200']);
        }
    }

    /**
     * Cancel driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function cancelSubscription(Request $request) {
        $user = User::find(@Auth::user()->id);
        
        if(!$user) {
            return redirect('signin_driver');
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

            return redirect('subscription')->with( ['SubMessage' => 'Subscription cancelled successfully'] );
        }
    }

    /**
     * Change driver's stripe subscription
     *
     * @param array $request  Input values
     * @return Json
     */
    public function switchSubscription(Request $request) {
        $user = User::find(@Auth::user()->id);
        
        if(!$user) {
            return redirect('signin_driver');
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
                
            return redirect('subscription')->with( ['SubMessage' => 'You have changed your subscription to <b>' . $type . "</b> successfully"] );
        }
    }
}