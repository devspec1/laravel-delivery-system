<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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

	public function index(Request $request) {
		//$data = array();

		if(isset(auth()->user()->id)) {
			$subscription = DB::table("subscriptions")->where([["uid", "=", auth()->user()->id], ["status", "!=", "canceled"]])->first();

			$subMessage = Session::get("SubMessage");
			

				
	        return view('subscription.index', ["subscription" => $subscription, "subMessage" => $subMessage]);
	       }
	       else {
	       	return redirect('signin_driver');
	       }
	}
	public function getSubscriptionPlan($plan) {
		if(isset(auth()->user()->id)) {
			$subscription = DB::table("subscriptions")->where([["uid", "=", auth()->user()->id], ["status", "!=", "canceled"]])->first();

			if(!$subscription)
	    		return view('subscription.subplan')->with(compact("plan"));

	    	else
	    		return redirect('subscription');
	    }
	     else {
	       	return redirect('signin_driver');
	       }
    }


	 public function createCustomer(Request $request){
        //Set key and api_version from config (PaymentGateway)
        $stripe_key = payment_gateway('secret','Stripe');
		$api_version = payment_gateway('api_version','Stripe');
		\Stripe\Stripe::setApiKey($stripe_key);
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

				$plan_id = "plan_GQJPw5BNB3TXTP"; // FOUNDER -- move to the model
				$subscription = \Stripe\Subscription::create([
				  'customer' => $customer->id,
				  'items' => [
				    [
				      'plan' =>  $plan_id,
				    ],
				  ],
				  'expand' => ['latest_invoice.payment_intent'],
				]);

				DB::table("subscriptions")->insert(["uid" => auth()->user()->id, "stripe_id" => $subscription->id, "status" => "new", "email" => $request->email, "plan_id" => $plan_id, "plan" => "Founder", 'country' => $country, 'card_name' => $card_name,  "created_at" => date("Y-m-d H:i:s")]);
				

				return response()->json(['status' => '200']);
    }

    public function cancelSubscription(Request $request) {
        //Set key and api_version from config (PaymentGateway)
    	$stripe_key = payment_gateway('secret','Stripe');
		$api_version = payment_gateway('api_version','Stripe');
		\Stripe\Stripe::setApiKey($stripe_key);
		\Stripe\Stripe::setApiVersion($api_version);

    	$sub = DB::table("subscriptions")->where([["uid", '=', auth()->user()->id], ["status", "!=", "canceled"]])->first();
    	$subscription = \Stripe\Subscription::retrieve($sub->stripe_id);
		$subscription->cancel();

		DB::table("subscriptions")->where("id", $sub->id)->update(["status" => "canceled", "updated_at" => date("Y-m-d H:i:s")]);

		return redirect('subscription')->with( ['SubMessage' => 'Subscription cancelled successfully'] );;
    }

    public function switchSubscription(Request $request) {

    	$sub = DB::table("subscriptions")->where([["uid", '=', auth()->user()->id], ["status", "!=", "canceled"]])->first();
    	$type = $sub->plan;
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

        $stripe_key = payment_gateway('secret','Stripe');
		$api_version = payment_gateway('api_version','Stripe');
        \Stripe\Stripe::setApiKey($stripe_key);
		\Stripe\Stripe::setApiVersion($api_version);
    	
        $subscription = \Stripe\Subscription::retrieve($sub->stripe_id);
        \Stripe\Subscription::update($sub->stripe_id, [
            'cancel_at_period_end' => false,
            'items' => [
            [
                'id' => $subscription->items->data[0]->id,
                'plan' => $pid,
            ],
            ],
        ]);

        DB::table("subscriptions")->where("id", $sub->id)->update(["plan_id" => $pid, "plan" => $plan, "updated_at" => date("Y-m-d H:i:s")]);
			
        return redirect('subscription')->with( ['SubMessage' => 'You have changed your subscription to <b>' . $type . "</b> successfully"] );;
    }

    
}

// Set your secret key. Remember to switch to your live secret key in production!
// See your keys here: https://dashboard.stripe.com/account/apikeys
