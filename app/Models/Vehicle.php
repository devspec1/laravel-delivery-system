<?php

/**
 * Driver Docuemnts Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Driver Docuemnts
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vehicle';

    public $timestamps = false;

    protected $fillable = ['user_id','company_id','insurance','rc','permit','vehicle_id','vehicle_type','vehicle_name','vehicle_number','document_count'];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    
    public function car_type()
    {
        return $this->belongsTo('App\Models\CarType','vehicle_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
}