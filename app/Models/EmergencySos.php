<?php

/**
 * Emercency Sos Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    EmergencySOS
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencySos extends Model
{
	/**
	* The database table used by the model.
	*
	* @var string
	*/
    protected $table = 'emergency_sos';
    
    public $timestamps = false;

    protected $appends = ['original_number'];

    public function getMobileNumberAttribute()
    {
        return $this->attributes['country_code'].$this->attributes['mobile_number'];
    }

    public function getOriginalNumberAttribute()
    {
        return $this->attributes['mobile_number'];
    }
}
