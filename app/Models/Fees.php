<?php

/**
 * Fees Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Fees
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fees extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fees';

    public $timestamps = false;

}
