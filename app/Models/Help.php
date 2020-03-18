<?php

/**
 * Help Model
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Request;

class Help extends Model
{
  use Translatable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'help';

    public $appends = ['category_name', 'subcategory_name'];

    public $translatedAttributes = ['name', 'description'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        if(Request::segment(1) == 'admin') {
            $this->defaultLocale = 'en';
        }
        else {
            $this->defaultLocale = Session::get('language');
        }
    }

    public function getUpdatedAtAttribute(){
        return date('d-m-Y'.' H:i:s',strtotime($this->attributes['updated_at']));
    }

    // Get all Active status records
    public static function active_all()
    {
        return Help::whereStatus('Active')->get();
    }

    public function category()
    {
      return $this->belongsTo('App\Models\HelpCategory','category_id','id');
    }

    public function subcategory()
    {
      return $this->hasMany('App\Models\HelpSubCategory','category_id','category_id');
    }

    public function scopeSubcategory_($query, $id)
    {
      return $query->where('subcategory_id', $id);
    }

    public function getCategoryNameAttribute()
    {
      return HelpCategory::find($this->attributes['category_id'])->category_name_lang;
    }

// question_lang
    public function getQuestionLangAttribute()
    {
      $lan = Session::get('language');
      if($lan=='en')
        return $this->attributes['question'];
      else{ 
         $get = HelpTranslations::where('help_id',$this->attributes['id'])->where('locale',$lan)->first();
         if($get)
          return $get->name;
        else
          return $this->attributes['question'];
      }
    }
// answer_lang
    public function getAnswerLangAttribute()
    {
      $lan = Session::get('language');
      // dd($lan);
      if($lan=='en')
        return $this->attributes['answer'];
      else{ 
         $get = HelpTranslations::where('help_id',$this->attributes['id'])->where('locale',$lan)->first();
         // dd($get);
         if($get)
          return $get->description;
        else
          return $this->attributes['answer'];
      }
    }

    public function getSubcategoryNameAttribute()
    {
      return @HelpSubCategory::find($this->attributes['subcategory_id'])->name_lang;
    }
}
