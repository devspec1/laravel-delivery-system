<?php

/**
 * Driver Location Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Driver Location
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'driver_location';

    protected $fillable = ['user_id','latitude','longitude','status','car_id'];

    // Join with profile_picture table
    public function car_type()
    {
        return $this->belongsTo('App\Models\CarType','car_id','id');
    }

    // Join with profile_picture table
    public function users()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    // Join with profile_picture table
    public function request()
    {
        return $this->belongsTo('App\Models\Request','user_id','driver_id');
    }

    public function manage_fare()
    {
        return $this->belongsTo('App\Models\ManageFare','car_id','vehicle_id');
    }
}