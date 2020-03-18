<?php

/**
 * Help Category Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Category
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Request;

class HelpCategory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'help_category';   

    public $timestamps = false;

    // Get all Active status records
    public static function active_all()
    {
      return HelpCategory::whereStatus('Active')->get();
    }

    public function subcategory()
    {
      return $this->belongsTo('App\Models\HelpSubCategory','category_id','id');
    }
    public function translate()
    {
      return $this->hasmany('App\Models\HelpCategoryLang','category_id','id');
    }
    public function getHelpCategoryAttribute()
    {
        return HelpCategoryLang::where('category_id',$this->attributes['id'])->get();
    }

    // category_name_lang
    public function getCategoryNameLangAttribute()
    {
      // Not Translate to admin Panel
      if (Request::segment(1) == 'admin') {
        return $this->attributes['name'];
      }

      $lan = Session::get('language');
      if($lan=='en')
        return $this->attributes['name'];
      else{ 
         $get = HelpCategoryLang::where('category_id',$this->attributes['id'])->where('locale',$lan)->first();
         if($get)
          return $get->name;
        else
          return $this->attributes['name'];
      }
    }
    // category_description_lang
    public function getCategoryDescriptionLangAttribute()
    {
      // Not Translate to admin Panel
      if (Request::segment(1) == 'admin') {
        return $this->attributes['description'];
      }

      $lan = Session::get('language');
      if($lan=='en')
       return $this->attributes['description'];
      else{ 
         $get = HelpCategoryLang::where('category_id',$this->attributes['id'])->where('locale',$lan)->first();
         if($get)
          return $get->description;
        else
          return $this->attributes['description'];
      }
    }

}
