<?php

/**
 * Driver Owe Amount Model
 *
 * @package    GoferEats
 * @subpackage Model
 * @category   Driver Owe Amount
 * @author     Trioangle Product Team
 * @version    2.1
 * @link       http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverOweAmount extends Model
{
    
    use CurrencyConversion;
    
    protected $fillable = ['user_id', 'amount','currency_code'];
    protected $convert_fields = ['amount'];
    public $timestamps = false;
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
	// Join with Currency table
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_code','code');
    }
}	
