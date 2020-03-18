<?php

/**
 * Rating Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Rating
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rating';

    protected $fillable = ['user_id','trip_id','driver_id','rider_rating','rider_comments','driver_rating','driver_comments'];

    public function rider()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    // Join with user table
    public function driver()
    {
        return $this->belongsTo('App\Models\User','driver_id','id');
    }
    // Join with trip table
    public function trip()
    {
        return $this->belongsTo('App\Models\Trips','trip_id','id');
    }
    
   
}
