<?php
/**
 * Language Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    ApiCredential
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'language';

    public $timestamps = false;

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
