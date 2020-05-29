<?php

/**
 * Driver Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Helper\RequestHelper;
use App\Http\Start\Helpers;
use App\Models\DriverLocation;
use App\Models\Application;
use App\Models\Country;
use App\Models\Merchant;
use App\Models\Payment;
use App\Models\DriverAddress;
use App\Models\DriverOweAmountPayment;
use App\Models\DriverOweAmount;
use App\Models\DriversSubscriptions;
use App\Models\StripeSubscriptionsPlans;
use App\Models\Rating;
use App\Models\Request as RideRequest;
use App\Models\ScheduleRide;
use App\Models\PaymentMethod;
use App\Models\Trips;
use App\Models\User;
use App\Models\UsersPromoCode;
use App\Models\BankDetail;
use App\Models\AppliedReferrals;
use App\Models\ReferralUser;
use App\Models\Fees;
use App\Models\ProfilePicture;
use App\Models\Vehicle;
use App\Models\DriverDocuments;
use App\Models\PayoutPreference;
use App\Models\PayoutCredentials;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JWTAuth;
use Validator;
use File;
use App\Http\Helper\InvoiceHelper;

class JotFormController extends Controller
{
	protected $request_helper; // Global variable for Helpers instance

	public function __construct(RequestHelper $request,InvoiceHelper $invoice_helper)
	{
		$this->request_helper = $request;
		$this->helper = new Helpers;
		$this->invoice_helper = $invoice_helper;
	}

     /**
     * Add a New Driver
     *
     * @param array $request  Input values
     * @return redirect     to Driver view
     */
    public function driver(Request $request)
    {
        $submissionID = $request->submissionID;
        $fieldvalues = $request->rawRequest;
        $obj = json_decode($fieldvalues, true);

        $ccodes = Country::get();
        
        $country_code = '';
        $last_phone_number = '';
        $phone_number = $obj['q127_mobileNumber'];

        foreach( $ccodes as $ccode )
        {
            if ( substr( $phone_number, 1, strlen( $ccode->phone_code ) ) == $ccode->phone_code )
            {
                // match
                $country_code = $ccode->phone_code;
                $last_phone_number = substr($phone_number, 1 + strlen($country_code));
                break;
            }
        }

        $user = new User;

        $user->first_name   = $obj['q96_name96']['first'];
        $user->last_name    = $obj['q96_name96']['last'];
        $user->email        = $obj['q128_email'];
        $user->country_code = $country_code;
        $user->mobile_number= $last_phone_number;
        $user->password     = $this->randomPassword();
        $user->status       = 'Pending';
        $user->user_type    = 'Driver';
        $user->used_referral_code = $obj['q138_invitationCode'] ? $obj['q138_invitationCode']:'';
        $user->company_id   = 1;
        $user->save();

        $user_pic = new ProfilePicture;
        $user_pic->user_id      =   $user->id;
        $user_pic->src          =   'https://rideon-cdn.sgp1.cdn.digitaloceanspaces.com/images/users/JotForm/Driver Application/' . $submissionID . '/' . $obj['temp_upload']['q131_profilephoto'][0];
        $user_pic->photo_source =   'Local';
        $user_pic->save();

        $user_address = new DriverAddress;
        $user_address->user_id       =   $user->id;
        $user_address->address_line1 =   $obj['q129_address']['addr_line1'] ? $obj['q129_address']['addr_line1']:'';
        $user_address->address_line2 =   $obj['q129_address']['addr_line2'] ? $obj['q129_address']['addr_line2']:'';
        $user_address->city          =   $obj['q129_address']['city'] ? $obj['q129_address']['city']:'';
        $user_address->state         =   $obj['q129_address']['state'] ? $obj['q129_address']['state']:'';
        $user_address->postal_code   =   $obj['q129_address']['postal'] ? $obj['q129_address']['postal']:'';
        $user_address->save();

        $payout_preference = PayoutPreference::firstOrNew(['user_id' => $user->id,'payout_method' => "BankTransfer"]);
        $payout_preference->user_id = $user->id;
        $payout_preference->country = "IN";
        $payout_preference->account_number  = $obj['q137_yourBank']['field_2'];
        $payout_preference->holder_name     = $obj['q137_yourBank']['field_3'];
        $payout_preference->holder_type     = "company";
        $payout_preference->paypal_email    = $obj['q137_yourBank']['field_2'];

        $payout_preference->phone_number    = $obj['q127_mobileNumber'] ?? '';
        $payout_preference->branch_code     = $obj['q137_yourBank']['field_5'] ?? '';
        $payout_preference->bank_name       = $obj['q137_yourBank']['field_6'] ?? '';
        $payout_preference->bank_location   = $obj['q137_yourBank']['field_4'] ?? '';
        $payout_preference->payout_method   = "BankTransfer";
        $payout_preference->address_kanji   = json_encode([]);
        $payout_preference->save();

        $payout_credentials = PayoutCredentials::firstOrNew(['user_id' => $user->id,'type' => "BankTransfer"]);
        $payout_credentials->user_id = $user->id;
        $payout_credentials->preference_id = $payout_preference->id;
        $payout_credentials->payout_id = $obj['q137_yourBank']['field_2'];
        $payout_credentials->type = "BankTransfer";
        $payout_credentials->default = 'yes';
        $payout_credentials->save();

        $user_doc = new DriverDocuments;
        $user_doc->user_id = $user->id;
        if ($obj['temp_upload']['q134_driver_license'][0])
            $user_doc->license_front = 'https://rideon-cdn.sgp1.cdn.digitaloceanspaces.com/images/users/JotForm/Driver Application/' . $submissionID . '/' . $obj['temp_upload']['q134_driver_license'][0];
        if ($obj['temp_upload']['q134_driver_license'][1])
            $user_doc->license_back = 'https://rideon-cdn.sgp1.cdn.digitaloceanspaces.com/images/users/JotForm/Driver Application/' . $submissionID . '/' . $obj['temp_upload']['q134_driver_license'][1];
        $user_doc->abn_number = $obj['q136_yourAbn'];
        $user_doc->save();

        $plan = StripeSubscriptionsPlans::where('plan_name','Driver only')->first();
        $subscription_row = new DriversSubscriptions;
        $subscription_row->user_id      = $user->id;
        $subscription_row->stripe_id    = '';
        $subscription_row->status       = 'subscribed';
        $subscription_row->email        = $user->email;
        $subscription_row->plan         = $plan->id;
        $subscription_row->country      = '';
        $subscription_row->card_name    = '';   
        $subscription_row->save(); 
                
        $application = new Application;
        $application->user_id = $user->id;
        $application->type = 'Driver';
        $application->vehicleType = implode($obj['q132_vehicleType']);
        $application->save();

        return response()->json([
            'status_code'     => '1',
            'status_message' => 'Validation success.',
        ]);
    }

    /**
     * Add a New Merchant
     *
     * @param array $request  Input values
     * @return redirect     to Home Delivery Order view
     */
    public function merchant(Request $request, $id=null)
    {
        $submissionID = $request->submissionID;
        $fieldvalues = $request->rawRequest;
        $obj = json_decode($fieldvalues, true);

        $ccodes = Country::get();
        
        $country_code = '';
        $last_phone_number = '';
        $phone_number = $obj['q127_mobileNumber'];

        foreach( $ccodes as $ccode )
        {
            if ( substr( $phone_number, 1, strlen( $ccode->phone_code ) ) == $ccode->phone_code )
            {
                // match
                $country_code = $ccode->phone_code;
                $last_phone_number = substr($phone_number, 1 + strlen($country_code));
                break;
            }
        }

        $user = new User;
        $usedRef = User::where('referral_code', $obj['q136_invitationCode'])->first();

        $user->first_name   = $obj['q96_name']['first'];
        $user->last_name    = $obj['q96_name']['last'];
        
        if ($usedRef)
            $user->used_referral_code = $usedRef->referral_code;
        else
            $user->used_referral_code = 0;

        $user->email        = $obj['q128_email'];
        $user->country_code = $country_code;
        $user->mobile_number = $last_phone_number;
        $user->user_type    = 'Merchant';
        
        $user->save();

        //find user by refferer_id
        if($usedRef) {
            //if there is no reference between users, create it
            $referrel_user = new ReferralUser;
            $referrel_user->referral_id = $user->id;
            $referrel_user->user_id     = $usedRef->id;
            $referrel_user->user_type   = $usedRef->user_type;
            $referrel_user->save();
        }

        $user_address = new DriverAddress;

        $user_address->user_id       = $user->id;
        $user_address->address_line1 = $obj['q129_address']['addr_line1'];
        $user_address->address_line2 = $obj['q129_address']['addr_line2'];
        $user_address->city          = $obj['q129_address']['city'];
        $user_address->state         = $obj['q129_address']['state'];
        $user_address->postal_code   = $obj['q129_address']['postal'];
        $user_address->save();

        $merchant = new Merchant;

        $merchant->user_id = $user->id;
        $merchant->name = $obj['q134_trading_name'];
        $merchant->description = $obj['q149_description'];
        $merchant->cuisine_type = $obj['q135_cuisine_type'];
        switch ($obj['q150_integration_type'])
        {
            case 'Gloria Food':
                $merchant->integration_type = 1;
                break;
            case 'SquareUp':
                $merchant->integration_type = 2;
                break;
            case 'Shopify':
                $merchant->integration_type = 3;
                break;
        }
        $merchant->delivery_fee  = $obj['q148_fee']['field_1'];
        $merchant->delivery_fee_per_km = $obj['q148_fee']['field_2'];
        $merchant->delivery_fee_base_distance = $obj['q148_fee']['field_3'];
        switch ($merchant->integration_type)
        {
            case 1: // Gloria Food
                $merchant->shared_secret = Str::uuid();
                break;
        }
        $merchant->save();
        
        $application = new Application;
        $application->user_id = $user->id;
        $application->type = 'Merchant';
        $application->q_hear = $obj['q145_q_hear'];
        $application->q_popularItem = $obj['q146_q_popularItem'];
        $i = 1; $cnt = sizeof($obj['q139_q_expectOrders']);
        foreach ($obj['q139_q_expectOrders'] as $tmp)
        {
            $application->q_expectOrders .= $tmp[0];
            if ($i != $cnt)
                $application->q_expectOrders .= ',';
            $i ++;
        }
        $application->asset_website = $obj['q144_q_assets']['field_2'];
        $application->asset_facebook = $obj['q144_q_assets']['field_1'];
        $application->asset_instagram = $obj['q144_q_assets']['field_3'];
        $application->asset_other = $obj['q144_q_assets']['field_4'];

        if ($obj['temp_upload']['q141_logo'][0])
            $application->logo = 'https://rideon-cdn.sgp1.cdn.digitaloceanspaces.com/images/users/JotForm/Merchant Application/' . $submissionID . '/' . $obj['temp_upload']['q141_logo'][0];
        if ($obj['temp_upload']['q147_q_photoItem'][0])
            $application->photoItem = 'https://rideon-cdn.sgp1.cdn.digitaloceanspaces.com/images/users/JotForm/Merchant Application/' . $submissionID . '/' . $obj['temp_upload']['q147_q_photoItem'][0];

        $application->save();

        return response()->json([
            'status_code'     => '1',
            'status_message' => 'Validation success.',
        ]);
    }

    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 16; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
