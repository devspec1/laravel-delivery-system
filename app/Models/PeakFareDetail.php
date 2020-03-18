<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeakFareDetail extends Model
{
    use CurrencyConversion;
    
    public $timestamps = false;

    // Make Id Column as Mass Assignable
    protected $fillable = ['id','fare_id'];

    public $appends = ['str_day_name'];

     protected $convert_fields = ['price'];

      public $disable_admin_panel_convertion = true;


    // Get the Name of the Day
    public function getStrDayNameAttribute()
    {
    	$day_names = ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    	return $day_names[($this->attributes['day'] or '0')];
    }
}
