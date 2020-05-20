<?php

/**
 * User Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    User
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use App\Models\Country;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DateTime;
use DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password',];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'string',
    ];

    protected $appends = ['car_type','latitude','longitude','date_time_join','phone_number','total_earnings','total_rides','total_commission','hidden_mobile_number','company_name'];

    // JWT Auth Functions Start
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    // JWT Auth Functions End

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeActiveOnlyStrict($query)
    {
        return $query->active()
            ->whereHas('vehicle',function($query) {
                $query->active();
            })
            ->whereHas('company',function($query) {
                $query->active();
            });
    }

    public function scopeAdminCompany($query)
    {
        return $query->where('company_id', '1');
    }

       public function setPasswordAttribute($input)
        {
            $this->attributes['password'] = bcrypt($input);
        }

    // Join with profile_picture table
    public function profile_picture()
    {
        return $this->belongsTo('App\Models\ProfilePicture','id','user_id');
    }

    // Join with profile_picture table
    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet','id','user_id');
    }

    // Join with vehicle table
    public function vehicle()
    {
        return $this->hasOne('App\Models\Vehicle','user_id','id');
    }

    // Join with driver_documents table
    public function driver_documents()
    {
        return $this->belongsTo('App\Models\DriverDocuments','id','user_id');
    }

     // Join with driver_location table
    public function driver_location()
    {
        return $this->belongsTo('App\Models\DriverLocation','id','user_id');
    }

     // Join with driver_documents table
    public function driver_address()
    {
        return $this->belongsTo('App\Models\DriverAddress','id','user_id');
    }

     // Join with driver_documents table
    public function rider_location()
    {
        return $this->belongsTo('App\Models\RiderLocation','id','user_id');
    }

    // Join with trips table
    public function driver_trips()
    {
        return $this->hasMany('App\Models\Trips','driver_id','id');
    }

    // Return the drivers default payout credential details
    public function payout_credentials()
    {
        return $this->hasMany('App\Models\PayoutCredentials','user_id','id');
    }

    // Return the drivers default payout credential details
    public function default_payout_credentials()
    {
        $payout_methods = getPayoutMethods($this->company_id);

        $payout_methods = array_map(function($value) {
            return snakeToCamel($value,true);
        }, $payout_methods);
    	return $this->hasOne('App\Models\PayoutCredentials')->whereIn('type',$payout_methods)->where('default','yes');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id','id');
    }

    public function driver_owe_amount_payments() {
        return $this->hasMany('App\Models\DriverOweAmountPayment', 'user_id', 'id');
    }

    public function driver_owe_amount() {
        return $this->hasOne('App\Models\DriverOweAmount', 'user_id', 'id');
    }

    // Get Driver payout currency
    public function getDriverPayoutCurrencyAttribute()
    {
        $payout = PayoutCredentials::with(['payout_preference'])->where('user_id', $this->attributes['id'])->where('default', 'yes')->first();
        return $payout->currency_code;
    }

    //Join with country
    public function country_name()
    {
        $data = Country::where('phone_code',@$this->attributes['country_code'])->first();
        if($data)
            return $data->long_name;    
    }

    // facebook authenticate 
    public static function user_facebook_authenticate($email, $fb_id){
        $user = User::where(function($query) use($email, $fb_id){
            $query->where('email', $email)->orWhere('fb_id', $fb_id);
        });
        return $user;
    }

    // Check Email and Google ID
    public static function user_google_authenticate($email, $google_id)
    {
        $user = User::where('user_type','Rider')->where(function($query) use($email, $google_id) {
            $query->where('email', $email)->orWhere('google_id', $google_id);
        });
        return $user;
    }

    // get latitude
    public function getLatitudeAttribute(){
        $user_type = @$this->attributes['user_type'];

        if($user_type == 'Driver')
        {
            $latitude = @DriverLocation::where('user_id',@$this->attributes['id'])->first()->latitude;
        }
        else
        {
            $latitude = @RiderLocation::where('user_id',@$this->attributes['id'])->first()->latitude;
        }

        return @$latitude;

    }

    // get longitude
    public function getLongitudeAttribute(){
        $user_type = @$this->attributes['user_type'];

        if($user_type == 'Driver')
        {
            $longitude = @DriverLocation::where('user_id',@$this->attributes['id'])->first()->longitude;
        }
        else
        {
            $longitude = @RiderLocation::where('user_id',@$this->attributes['id'])->first()->longitude;
        }

        return @$longitude;
    }
   

    // Get header picture source URL based on photo_source
    public function getCarTypeAttribute()
    {
       $user = Vehicle::with([
                    'car_type' => function($query){}
                 ])->where('user_id',$this->attributes['id'])->get();

     

        if($user->count())
        return (@$user[0]->car_type->car_name)? @$user[0]->car_type->car_name : '';    
        else
        return "";
    }

    public function getPhoneNumberAttribute()
    {
        return "+".@$this->attributes['country_code'].@$this->attributes['mobile_number'];
    }

    public function getDateTimeJoinAttribute()
    {
        $full = false;

        $now = new DateTime;
        $ago = new DateTime(@$this->attributes['created_at']);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function getTotalRidesAttribute()
    {
        $total_rides=DB::table('trips')->where('driver_id',$this->attributes['id']);
        return $total_rides->count();
    }

    public function getTotalEarningsAttribute()
    {
        $total_rides=Trips::where('driver_id',$this->attributes['id'])->get();
        if($total_rides->count()) {

            $total_amount = $total_rides->sum(function ($trip) {
                return $trip->company_driver_earnings;
            });

            return $total_amount;
        }
        else
        {
            return number_format(0,2);
        }
    }

    public function getTotalCommissionAttribute()
    {
        $total_rides=Trips::where('driver_id',$this->attributes['id'])->get();
        if($total_rides->count())
        {
            $total_amount = $total_rides->sum(function ($trip) {
                return $trip->commission;
            });

            return $total_amount;
        }
        else
        {
            return number_format(0,2);
        }
    }

    public function getTotalCompanyAdminCommissionAttribute()
    {
        $total_rides=Trips::where('driver_id',$this->attributes['id'])->get();
        if($total_rides->count())
        {
            $total_amount = $total_rides->sum(function ($trip) {
                return $trip->company_admin_commission;
            });

            return $total_amount;
        }
        else
        {
            return number_format(0,2);
        }
    }

    public function getCurrencyAttribute()
    {
        $currency_code = $this->attributes['currency_code'];
        $currency = Currency::where('code', $currency_code)->first();
        if($currency == '') {
            $currency = Currency::defaultCurrency()->first();
            User::where('id', $this->attributes['id'])->update(['currency_code' => $currency->code]);
        }
        return $currency;
    }
    // Get Translated Status Attribute
    public function getTransStatusAttribute()
    {
        return trans('messages.driver_dashboard.'.$this->attributes['status']);
    }

    // Get Payout Id of the driver
    public function getPayoutIdAttribute()
    {
        $payout_id = '';
        $payout_details = $this->default_payout_credentials;
        if($payout_details != '')
            $payout_id = $payout_details->account_number;

        return $payout_id;
    }

    // Get Mobile number with Protected format
    public function getHiddenMobileNumberAttribute()
    {
        // return $this->attributes['mobile_number'];

        $protected_number = '-';
        if(!isset($this->attributes['mobile_number'])){
            return $protected_number;
        }
        $mobile_number = $this->attributes['mobile_number'];
        if($mobile_number != '') {
            $protected_number = str_replace(range(0,9), "*", substr($mobile_number, 0, -4)) .  substr($mobile_number, -4);
        }

        return $protected_number;
    }

    public function getCompanyNameAttribute()
    {
        $company_name = '';

        if(@$this->attributes['user_type'] == 'Driver') {
            $company_name = isset($this->company) ? $this->company->name : '';
        }

        return $company_name;
    }

     // Set valid Driver referral code
    public function setUsedReferralCodeAttribute($value)
    {
            $this->attributes['used_referral_code'] = $value;
    
    }

    // Get Unique Referral Code
    public function getUniqueReferralCode()
    {
        $code = $this->generateUniqueReferralCode();
        $is_valid = $this->isValidReferralCode($code);        
        if($is_valid) {
            return $code;
        }
        else {
            return $this->getUniqueReferralCode();
        }
    }

    // Get Unique Referral Code
    public function generateUniqueReferralCode()
    {
        //$code = strtoupper(str_random(10));
        //--Konstantin N changes
        srand(intval(substr(md5(microtime()),rand(0,26),5)));
        $code = strtoupper("RODO" . intval( "0" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ));
        //--

        return $code;
    }

    // Validate Referral Code already taken by others
    public function isValidReferralCode($code)
    {
        $prev_code_check = DB::Table('users')->where('referral_code',$code)->count();
        return $prev_code_check == 0;
    }

    // get the referred user id
    public function getReferralUserIdAttribute()
    {
        $referral_user = DB::Table('users')->where('referral_code',$this->attributes['used_referral_code'])->first(['id']);
        return (isset($referral_user->id)) ? $referral_user->id : '';
    }

    // Check user has completed their referrals
    public function getIsReferralCompletedAttribute()
    {
        if($this->referral_user_id == '')
            return false;

        $c_date = date('Y-m-d');

        $referral_user = ReferralUser::where('user_id',$this->referral_user_id)->where('referral_id',$this->attributes['id'])->whereRaw('"'.$c_date.'" between start_date and end_date')->wherePaymentStatus('Pending')->first();

        return (isset($referral_user) && $referral_user->remaining_trips == 0);
    }

    public function getTotalReferralEarningsAttribute()
    {
        $total_amount = number_format(0,2);
        $currency_symbol = '';
        $referral_users = ReferralUser::where('user_id',$this->attributes['id'])->wherePaymentStatus('Completed')->get();

        if($referral_users->count()) {
            $currency_symbol = html_entity_decode($referral_users[0]->currency_symbol);
            $total_amount = $referral_users->sum(function ($referral_user) {
                return $referral_user->amount;
            });
        }

        return $currency_symbol.''.$total_amount;
    }

    public function getPendingReferralAmountAttribute()
    {
        $total_amount = number_format(0,2);
        $currency_symbol = '';
        $referral_users = ReferralUser::where('user_id',$this->attributes['id'])->wherePaymentStatus('Completed')->where('pending_amount','>',0)->get();

        if($referral_users->count()) {
            $currency_symbol = html_entity_decode($referral_users[0]->currency_symbol);
            $total_amount = $referral_users->sum(function ($referral_user) {
                return $referral_user->pending_amount;
            });
        }

        return $currency_symbol.''.$total_amount;
    }

    // get driver total owe amount
    public function getTotalOweAmountAttribute()
    {
        $owe_amount = $this->driver_trips->sum('owe_amount');
        return $owe_amount;
    }

    // get driver applied owe amount
    public function getAppliedOweAmountAttribute()
    {
        $applied_owe_amount = $this->trip_applied_owe_amount + $this->paid_amount;
        return $applied_owe_amount;
    }

    // get driver applied owe amount
    public function getTripAppliedOweAmountAttribute()
    {
        $applied_owe_amount = $this->driver_trips->sum('applied_owe_amount');
        return $applied_owe_amount;
    }

    // get driver applied owe amount
    public function getPaidAmountAttribute()
    {
        $paid_amount = $this->driver_owe_amount_payments->sum('amount');
        return $paid_amount;
    }

    // get driver remaining owe amount
    public function getRemainingOweAmountAttribute()
    {
        return $this->driver_owe_amount->amount;
    }

    // Add referral amount to wallet
    public function addAmountToWallet($to_user,$user_type,$currency_code,$wallet_amount)
    {
        $wallet = Wallet::where('user_id', $to_user)->first();
        if(isset($wallet)) {
            $amount = $this->currency_convert($currency_code,$wallet->getOriginal('currency_code'),$wallet_amount);
            $final_wallet = number_format($wallet->getOriginal('amount'),2,'.','') + number_format($amount,2,'.','');
            $wallet->amount = $final_wallet;
        }
        else {
            $wallet = new Wallet;
            $wallet->user_id = $to_user;
            $wallet->amount = $wallet_amount;
            $wallet->currency_code = $currency_code;
        }
        $wallet->save();

        $this->completeReferral($to_user);
        return '';
    }

    // Update referral user table payment status
    public function completeReferral($referral_user_id)
    {
        $c_date = date('Y-m-d');
        $referral_user = ReferralUser::where('user_id',$referral_user_id)->where('referral_id',$this->attributes['id'])->whereRaw('"'.$c_date.'" between start_date and end_date')->wherePaymentStatus('Pending')->first();
        if($referral_user != '') {
            $referral_user->payment_status = 'Completed';
            $referral_user->save();
        }

        return '';
    }

    public function currency_convert($from, $to, $price = 0)
    {
        if($from == $to) {
            return $price;
        }

        $rate = Currency::whereCode($from)->first()->rate;
        $session_rate = Currency::whereCode($to)->first()->rate;
        $usd_amount = $price / $rate;

        $currency_val = number_format($usd_amount * $session_rate, 2, '.', '');
        return $currency_val;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getEnvMobileNumberAttribute()
    {
        if(isLiveEnv()) {
            return '';
        }
        return $this->mobile_number;
    }

    public function getEmailAttribute()
    {
        if(isLiveEnv() && isAdmin()) {
            return substr($this->attributes['email'], 0, 1) . '****' . substr($this->attributes['email'],  -4);
        }
        return $this->attributes['email'];
    }

    // phone number restrictions
    public function getMobileNumberAttribute()
    {
        if(isLiveEnv() && isAdmin()) {
            return substr($this->attributes['mobile_number'], 0, 1) . '****' . substr($this->attributes['mobile_number'],  -4);
        }
        return $this->attributes['mobile_number'];
    }
}