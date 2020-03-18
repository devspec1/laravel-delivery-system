<?php

/**
 * Help SubCategory Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help SubCategory
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Request;

class HelpSubCategory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'help_subcategory';

    public $timestamps = false;

    public $appends = ['category_name'];

    // Get all Active status records
    public static function active_all()
    {
      return HelpSubCategory::whereStatus('Active')->get();
    }

    public function category()
    {
      return $this->belongsTo('App\Models\HelpCategory','id','category_id');
    }
    public function translate()
    {
      return $this->hasmany('App\Models\HelpSubCategoryLang','sub_category_id','id');
    }

    public function getCategoryNameAttribute()
    {
      return HelpCategory::find($this->attributes['category_id'])->name;
    }

    public function getHelpSubCategoryAttribute()
    {
        return HelpSubCategoryLang::where('sub_category_id',$this->attributes['id'])->get();
    }

    // name_lang
    public function getNameLangAttribute()
    {
      // Not Translate to admin Panel
      if (Request::segment(1) == 'admin') {
        return $this->attributes['name'];
      }

      $lan = Session::get('language');
      if($lan=='en')
        return $this->attributes['name'];
      else{ 
         $get = HelpSubCategoryLang::where('sub_category_id',$this->attributes['id'])->where('locale',$lan)->first();
         if($get)
          return $get->name;
        else
          return $this->attributes['name'];
      }
    }
    // description_lang
    public function getDescriptionLangAttribute()
    {
      // Not Translate to admin Panel
      if (Request::segment(1) == 'admin') {
        return $this->attributes['description'];
      }

      $lan = Session::get('language');
      if($lan=='en')
        return $this->attributes['description'];
      else{ 
         $get = HelpSubCategoryLang::where('sub_category_id',$this->attributes['id'])->where('locale',$lan)->first();
         if($get)
          return $get->description;
        else
          return $this->attributes['description'];
      }
    }
}