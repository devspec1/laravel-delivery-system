<?php

/**
 * Rider Location Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Rider Location
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderLocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rider_location';

    public $timestamps = false;

    protected $guarded = [];
}