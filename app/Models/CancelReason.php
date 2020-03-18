<?php
/**
 * Language Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    CancelReason
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelReason extends Model
{
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reason','status','cancelled_by'];

    public function scopeActive($query) {
        return $query->where('status', 'Active');
    }
    
}
