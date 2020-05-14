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

class DriverDocuments extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'driver_documents';

    public $timestamps = false;

    protected $fillable = ['user_id','license_front','license_back', 'right_to_work', 'abn_number', 'document_count'];

    /*public function car_type()
    {
        return $this->belongsTo('App\Models\CarType','vehicle_id','id');
    }*/

    // Join with vehicle table
    public function vehicle()
    {
        return $this->hasOne('App\Models\Vehicle','user_id','user_id');
    }

    public function getCarTypeAttribute(){
        return @$this->vehicle->car_type;
    }

    public function getInsuranceAttribute(){
        return @$this->vehicle->insurance;
    }

    public function getRcAttribute(){
        return @$this->vehicle->rc;
    }

    public function getPermitAttribute(){
        return @$this->vehicle->permit;
    }

    public function getVehicleIdAttribute(){
        return @$this->vehicle->vehicle_id;
    }

    public function getVehicleTypeAttribute(){
        return @$this->vehicle->vehicle_type;
    }

    public function getVehicleNameAttribute(){
        return @$this->vehicle->vehicle_name;
    }

    public function getVehicleNumberAttribute(){
        return @$this->vehicle->vehicle_number;
    }
   
}
