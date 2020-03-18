<?php

/**
 * Help Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Help
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\HelpDataTable;
use App\Models\Help;
use App\Models\HelpCategory;
use App\Models\HelpSubCategory;
use App\Models\Language;
use Validator;

class HelpController extends Controller
{
    /**
     * Load Datatable for Help
     *
     * @param array $dataTable  Instance of HelpDataTable
     * @return datatable
     */
    public function index(HelpDataTable $dataTable)
    {
        return $dataTable->render('admin.help.view');
    }

    /**
     * Add a New Help
     *
     * @param array $request  Input values
     * @return redirect     to Help view
     */
    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            $data['category'] = HelpCategory::active_all();
            $data['subcategory'] = HelpSubCategory::active_all();
            return view('admin.help.add', $data);
        }
        
        if($request->submit) {
            // Add Help Validation Rules
            $rules = array(
                'question'    => 'required',
                'category_id' => 'required',
                'answer'      => 'required',
                'status'      => 'required'
            );

            // Add Help Validation Custom Names
            $attributes = array(
                'question'    => 'Question',
                'category_id' => 'Category',
                'answer'      => 'Answer',
                'status'      => 'Status'
            );

            $except = array('description');
            foreach($request->translations ?: array() as $k => $translation) {
                $rules['translations.'.$k.'.locale'] = 'required';
                $rules['translations.'.$k.'.name'] = 'required';
                $rules['translations.'.$k.'.description'] = 'required';

                $attributes['translations.'.$k.'.locale'] = 'Language';
                $attributes['translations.'.$k.'.name'] = 'Name';
                $attributes['translations.'.$k.'.description'] = 'Description';
                $except[] = 'translations.'.$k.'.description';
            }
            $validator = Validator::make($request->all(), $rules,[], $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $help = new Help;
            $help->category_id    = $request->category_id;
            $help->subcategory_id = $request->subcategory_id;
            $help->question       = $request->question;
            $help->answer         = $request->answer;
            $help->suggested      = $request->suggested;
            $help->status         = $request->status;

            $help->save();

            foreach($request->translations ?: array() as $translation_data) {  
                $translation = $help->getTranslationById(@$translation_data['locale'], $help->id);
                $translation->name = $translation_data['name'];
                $translation->description = $translation_data['description'];
                $translation->save();
            }

            flashMessage('success', 'Added Successfully');
        }

        return redirect('admin/help');
    }

    /**
     * Update Help Details
     *
     * @param array $request    Input values
     * @return redirect     to Help View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            $data['category'] = HelpCategory::active_all();
            $data['subcategory'] = HelpSubCategory::active_all();
            $data['result'] = Help::findOrFail($request->id);

            return view('admin.help.edit', $data);
        }
        else if($request->submit) {
            // Edit Help Validation Rules
            $rules = array(
                'question'    => 'required',
                'category_id' => 'required',
                'answer'      => 'required',
                'status'      => 'required'
            );

            // Edit Help Validation Custom Fields Name
            $attributes = array(
                'question'    => 'Question',
                'category_id' => 'Category',
                'answer'      => 'Answer',
                'status'      => 'Status'
            );
            $except = array('description');
            foreach($request->translations ?: array() as $k => $translation)
            {
                $rules['translations.'.$k.'.locale'] = 'required';
                $rules['translations.'.$k.'.name'] = 'required';
                $rules['translations.'.$k.'.description'] = 'required';

                $attributes['translations.'.$k.'.locale'] = 'Language';
                $attributes['translations.'.$k.'.name'] = 'Name';
                $attributes['translations.'.$k.'.description'] = 'Description';
                $except[] = 'translations.'.$k.'.description';
            }
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes); 

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $help = Help::findOrFail($request->id);
            $help->category_id    = $request->category_id;
            $help->subcategory_id = $request->subcategory_id;
            $help->question       = $request->question;
            $help->answer         = $request->answer;
            $help->suggested      = $request->suggested;
            $help->status         = $request->status;
            $help->save();

             $removed_translations = explode(',', $request->removed_translations);
            foreach(array_values($removed_translations) as $id) {
                $help->deleteTranslationById($id);
            }

            foreach($request->translations ?: array() as $translation_data) {  
                $translation = $help->getTranslationById(@$translation_data['locale'], $translation_data['id']);
                $translation->name = $translation_data['name'];
                $translation->description = $translation_data['description'];

                $translation->save();
            }

            flashMessage('success', 'Updated Successfully');
        }
        return redirect('admin/help');
    }

    /**
     * Delete Help
     *
     * @param array $request    Input values
     * @return redirect     to Help View
     */
    public function delete(Request $request)
    {
        $help = Help::findOrFail($request->id);
        $help->delete();

        flashMessage('success', 'Deleted Successfully');
        return redirect('admin/help');
    }

    public function ajax_help_subcategory(Request $request)
    {
        $result = HelpSubCategory::where('category_id', $request->id)->get();
        return json_encode($result);
    }
}