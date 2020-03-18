<?php

/**
 * Help Category Lang Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Category Lang
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 

class HelpCategoryLang extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'help_category_lang';

    public $timestamps = false;

    protected $fillable = ['name', 'description'];

    public function language() {
        return $this->belongsTo('App\Models\Language','locale','value');
    }
}
