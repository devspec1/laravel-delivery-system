<?php

/**
 * Referral User Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Referral User
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DB;

class ReferralUser extends Model
{
    use CurrencyConversion;

    public $convert_fields = ['amount','pending_amount'];

    protected $appends = ['referred_user_name', 'remaining_days', 'remaining_trips','earnable_amount'];

    public $disable_admin_panel_convertion = true;

    // Join with user table
    public function referral_user()
    {
        return $this->belongsTo('App\Models\User','referral_id','id');
    }

    // Join with user table
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    // Get the referred user name
    public function getReferredUserNameAttribute()
    {
        return $this->referral_user->first_name;
    }

    // Get the referred user name
    public function getReferredUserProfilePictureSrcAttribute()
    {
        return $this->referral_user->profile_picture->src;
    }

    // Get the Remaining days to get payment
    public function getRemainingDaysAttribute()
    {
        if($this->attributes['payment_status'] == 'Expired') {
            return 0;
        }
        $end_date = new DateTime($this->attributes['end_date']);
        $now = new DateTime(date('Y-m-d'));
        $interval = $end_date->diff($now);
        $remaining_days = $interval->days;
        return ($remaining_days < 0) ? 0 : $remaining_days;
    }

    // Get the Remaining trips to get payment
    public function getRemainingTripsAttribute()
    {
        $start_date = date('Y-m-d H:i:s',strtotime($this->attributes['start_date']));
        $end_date = date('Y-m-d 23:59:59',strtotime($this->attributes['end_date']));
        $trip_col = 'user_id';

        if($this->attributes['user_type'] == 'Driver') {
            $trip_col = 'driver_id';
        }

        $prev_trips_count = DB::Table('trips')->selectRaw("count('id') as trips_count")->where($trip_col,$this->attributes['referral_id'])->whereBetween('updated_at',[$start_date, $end_date])->whereStatus('Completed')->first()->trips_count;

        $trips_count = $this->attributes['trips'] - $prev_trips_count;
        return ($trips_count < 0) ? 0 : $trips_count;
    }

    // Get the Remaining trips to get payment
    public function getEarnableAmountAttribute()
    {
        return html_entity_decode($this->currency_symbol).''.$this->amount;
    }
}
