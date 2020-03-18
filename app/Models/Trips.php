<?php

/**
 * Trips Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Trips
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DB;
use Auth;

class Trips extends Model
{
    use CurrencyConversion;

    public $convert_fields = ['time_fare', 'distance_fare', 'base_fare', 'total_fare', 'access_fee', 'driver_payout', 'owe_amount', 'remaining_owe_amount', 'applied_owe_amount', 'wallet_amount','promo_amount','payable_driver_payout','cash_collectable','commission','company_admin_commission','total_trip_fare','total_invoice','total_payout_frontend','cash_collect_frontend','driver_front_payout','rider_paid_amount','subtotal_fare','peak_amount','schedule_fare','driver_peak_amount','company_commission','driver_service_fee','driver_or_company_commission','driver_or_company_earning','tips','admin_total_amount', 'waiting_charge','toll_fee','driver_earnings'];
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trips';

    protected $appends = ['vehicle_name','driver_name','rider_name','rider_profile_picture','driver_thumb_image','rider_thumb_image','date','pickup_time','pickup_time_formatted','drop_time','pickup_date_time','trip_time','begin_date','payout_status','date_time_trip','driver_joined_at','payable_driver_payout','cash_collectable','commission','company_admin_commission','total_trip_fare','total_invoice','total_payout_frontend','cash_collect_frontend','driver_front_payout','rider_paid_amount','map_image','currency_symbol','status','toll_fee_reason','company_driver_earnings'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // Join with profile_picture table
    public function users()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    
    // Join with user table
    public function driver()
    {
        return $this->belongsTo('App\Models\User','driver_id','id');
    }
    // Join with cancel table
    public function cancel()
    {
        return $this->belongsTo('App\Models\Cancel','id','trip_id');
    }
    // Join with payment table
    public function payment()
    {
        return $this->belongsTo('App\Models\Payment','id','trip_id');
    }
    // Join with Currency table
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_code','code');
    }
    public function language()
    {
        return $this->belongsTo('App\Models\Language','language_code','value');
    } 
    // Join with profile_picture table
    public function profile_picture()
    {
        return $this->belongsTo('App\Models\ProfilePicture','user_id','user_id');
    }

    // Join with car_type table
    public function car_type()
    {
        return $this->belongsTo('App\Models\CarType','car_id','id');
    }

    // Join with driver_location table
    public function driver_location()
    {
        return $this->belongsTo('App\Models\DriverLocation','driver_id','user_id');
    }
    // Join with rating table
    public function rating()
    {
        return $this->belongsTo('App\Models\Rating','user_id','user_id');
    }
     public function trip_rating()
    {
        return $this->belongsTo('App\Models\Rating','id','trip_id');
    }

    // Join with request table
    public function ride_request()
    {
        return $this->belongsTo('App\Models\Request','request_id','id');
    }
    
    // Join with Driver Address table
    public function driver_address()
    {
        return $this->belongsTo('App\Models\DriverAddress','driver_id','user_id');
    }

    // Join with payment table
    public function driver_payment()
    {
        return $this->hasOne('App\Models\Payment','trip_id','id');
    }

    // Join with payment table
    public function toll_reason()
    {
        return $this->hasOne('App\Models\TollReason','id','toll_reason_id');
    }

     // Join with payment table
    public function trip_toll_reason()
    {
        return $this->hasOne('App\Models\TripTollReason','trip_id','id');
    }

    public function scopePaymentTripsOnly($query)
    {
        return $query->whereNotIn('payment_mode',['Cash','Cash & Wallet']);
    }

    public function scopeCashTripsOnly($query)
    {
        return $query->whereIn('payment_mode',['Cash','Cash & Wallet']);
    }

    public function scopeDriverPayoutTripsOnly($query) {
        return $query->with(['payment'])
            ->whereHas('driver_payment', function ($query) {
                $query->where('driver_payout_status', 'Pending');
            })
            ->where(function($query)  {
                if(LOGIN_USER_TYPE=='company') {
                    $query->whereHas('driver',function($q1){
                        $q1->where('company_id',Auth::guard('company')->user()->id);
                    });
                }else{
                    $query->whereHas('driver',function($q1){
                        $q1->where('company_id',1);
                    });
                }
            })
            ->where('status','Completed')
            ->where('driver_payout','>',0)
            ->where('payment_mode','<>','Cash');
    }

    public function scopeCompanyPayoutTripsOnly($query) {
        return $query->with(['payment'])
            ->whereHas('driver_payment', function ($query) {
                if(LOGIN_USER_TYPE == 'admin') {
                    $query->where('admin_payout_status','Pending');
                }
                else {
                    $query->where('driver_payout_status', 'Pending');
                }
            })
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'trips.driver_id')
                    ->where('users.company_id', '!=', 1);
            })
            ->join('companies', function ($join) {
                $join->on('companies.id', '=', 'users.company_id')
                    ->where('users.company_id', '!=', 1);
            })
            ->where('trips.status','Completed')
            ->where('trips.driver_payout','>',0)
            ->where('trips.payment_mode','<>','Cash');
    }

    // Get vehicle name
    public function getVehicleNameAttribute()
    {
        return CarType::find($this->attributes['car_id'])->car_name;
    } 

     // Get status
    public function getStatusAttribute()
    {
        $status = $this->attributes['status'];
        if(LOGIN_USER_TYPE == 'company' || LOGIN_USER_TYPE == 'admin' || $status == "Completed") {
            return $status;
        }

        if($status == "Payment" ) {
            if(@Auth::user()->user_type=="Rider") {
                if(@$this->trip_rating->rider_rating) {
                    return $status;
                }
            }
            else {
                if(@Auth::user()->user_type=="Driver") {
                    if(@$this->trip_rating->driver_rating) {
                       return $status;
                    }
                }
            }
            return "Rating";
        }

        return $status;
    }

    public function getCommissionAttribute()
    {
        return $this->attributes['access_fee'] + ( $this->attributes['peak_amount'] - $this->attributes['driver_peak_amount'] ) + $this->attributes['schedule_fare'] + $this->attributes['driver_or_company_commission'];
    }
    public function getCompanyAdminCommissionAttribute()
    {
        return ( $this->attributes['peak_amount'] - $this->attributes['driver_peak_amount'] ) + $this->attributes['driver_or_company_commission'];
    }
    public function getDriverOrCompanyEarningAttribute()
    {
        return ( $this->attributes['subtotal_fare'] + $this->attributes['driver_peak_amount'] + $this->attributes['tips'] + $this->attributes['waiting_charge'] + $this->attributes['toll_fee']) - $this->attributes['driver_or_company_commission'];
    }
    
    public function getAdminTotalAmountAttribute()
    {
        return ( $this->attributes['subtotal_fare'] + $this->attributes['peak_amount'] + $this->attributes['access_fee'] + $this->attributes['schedule_fare'] + $this->attributes['tips'] + $this->attributes['toll_fee'] + $this->attributes['waiting_charge']);
    }

    public function getPayableDriverPayoutAttribute()
    {
        if($this->attributes['payment_mode']=="Cash" && $this->attributes['wallet_amount']==0 && $this->attributes['promo_amount']==0){
            return 0;
        }
        
        if(($this->attributes['payment_mode']=="Cash" || $this->attributes['payment_mode']=="Cash & Wallet") && ($this->attributes['wallet_amount']!=0 || $this->attributes['promo_amount']!=0)) {
            $promo_wallet=$this->attributes['wallet_amount']+$this->attributes['promo_amount'];
            $cash_collectable = $this->total_fare()-$promo_wallet;
            if($promo_wallet > $this->total_fare()) {
                $cash_collectable= 0;
            }

            return number_format(($this->attributes['driver_payout'] + $this->attributes['access_fee'] -$cash_collectable),2, '.', '');
        }

        return number_format((($this->total_fare()-$this->attributes['access_fee'])-$this->attributes['applied_owe_amount']),2, '.', '');
    }

    public function getRiderPaidAmountAttribute()
    {
        return number_format(($this->attributes['total_fare'])-($this->attributes['wallet_amount']+$this->attributes['promo_amount']),2, '.', '');   
    }
    public function getCashCollectableAttribute()
    {
        $cashcollect=0;

        if($this->attributes['payment_mode']=="Cash" || $this->attributes['payment_mode']=="Cash & Wallet")
        {  
            if($this->attributes['promo_amount']+$this->attributes['wallet_amount'] > $this->total_fare())
            {
                $cashcollect = 0 ; 
            }
            else
            $cashcollect=$this->total_fare()-($this->attributes['promo_amount']+$this->attributes['wallet_amount']);
        }
        return number_format($cashcollect,2, '.', '');
    }

    public function total_fare()
    {
        return $total_fare = $this->attributes['base_fare'] + $this->attributes['time_fare'] + $this->attributes['distance_fare'] + $this->attributes['schedule_fare'] + $this->attributes['access_fee'] + $this->attributes['peak_amount'] + $this->attributes['tips'] + $this->attributes['waiting_charge'] + $this->attributes['toll_fee'];
    }
    public function getDriverFrontPayoutAttribute()
    {
        return number_format((($this->attributes['wallet_amount']+$this->attributes['promo_amount'])-($this->attributes['access_fee']+$this->attributes['applied_owe_amount'])),2, '.', '');
    }
    public function getCashCollectFrontendAttribute()
    {
        $cashcollect=0;
        if($this->attributes['payment_mode']=="Cash" || $this->attributes['payment_mode']=="Cash & Wallet")
        {
            $cashcollect=$this->attributes['total_fare']-($this->attributes['promo_amount']+$this->attributes['wallet_amount']);
        }
        return number_format($cashcollect,2, '.', '');
    }

    public function getTotalPayoutFrontendAttribute()
    {
        return number_format($this->attributes['driver_payout'],2, '.', '');
    }

    public function getPayoutStatusAttribute()
    {
        $payout=Payment::where('trip_id',$this->attributes['id']);
        if($payout->count())
        {
            return Payment::where('trip_id',$this->attributes['id'])->first()->driver_payout_status;    
        }
        else
        {
            return "";
        }
        
    } 
    // get begin trip value
    public function getDateAttribute()
    {
        return strtotime($this->attributes['begin_trip']);
    }

    public function getMapImageAttribute()
    {   
        $map_image = @$this->attributes['map_image'];       

        if($map_image != '') {
            $map_image = url('images/map/'.$this->attributes['id'].'/'.$map_image);
        }
        return $map_image;
    }

    public function getTripImageAttribute()
    {   
        $map_image = @$this->attributes['map_image'];       

        if($map_image != '') {
            return url('images/map/'.$this->attributes['id'].'/'.$map_image);
        }
        return "http://maps.googleapis.com/maps/api/staticmap?size=640x480&zoom=14&path=color:0x000000ff%7Cweight:4%7Cenc:".$this->attributes['trip_path']."&markers=size:mid|icon:". url('images/pickup.png')."|".$this->attributes['pickup_latitude'].",".$this->attributes['pickup_longitude']."&markers=size:mid|icon:".url('images/drop.png')."|".$this->attributes['drop_latitude'].",".$this->attributes['drop_longitude']."&sensor=false&key=".MAP_KEY;
    }

    // get trip currency code
    public function getCurrencySymbolAttribute()
    {
        $trips= Trips::where('request_id',$this->attributes['id']);
        if($trips->count())
        {
            $code =  @$trips->get()->first()->currency_code;

           return Currency::where('code',$code)->first()->symbol;
        }
        else
        {
            return "$";
        }
    }

    // get begin trip value with the format: yyyy-mm-dd
    public function getBeginDateAttribute()
    {
        return date('Y-m-d',strtotime($this->attributes['created_at']));
    }
    // get pickup date with the format: Thursday, July 20, 2017 11:58 AM
    public function getPickupDateTimeAttribute()
    {
      return date('l, F d, Y h:i A',strtotime($this->attributes['created_at']));
    }
    // get pickup time with the format: 11:58 AM
    public function getPickupTimeAttribute()
    {
      return date('h:i A',strtotime($this->attributes['begin_trip']));
    }

    // get pickup time with the format: 11:58 AM
    public function getPickupTimeFormattedAttribute()
    {
        $begin_trip = $this->getFormattedTime('begin_trip');
        if($begin_trip == '-') {
            return '';
        }
        return $begin_trip;
    }
    // get drop time with the format: 11:58 AM
    public function getDropTimeAttribute()
    {
      return date('h:i A',strtotime($this->attributes['end_trip']));
    }
    // get Driver name
    public function getDriverNameAttribute()
    {
      return User::find($this->attributes['driver_id'])->first_name;
    }
    // get Rider name
    public function getRiderNameAttribute()
    {
      return User::find($this->attributes['user_id'])->first_name;
    }
    // get Rider Profile Picture
    public function getRiderProfilePictureAttribute()
    {
      $profile_picture=ProfilePicture::where('user_id',$this->attributes['user_id'])->first();
      return isset($profile_picture)?$profile_picture->src:url('images/user.jpeg');
    }
    // get DriverThumb image
    public function getDriverThumbImageAttribute()
    {
      $profile_picture=ProfilePicture::find($this->attributes['driver_id']);
      return isset($profile_picture)?$profile_picture->src:url('images/user.jpeg');
    }
    // get DriverThumb image
    public function getRiderThumbImageAttribute()
    {
      $profile_picture=ProfilePicture::find($this->attributes['user_id']);
      return isset($profile_picture)?$profile_picture->src:url('images/user.jpeg');
    }
    // get total trip time
    public function getTripTimeAttribute()
    {      
      $begin_time = new DateTime($this->attributes['begin_trip']);
      $end_time   = new DateTime($this->attributes['end_trip']);
      $timeDiff   = date_diff($begin_time,$end_time);
      return $timeDiff->format('%H').':'.$timeDiff->format('%I').':'.$timeDiff->format('%S');
               
    }
    public function getTotalTripFareAttribute()
    {
        return number_format(($this->attributes['total_fare']-$this->attributes['access_fee']),2, '.', '');    
    }
    public function getTotalInvoiceAttribute()
    {
        return $this->total_fare();
    }
    
    public function getTotalFareAttribute()
    {
        return number_format(($this->attributes['total_fare']),2, '.', ''); 
    }
    public function getDriverPayoutAttribute()
    {
        return number_format(($this->attributes['driver_payout']),2, '.', ''); 
    }
    public function getAccessFeeAttribute()
    {
        return number_format(($this->attributes['access_fee']),2, '.', ''); 
    }
    public function getOweAmountAttribute()
    {
        return number_format(($this->attributes['owe_amount']),2, '.', ''); 
    }
    public function getWalletAmountAttribute()
    {
        return number_format(($this->attributes['wallet_amount']),2, '.', ''); 
    }
    public function getAppliedOweAmountAttribute()
    {
        return number_format(($this->attributes['applied_owe_amount']),2, '.', ''); 
    }
    public function getRemainingOweAmountAttribute()
    {
        return number_format(($this->attributes['remaining_owe_amount']),2, '.', ''); 
    }    
    public function getPromoAmountAttribute()
    {
        return number_format(($this->attributes['promo_amount']),2, '.', ''); 
    }
    public function getDateTimeTripAttribute()
    {
        $full = false;

        $now = new DateTime;
        $ago = new DateTime($this->attributes['created_at']);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => __('messages.date_time.year'),
            'm' => __('messages.date_time.month'),
            'w' => __('messages.date_time.week'),
            'd' => __('messages.date_time.day'),
            'h' => __('messages.date_time.hour'),
            'i' => __('messages.date_time.minute'),
            's' => __('messages.date_time.second'),
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            }
            else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        if($string) {
            return implode(', ', $string) . ' '.__('messages.ago');
        }
        return __('messages.just_now');
    }

    public function getDriverJoinedAtAttribute()
    {
        $full = false;
        $driver_created_at=DB::table('users')->where('id',$this->attributes['driver_id'])->get()->first()->created_at;
        $now = new DateTime;
        $ago = new DateTime($driver_created_at);
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

    // get Formatted Time with the format: 11:58 AM
    public function getFormattedTime($attribute)
    {
        $formatted_time = '-';
        $trip_time = strtotime($this->attributes[$attribute]);
        if($trip_time > 0) {
            $formatted_time = date('g:i A',$trip_time);
        }

        return $formatted_time;
    }

    public function getPeakSubtotalFareAttribute()
    {
        return $this->peak_amount + $this->subtotal_fare;
    }

    public function getWeekDaysAttribute()
    {
        $week_no = 0;
        $year = date('Y', strtotime($this->attributes['created_at']));
        $week_no = date('W', strtotime($this->attributes['created_at']));
        $week_days = \App\Http\Start\Helpers::getWeekDates($year, $week_no);

        return $week_days;
    }

    public function scopeCompanyTripsOnly($query, $company_id)
    {
        $company_trips = $query->whereHas('driver', function ($query) use ($company_id) {
            $query->where('company_id',$company_id);
        });
        return $company_trips;
    }

    public function getCompanyDriverAmountAttribute()
    {
        if($this->driver->company_id == 1) {
           return  $this->driver_payout;
        }
        $payment_mode  = $this->attributes['payment_mode'];

        $subtotal_fare = ($payment_mode == 'Cash' || $payment_mode == 'Cash & Wallet') ? $this->total_fare : $this->subtotal_fare;
        return $subtotal_fare;
    }

    public function getCompanyDriverEarningsAttribute()
    {
        return  $this->driver_or_company_earning;
        /*if($this->driver->company_id == 1) {
        }

        $payment_mode  = $this->attributes['payment_mode'];

        if(in_array($payment_mode,['Cash','Cash & Wallet'])) {
            return $this->total_fare;
        }
        return $this->$this->driver_or_company_earning;*/
    }

    public function getTollFeeReasonAttribute()
    {
        $reason = '';
        if ($this->toll_reason_id) {
            $reason = $this->toll_reason->reason;
        }
        return $reason;
    }

    public function getTripTollFeeReasonAttribute()
    {
        $reason = '';
        if ($this->toll_reason_id && $this->toll_reason_id==1) {
            $reason = $this->trip_toll_reason->reason;
        }

        return $reason;
    }
}
