<?php

/**
 * Company Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Company
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use DB;

class Company extends Authenticatable
{
    use Notifiable;

    protected $guard = 'company';

    protected $table = 'companies';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['first_name'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($input)
    {
        $this->attributes['password'] = bcrypt($input);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Update Company Role
    public static function update_role($user_id, $role_id)
    {
        return DB::table('role_user')->where('user_id', $user_id)->update(['role_id' => $role_id]);
    }

    public function company_document()
    {
        return $this->hasOne('App\Models\CompanyDocuments','company_id','id');
    }

    // Return the drivers default payout credential details
    public function payout_credentials()
    {
        return $this->hasMany('App\Models\CompanyPayoutCredentials','company_id','id');
    }

    // Return the drivers default payout credential details
    public function default_payout_credentials()
    {
        $payout_methods = getPayoutMethods();

        $payout_methods = array_map(function($value) {
            return snakeToCamel($value,true);
        }, $payout_methods);
        return $this->hasOne('App\Models\CompanyPayoutCredentials')->whereIn('type',$payout_methods)->where('default','yes');
    }

    public function drivers()
    {
        return $this->hasMany('App\Models\User','company_id','id');
    }

    public function getFirstNameAttribute()
    {
        return $this->name;
    }

    public function getLastNameAttribute()
    {
        $name_array = explode(' ', $this->name);
        return end($name_array);
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

    // get driver applied owe amount
    public function getTotalOweAmountAttribute()
    {
        $company_owe = Trips::CompanyTripsOnly($this->id)->get();
        return $company_owe->sum('owe_amount');
    }

    // get driver applied owe amount
    public function getAppliedOweAmountAttribute()
    {
        $company_id = $this->id;
        $company_owe = Trips::CompanyTripsOnly($company_id)->get();
        $driver_owe_amount_paymnt = DriverOweAmountPayment::whereHas('user',function($q) use ($company_id){
            $q->where('company_id',$company_id);
        })->get();

        $applied_owe_amount = $company_owe->sum('applied_owe_amount') + $driver_owe_amount_paymnt->sum('amount');
        return $applied_owe_amount;
    }

    // get driver applied owe amount
    public function getRemainingOweAmountAttribute()
    {
        $company_id = $this->id;
        $company_driver_owe_amount = DriverOweAmount::whereHas('user',function($q) use ($company_id){
            $q->where('company_id',$company_id);
        })->get();
        return $company_driver_owe_amount->sum('amount');
    }

    // Add referral amount to wallet
    public function addAmountToWallet($to_user,$user_type,$currency_code,$wallet_amount)
    {
        $wallet = Wallet::where('user_id', $to_user)->first();
        if(isset($wallet)) {
            $amount = $this->currency_convert($currency_code,$wallet->getOriginal('currency_code'),$wallet_amount);
            $final_wallet = intval($wallet->getOriginal('amount')) + intval($amount);
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

    public function getEnvMobileNumberAttribute()
    {
        if(isLiveEnv()) {
            return '';
        }
        return $this->mobile_number;
    }

    public function getEmailAttribute()
    {
        if(isLiveEnv()) {
            return substr($this->attributes['email'], 0, 1) . '****' . substr($this->attributes['email'],  -4);
        }
        return $this->attributes['email'];
    }

    // phone number restrictions
    public function getMobileNumberAttribute()
    {
        if(isLiveEnv()) {
            return substr($this->attributes['mobile_number'], 0, 1) . '****' . substr($this->attributes['mobile_number'],  -4);
        }
        return $this->attributes['mobile_number'];
    }
}