<?php
/**
 * Language Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    TollReason
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppliedReferrals extends Model
{
    use CurrencyConversion;
    public $timestamps = true;

    public $convert_fields = ['amount'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','amount','currency_code'];    
}
