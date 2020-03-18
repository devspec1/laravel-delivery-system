<?php

/**
 * Trip Observer
 *
 * @package     Gofer
 * @subpackage  Observer
 * @category    Trip
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Observers;

use App\Http\Helper\RequestHelper;
use App\Models\Trips;
use App\Models\User;
use App\Models\ReferralUser;

class TripObserver
{
    protected $request_helper;

    public function __construct(RequestHelper $request)
    {
        $this->request_helper = $request;
    }

    /**
     * Listen to the Trips updated event.
     *
     * @param  Trips  $trip
     * @return void
     */
    public function updated(Trips $trip)
    {
        if($trip->status == 'Completed') {

            // Driver Referral Functionality
            $driver = User::where('id', $trip->driver_id)->first();

            if($driver->used_referral_code != '' && $driver->is_referral_completed) {

                $referrar = User::where('id', $driver->referral_user_id)->first();

                $referral_user = ReferralUser::where('referral_id',$driver->id)->where('user_id',$referrar->id)->first();

                $driver->addAmountToWallet($driver->referral_user_id,$driver->user_type,$referral_user->currency_code,$referral_user->amount);

                $referral_users = ReferralUser::where('referral_id',$driver->id)->where('user_id',$referrar->id)->first();
                $this->sendNotificationToUser($referral_users);
            }

            // Rider Referral Functionality
            $rider = User::where('id', $trip->user_id)->first();
            
            if($rider->used_referral_code != '' && $rider->is_referral_completed) {

                $referrar = User::where('id', $rider->referral_user_id)->first();

                $referral_user = ReferralUser::where('referral_id',$rider->id)->where('user_id',$referrar->id)->first();

                $rider->addAmountToWallet($rider->referral_user_id,$rider->user_type,$referral_user->currency_code,$referral_user->amount);

                $referral_users = ReferralUser::where('referral_id',$rider->id)->where('user_id',$referrar->id)->first();
                $this->sendNotificationToUser($referral_users);
            }
        }
    }

    /**
     * Send Completed Push Notification to User After Referral Trip Completed
     *
     * @param  ReferralUser $referral_users
     * @return void
     */
    public function sendNotificationToUser($referral_users)
    {
        $referrar =  User::where('id', $referral_users->user_id)->first();

        $push_title = __('messages.referrals.referral_credited').' - '.$referral_users->referral_user->first_name;
        $text    = __('messages.referrals.referral_credited_desc');

        $push_data['push_title'] = $push_title;
        $push_data['data'] = array(
            'custom_message' => array(
                'title' => $push_title,
                'message_data' => $text,
            )
        );

        $this->request_helper->SendPushNotification($referrar,$push_data);
    }
}