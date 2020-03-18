<?php

/**
 * Help Category Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help Category
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\HelpCategoryDataTable;
use App\Models\HelpCategory;
use App\Models\HelpSubCategory;
use App\Models\Help;
use App\Models\HelpCategoryLang;
use App\Models\Language;
use Validator;

class HelpCategoryController extends Controller
{
    /**
     * Load Datatable for Help Category
     *
     * @param array $dataTable  Instance of HelpCategoryDataTable
     * @return datatable
     */
    public function index(HelpCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.help_category.view');
    }

    /**
     * Add a New Help Category
     *
     * @param array $request  Input values
     * @return redirect     to Help Category view
     */
    public function add(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            return view('admin.help_category.add',$data);
        }
        else if($request->submit) {
            // Add Help Category Validation Rules
            $rules = array(
                'name'    => 'required|unique:help_category',
                'status'  => 'required'
            );

            // Add Help Category Validation Custom Names
            $attributes = array(
                'name'    => 'Name',
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

            $help_category = new HelpCategory;

            $help_category->name        = $request->name;
            $help_category->description = isset($request->description) ? $request->description :'';
            $help_category->status      = $request->status;

            $help_category->save();

            foreach($request->translations ?: array() as $translation_data) {
                if($translation_data) {
                    $help_category_lang = new HelpCategoryLang;
                    $help_category_lang->name        = $translation_data['name'];
                    $help_category_lang->description = isset($translation_data['description']) ? $translation_data['description'] : '';
                    $help_category_lang->locale      = $translation_data['locale'];
                    $help_category_lang->category_id = $help_category->id;
                    $help_category_lang->save();
                }
            }

            flashMessage('success', 'Added Successfully');
        }
        return redirect('admin/help_category');
    }

    /**
     * Update Help Category Details
     *
     * @param array $request    Input values
     * @return redirect     to Help Category View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['result'] = HelpCategory::findOrFail($request->id);
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');

            return view('admin.help_category.edit', $data);
        }
        
        if($request->submit) {
            // Edit Help Category Validation Rules
            $rules = array(
                'name'    => 'required|unique:help_category,name,'.$request->id,
                'status'  => 'required'
            );

            // Edit Help Category Validation Custom Fields Name
            $attributes = array(
                'name'    => 'Name',
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

            $help_category = HelpCategory::findOrFail($request->id);

            $help_category->name        = $request->name;
            $help_category->description = isset($request->description) ? $request->description :'';
            $help_category->status      = $request->status;

            $help_category->save();
            $data['locale'][0] = 'en';
            foreach($request->translations ?: array() as $translation_data) {
                if($translation_data) {
                    $get_val = HelpCategoryLang::where('category_id',$help_category->id)->where('locale',$translation_data['locale'])->first();
                    if($get_val) {
                        $help_category_lang = $get_val;
                    }
                    else {
                        $help_category_lang = new HelpCategoryLang;
                    }
                    $help_category_lang->name        = $translation_data['name'];
                    $help_category_lang->description = isset($translation_data['description']) ? $translation_data['description'] : '';
                    $help_category_lang->locale      = $translation_data['locale'];
                    $help_category_lang->category_id     = $help_category->id;
                    $help_category_lang->save();
                    $data['locale'][] = $translation_data['locale'];
                }
            }
            if(@$data['locale']) {
                HelpCategoryLang::where('category_id',$help_category->id)->whereNotIn('locale',$data['locale'])->delete();
            }
            
            flashMessage('success', 'Updated Successfully');
        }
        else
        {
            return redirect('admin/help_category');
        }
    }

    /**
     * Delete Help Category
     *
     * @param array $request    Input values
     * @return redirect     to Help Category View
     */
    public function delete(Request $request)
    {
        $count = Help::where('category_id', $request->id)->count();
        $subcategory_count = HelpSubCategory::where('category_id', $request->id)->count();

        if($count > 0) {
            flashMessage('error', 'Help have this Help Category. So, Delete that Help or Change that Help Help Category.');
        }
        elseif($subcategory_count > 0) {
            flashMessage('error', 'Help Subcategory have this Help Category. So, Delete that Help Subcategory or Change that Help Subcategory.');
        }
        else {
            $help_category = HelpCategory::findOrFail($request->id);
            $help_category->delete();
            HelpCategoryLang::where('category_id',$request->id)->delete();
            flashMessage('success', 'Deleted Successfully');
        }
        return redirect('admin/help_category');
    }
}
