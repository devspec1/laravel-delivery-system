<?php

/**
 * Payout Credentials Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Payout Credentials
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutCredentials extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'type'];

    // Return the drivers default payout_preference details
    public function payout_preference()
    {
        return $this->belongsTo('App\Models\PayoutPreference','preference_id','id');
    }

    // Join with users table
	public function users()
    {
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
}