<?php

/**
 * Help Sub Category Lang Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Sub Category Lang
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 

class HelpSubCategoryLang extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'help_sub_category_lang';

    protected $fillable = ['name', 'description'];

    public $timestamps = false;

    public function language() {
        return $this->belongsTo('App\Models\Language','locale','value');
    }
}
