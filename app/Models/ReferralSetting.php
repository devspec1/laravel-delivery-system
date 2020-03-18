<?php

/**
 * Referral Setting Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Referral Settings
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    use CurrencyConversion;

    public $timestamps = false;

    public $convert_fields = [];

    public function scopeRider($query)
    {
    	return $query->whereUserType('Rider');
    }

    public function scopeDriver($query)
    {
    	return $query->whereUserType('Driver');
    }

    public function get_referral_amount($user_type)
    {
        if($user_type == 'Driver') {
            return $this->driver_referral_amount;
        }
        return $this->rider_referral_amount;
    }

    public function getRiderReferralAmountAttribute()
    {
        $admin_referral_details = \DB::Table('referral_settings')->where('user_type','Rider')->get()->pluck('value','name');
        if($admin_referral_details['apply_referral'] != '1') {
            return "0";
        }

        $amount = $this->currency_convert($admin_referral_details['currency_code'],$this->currency_code,$admin_referral_details['referral_amount']);
        $symbol = html_entity_decode($this->currency_symbol);

        return $symbol.''.$amount;
    }

    public function getDriverReferralAmountAttribute()
    {
        $admin_referral_details = \DB::Table('referral_settings')->where('user_type','Driver')->get()->pluck('value','name');
        if($admin_referral_details['apply_referral'] != '1') {
            return "0";
        }

        $amount = $this->currency_convert($admin_referral_details['currency_code'],$this->currency_code,$admin_referral_details['referral_amount']);
        $symbol = html_entity_decode($this->currency_symbol);

        return $symbol.''.$amount;
    }
}