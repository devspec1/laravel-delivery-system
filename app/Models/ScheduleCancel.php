<?php

/**
 * Cancel Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Cancel
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleCancel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'schedule_cancel';

    protected $fillable = ['id','schedule_ride_id','cancel_reason','cancel_by','cancel_reason_id'];

    public function cancel_reasons(){
    	return $this->hasOne('App\Models\CancelReason','id','cancel_reason_id');
    }


   
}
