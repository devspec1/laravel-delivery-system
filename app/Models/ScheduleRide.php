<?php

/**
 * ScheduleRide Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    ScheduleRide
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRide extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'schedule_ride';

    public $timestamps = false;
    
    protected $appends = ['schedule_display_date','icon','car_name','default_icon','schedule_display_time','currency_symbol','rider_name'];

    // Joins the users Table
    public function users()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    public function driver()
    {
        return $this->hasOne('App\Models\User','id','driver_id');
    }

    public function schedule_cancel()
    {
        return $this->hasOne('App\Models\ScheduleCancel','schedule_ride_id','id');
    }

    public function request()
    {
        return $this->hasOne('App\Models\Request','schedule_id','id')->where('status', 'Accepted');
    }

    // Get Schedule display date and time value
    public function getScheduleDisplayDateAttribute()
    {
        return date('D M j g:i a',strtotime($this->attributes['schedule_date'].''.$this->attributes['schedule_time']));
    }

    // Get Schedule display time value
    public function getScheduleDisplayTimeAttribute()
    {
        return date('g:i a',strtotime($this->attributes['schedule_date'].''.$this->attributes['schedule_time']));
    }

    // Get Icon Attribute
    public function getIconAttribute()
    {
        $caricon =  CarType::find($this->attributes['car_id']);
        return optional($caricon)->icon;
    }
   
    // Get Default Icon Attribute
    public function getDefaultIconAttribute()
    {
        return url('images/user.jpeg');
    }

    // Get the Name of the Car
    public function getCarNameAttribute()
    {
        $caricon =  CarType::find($this->attributes['car_id']);
        return $caricon->car_name;
    }

    // Get the Base fare of the car
    public function getFareEstimationAttribute()
    {
        $estimate = ManageFare::where('vehicle_id',$this->attributes['car_id'])->where('location_id',$this->attributes['location_id'])->first();

        $request_helper = resolve('App\Http\Helper\RequestHelper');

        $get_fare_estimation = $request_helper->GetDrivingDistance($this->attributes['pickup_latitude'], $this->attributes['drop_latitude'], $this->attributes['pickup_longitude'], $this->attributes['drop_longitude']);

        $fare_estimation = 0;

        if ($get_fare_estimation['status'] == "success") {
            if ($get_fare_estimation['distance'] == '') {
                $get_fare_estimation['distance'] = 0;
            }

            $minutes = round(floor(round($get_fare_estimation['time'] / 60)));
            $km = round(floor($get_fare_estimation['distance'] / 1000) . '.' . floor($get_fare_estimation['distance'] % 1000));

            $base_fare = round($estimate->base_fare + $estimate->per_km * $km);

            $fare_estimation = number_format(($base_fare + round($estimate->per_min * $minutes)), 2, '.', '');
        }

        return $fare_estimation;
    }

    // Get the Currency Symbol for Base fare of the car
    public function getCurrencySymbolAttribute()
    {
        $car_type = ManageFare::where('vehicle_id',$this->attributes['car_id'])->where('location_id',$this->attributes['location_id'])->first();

        $currency_symbol = "$";
        if(!is_null($car_type)) {
            $code =  $car_type->currency_code;
            $currency_symbol = Currency::where('code',$code)->first()->symbol;
        }

        return $currency_symbol;
    }

    public function getRiderThumbImageAttribute()
    {
        $profile_picture=ProfilePicture::find($this->attributes['user_id']);
        return isset($profile_picture)?$profile_picture->src: url('images/user.jpeg');
    }

    // get Rider name
    public function getRiderNameAttribute()
    {
        $user_details = User::find($this->attributes['user_id']);
        return optional($user_details)->first_name;
    }

    // get Driver name
    public function getDriverNameAttribute()
    {
        if($this->attributes['driver_id'] == 0) {
            return '';
        }
        $user_details = User::find($this->attributes['driver_id']);
        return optional($user_details)->first_name;
    }
}