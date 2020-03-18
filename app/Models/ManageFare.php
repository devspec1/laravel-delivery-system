<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageFare extends Model
{
    use CurrencyConversion;
    
    public $timestamps = false;
    public $table = 'manage_fare';

    protected $convert_fields = ['base_fare', 'min_fare', 'per_min', 'per_km','schedule_fare', 'schedule_cancel_fare','waiting_charge'];

   public $disable_admin_panel_convertion = true;

    // Join with Locations table table
    public function location()
    {
        return $this->belongsTo('App\Models\Location');
    }

    // Join with Car Type table table
    public function car_type()
    {
        return $this->belongsTo('App\Models\CarType','vehicle_id','id');
    }

    public function peak_fare()
    {
        return $this->hasMany('App\Models\PeakFareDetail','fare_id','id')->orderByDesc('day');
    }
}
