<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Mail;
use URL;
use Auth;
use Config;
use DateTime;
use DateTimeZone;
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use App\Models\PasswordResets;
use App\Models\PayoutPreference;
use App\Models\PayoutCredentials;
use App\Models\PaymentGateway;
use App\Models\Country;
use App\Models\Currency;
use App\Mail\MailQueue;

class EmailController extends Controller
{
    
     /**
     * Send Forgot Password Mail with Confirmation Link
     *
     * @param array $user  User Details
     * @return true
     */
    public function forgot_password_link($user)
    {
        $data['first_name'] = $user->first_name;

        $token = $data['token'] = str_random(100); // Generate random string values - limit 100
        $url = $data['url'] = URL::to('/').'/';

        $data['locale']       = App::getLocale();

        $password_resets = new PasswordResets;

        $password_resets->email      = $user->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');
        
        $password_resets->save(); // Insert a generated token and email in password_resets table
        $email      = $user->email;
        $content    = [
            'first_name' => $user->first_name, 
            'url'=> $url,
            'token' => $token
            ];
        // Send Forgot password email to give user email
        Mail::to($email)->queue(new ForgotPasswordMail($content));

        return true;
    }

    /**
     * Send Forgot Password Mail with Confirmation Link
     *
     * @param array $company  Company Details
     * @return true
     */
    public function company_forgot_password_link($company)
    {
        $data['first_name'] = $company->name;

        $token = $data['token'] = str_random(100); // Generate random string values - limit 100
        $url = $data['url'] = URL::to('/').'/company/';

        $data['locale']       = App::getLocale();

        $password_resets = new PasswordResets;

        $password_resets->email      = $company->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');
        
        $password_resets->save(); // Insert a generated token and email in password_resets table
        $email      = $company->email;
        $content    = [
            'first_name' => $company->name, 
            'url'=> $url,
            'token' => $token
            ];

        // Send Forgot password email to give user email

        Mail::to($email)->queue(new ForgotPasswordMail($content));

        return true;
    }
    /**
     * Send Updated Payout Information Mail to Host
     *
     * @param array $payout_preference_id Payout Preference Details
     * @return true
     */
    public function payout_preferences($payout_preference_id, $type = 'update')
    {
        if($type != 'delete') {
            $result = PayoutCredentials::with(['payout_preference','users'])->find($payout_preference_id);
            $user = $result->users;
            $data['first_name'] = $user->first_name;
            $data['updated_time'] = $result->updated_time;
            $data['updated_date'] = $result->updated_date;
        }
        else {
            if(LOGIN_USER_TYPE == 'api') {
                //set api user authentication
                $user=JWTAuth::parseToken()->authenticate();
                $data['first_name'] = $user->first_name;
                $new_str = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone(Config::get('app.timezone')));
                $new_str->setTimeZone(new DateTimeZone($user->timezone));
            }
            else {
                //set web user authentication
                $user = Auth::user();
                $data['first_name'] = $user->first_name;
                $new_str = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone(Config::get('app.timezone')));
            }
            $data['deleted_time'] = $new_str->format('d M').' at '.$new_str->format('H:i');
        }

        $data['type'] = $type;
        $data['url'] = url('/').'/';
        $data['locale']       = App::getLocale();

        if($type == 'update')
            $subject = trans('messages.email.your').' '.SITE_NAME." ".trans('messages.email.payout_information_updated');
        else if($type == 'delete')
            $subject = trans('messages.email.your').' '.SITE_NAME." ".trans('messages.email.payout_information_deleted');
        else if($type == 'default_update')
            $subject = trans('messages.email.payout_information_changed');
       
        $data['subject'] = $subject;
        $data['view_file'] = 'emails.payout_preferences';

        Mail::to($user->email, $user->first_name)->queue(new MailQueue($data));

        return true;
    }
}
