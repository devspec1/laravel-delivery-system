<?php

/**
 * Driver Address Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Driver Address
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAddress extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'driver_address';

    protected $fillable = ['address_line1', 'address_line2', 'city', 'state', 'postal_code','user_id',];

    public $timestamps = false;


   
}
