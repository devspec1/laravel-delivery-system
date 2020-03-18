<?php

/**
 * Car Type Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Car Type
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarType extends Model
{
    // use CurrencyConversion;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'car_type';

    public $timestamps = false;

    // protected $convert_fields = ['base_fare', 'min_fare', 'per_min', 'per_km','schedule_fare', 'schedule_cancel_fare'];

    public $disable_admin_panel_convertion = true;

    public function getVehicleImageAttribute()
    {
        return url('images/car_image/'.$this->attributes['vehicle_image']);   
    }

     public function getActiveImageAttribute()
    {
        $url = \App::runningInConsole() ? SITE_URL : url('/');
        return $url.'/images/car_image/'.$this->attributes['active_image'];   
          
    }

    // Get the active Vehicle Type only
    public function scopeActive($query)
    {
        return $query->whereStatus('Active');
    }

      public function manage_fare()
    {
        return $this->belongsTo('App\Models\ManageFare','id','vehicle_id');
    }

}
