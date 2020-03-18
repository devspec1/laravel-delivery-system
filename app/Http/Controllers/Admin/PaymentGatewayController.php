<?php

/**
 * Payment Gateway Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Payment Gateway
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    /**
     * Load View and Update Payment Gateway Data
     *
     * @return redirect to payment_gateway
     */
    public function index(Request $request)
    {
        if($request->isMethod('GET')) {
            return view('admin.payment_gateway');
        }

        $paypal_rules = array();
        $stripe_rules = array();
        $bt_rules = array();

        if($request->paypal_enabled) {
            $paypal_rules = array(
                'paypal_id'         => 'required',
                'paypal_mode'       => 'required',
                'paypal_client'     => 'required',
                'paypal_secret'     => 'required',
            );
        }
        if($request->stripe_enabled) {
            $stripe_rules = array(
                'stripe_publish_key'=> 'required',
                'stripe_secret_key' => 'required',
                'stripe_api_version' => 'required',
            );
        }
        if($request->bt_enabled) {
            $bt_rules = array(
                'bt_mode'           => 'required',
                'bt_merchant_id'    => 'required',
                'bt_public_key'     => 'required',
                'bt_private_key'    => 'required',
            );
        }
        $rules = array_merge($paypal_rules,$stripe_rules,$bt_rules);

        // Payment Gateway Validation Custom Names
        $attributes = array(
            'paypal_id'         => 'PayPal ID',
            'paypal_mode'       => 'PayPal Mode',
            'paypal_client'     => 'PayPal Client',
            'paypal_secret'     => 'PayPal Secret',
            'stripe_publish_key'=> 'Stripe Publish Key',
            'stripe_secret_key' => 'Stripe Secret Key',
            'stripe_api_version'=> 'Stripe API Version',
            'bt_mode'           => 'Payment Mode',
            'bt_merchant_id'    => 'Merchant ID',
            'bt_public_key'     => 'Public Key',
            'bt_private_key'    => 'Private Key',
        );

        if($request->stripe_enabled && $request->bt_enabled) {
            flashMessage('danger', 'Please Choose either Braintree or Stripe for Card Payments');
            return back();
        }

        if($request->stripe_enabled == '0' && $request->bt_enabled == '0' && $request->paypal_enabled == '0') {
            flashMessage('danger', 'Please enable atleast One Payment Gateway');
            return back();
        }

        if($request->payout_methods == '') {
            flashMessage('danger', 'Atleast One payout method should be enabled.');
            return back();
        }

        $this->validate($request, $rules, [], $attributes);

        $default_payments = array(
            payment_gateway('trip_default','Common'),
        );

        if($request->paypal_enabled == "0" && in_array('paypal',$default_payments)) {
            flashMessage('danger', 'Unable to Disable Paypal. Because this is default payment method');
            return back();
        }

        if($request->stripe_enabled == "0" && in_array('stripe',$default_payments)) {
            flashMessage('danger', 'Unable to Disable Stripe. Because this is default payment method');
            return back();
        }
        
        if($request->bt_enabled == "0" && in_array('braintree',$default_payments)) {
            flashMessage('danger', 'Unable to Disable Braintree. Because this is default payment method');
            return back();
        }
        
        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Paypal'])->update(['value' => $request->paypal_enabled]);
        PaymentGateway::where(['name' => 'paypal_id', 'site' => 'Paypal'])->update(['value' => $request->paypal_id]);
        PaymentGateway::where(['name' => 'mode', 'site' => 'Paypal'])->update(['value' => $request->paypal_mode]);
        PaymentGateway::where(['name' => 'client', 'site' => 'Paypal'])->update(['value' => $request->paypal_client]);
        PaymentGateway::where(['name' => 'secret', 'site' => 'Paypal'])->update(['value' => $request->paypal_secret]);

        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Stripe'])->update(['value' => $request->stripe_enabled]);
        PaymentGateway::where(['name' => 'publish', 'site' => 'Stripe'])->update(['value' => $request->stripe_publish_key]);
        PaymentGateway::where(['name' => 'secret', 'site' => 'Stripe'])->update(['value' => $request->stripe_secret_key]);
        PaymentGateway::where(['name' => 'api_version', 'site' => 'Stripe'])->update(['value' => $request->stripe_api_version]);

        PaymentGateway::where(['name' => 'is_enabled', 'site' => 'Braintree'])->update(['value' => $request->bt_enabled]);
        PaymentGateway::where(['name' => 'mode', 'site' => 'Braintree'])->update(['value' => $request->bt_mode]);
        PaymentGateway::where(['name' => 'merchant_id', 'site' => 'Braintree'])->update(['value' => $request->bt_merchant_id]);
        PaymentGateway::where(['name' => 'public_key', 'site' => 'Braintree'])->update(['value' => $request->bt_public_key]);
        PaymentGateway::where(['name' => 'private_key', 'site' => 'Braintree'])->update(['value' => $request->bt_private_key]);

        $payout_methods = implode($request->payout_methods,',');

        PaymentGateway::where(['name' => 'payout_methods', 'site' => 'Common'])->update(['value' => $payout_methods]);

        /*if($request->stripe_enabled == "1" && !in_array('stripe',$default_payments)) {
            PaymentGateway::where(['name' => 'trip_default', 'site' => 'Common'])->update(['value' => 'stripe']);
        }

        if($request->bt_enabled == "1" && !in_array('braintree',$default_payments)) {
            PaymentGateway::where(['name' => 'trip_default', 'site' => 'Common'])->update(['value' => 'braintree']);
        }*/

        flashMessage('success', 'Updated Successfully');
    
        return redirect('admin/payment_gateway');
    }
}