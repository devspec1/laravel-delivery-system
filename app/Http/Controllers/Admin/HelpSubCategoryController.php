<?php

/**
 * Help Sub Category Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Sub Category
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\HelpSubCategoryDataTable;
use App\Models\HelpSubCategory;
use App\Models\Help;
use App\Models\HelpCategory;
use App\Models\HelpSubCategoryLang;
use App\Models\Language;
use App\Http\Start\Helpers;
use Validator;

class HelpSubCategoryController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Help Subcategory
     *
     * @param array $dataTable  Instance of HelpSubCategoryDataTable
     * @return datatable
     */
    public function index(HelpSubCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.help_subcategory.view');
    }

    /**
     * Add a New Help Subcategory
     *
     * @param array $request  Input values
     * @return redirect     to Help Subcategory view
     */
    public function add(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['category'] = HelpCategory::active_all();
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            return view('admin.help_subcategory.add', $data);
        }
        else if($request->submit) {
            // Add Help Subcategory Validation Rules
            $rules = array(
                'name'    => 'required|unique:help_subcategory',
                'category_id'  => 'required',
                'status'  => 'required'
            );

            // Add Help Subcategory Validation Custom Names
            $attributes = array(
                'name'    => 'Name',
                'category_id'  => 'Category',
                'status'  => 'Status'
            );

            foreach($request->translations ?: array() as $k => $translation) {
                $rules['translations.'.$k.'.locale'] = 'required';
                $rules['translations.'.$k.'.name'] = 'required';

                $attributes['translations.'.$k.'.locale'] = 'Language';
                $attributes['translations.'.$k.'.name'] = 'Name';
            }
            $validator = Validator::make($request->all(), $rules,[], $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $help_subcategory = new HelpSubCategory;
            $help_subcategory->name        = $request->name;
            $help_subcategory->category_id = $request->category_id;
            $help_subcategory->description = isset($request->description) ? $request->description :'';
            $help_subcategory->status      = $request->status;
            $help_subcategory->save();

            foreach($request->translations ?: array() as $translation_data) {
                if($translation_data) {
                    $help_category_lang = new HelpSubCategoryLang;
                    $help_category_lang->name        = $translation_data['name'];
                    $help_category_lang->description = isset($translation_data['description']) ? $translation_data['description'] : '';
                    $help_category_lang->locale      = $translation_data['locale'];
                    $help_category_lang->sub_category_id = $help_subcategory->id;
                    $help_category_lang->save();
                }
            }

            flashMessage('success', 'Added Successfully');
        }

        return redirect('admin/help_subcategory');
    }

    /**
     * Update Help Subcategory Details
     *
     * @param array $request    Input values
     * @return redirect     to Help Subcategory View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['category'] = HelpCategory::active_all();
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            $data['result'] = HelpSubCategory::findOrFail($request->id);

            return view('admin.help_subcategory.edit', $data);
        }

        if($request->submit) {
            // Edit Help Subcategory Validation Rules
            $rules = array(
                'name'    => 'required|unique:help_subcategory,name,'.$request->id,
                'category_id'  => 'required',
                'status'  => 'required'
            );

            // Edit Help Subcategory Validation Custom Fields Name
            $attributes = array(
                'name'    => 'Name',
                'category_id'  => 'Category',
                'status'  => 'Status'
            );

            foreach($request->translations ?: array() as $k => $translation) {
                $rules['translations.'.$k.'.locale'] = 'required';
                $rules['translations.'.$k.'.name'] = 'required';

                $attributes['translations.'.$k.'.locale'] = 'Language';
                $attributes['translations.'.$k.'.name'] = 'Name';
            }

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $help_subcategory = HelpSubCategory::findOrFail($request->id);

            $help_subcategory->name        = $request->name;
            $help_subcategory->category_id = $request->category_id;
            $help_subcategory->description = isset($request->description) ? $request->description :'';
            $help_subcategory->status      = $request->status;

            $help_subcategory->save();

           
            $data['locale'][0] = 'en';
            foreach($request->translations ?: array() as $translation_data) {  
                if($translation_data){
                     $get_val = HelpSubCategoryLang::where('sub_category_id',$help_subcategory->id)->where('locale',$translation_data['locale'])->first();
                        if($get_val)
                            $help_category_lang = $get_val;
                        else
                            $help_category_lang = new HelpSubCategoryLang;
                    $help_category_lang->name        = $translation_data['name'];
                    $help_category_lang->description = isset($translation_data['description']) ? $translation_data['description'] : '';
                    $help_category_lang->locale      = $translation_data['locale'];
                    $help_category_lang->sub_category_id     = $help_subcategory->id;
                    $help_category_lang->save();
                    $data['locale'][] = $translation_data['locale'];
                }
            }
            if(@$data['locale']) {
                HelpSubCategoryLang::where('sub_category_id',$help_subcategory->id)->whereNotIn('locale',$data['locale'])->delete();
            }

            flashMessage('success', 'Updated Successfully');
        }

        return redirect('admin/help_subcategory');
    }

    /**
     * Delete Help Subcategory
     *
     * @param array $request    Input values
     * @return redirect     to Help Subcategory View
     */
    public function delete(Request $request)
    {
        $count = Help::where('subcategory_id', $request->id)->count();

        if($count > 0) {
            flashMessage('error', 'Help have this Help Subcategory. So, Delete that Help or Change that Help Help Subcategory.');
            return redirect('admin/help_subcategory');
        }

        $sub_category = HelpSubCategory::findOrFail($request->id);
        $sub_category->delete();
        HelpSubCategoryLang::where('sub_category_id',$request->id)->delete();
        flashMessage('success', 'Deleted Successfully');
        return redirect('admin/help_subcategory');
    }
}