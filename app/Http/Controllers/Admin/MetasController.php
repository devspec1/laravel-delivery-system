<?php

/**
 * Metas Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Metas
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\MetasDataTable;
use App\Models\Metas;
use App\Http\Start\Helpers;
use Validator;

class MetasController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Metas
     *
     * @param array $dataTable  Instance of MetasDataTable
     * @return datatable
     */
    public function index(MetasDataTable $dataTable)
    {
        return $dataTable->render('admin.metas.view');
    }

    /**
     * Update Meta Details
     *
     * @param array $request    Input values
     * @return redirect     to Metas View
     */
    public function update(Request $request)
    {
        if(!$_POST)
        {
			$data['result'] = Metas::find($request->id);

            return view('admin.metas.edit', $data);
        }
        else if($request->submit)
        {
            // Edit Metas Validation Rules
            $rules = array(
                    'title'    => 'required'
                    );

            // Edit Metas Validation Custom Fields Name
            $niceNames = array(
                        'title'    => 'Page Title'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $metas = Metas::find($request->id);

			    $metas->title        = $request->title;
			    $metas->description = $request->description;
			    $metas->keywords      = $request->keywords;

                $metas->save();

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function

                return redirect('admin/metas');
            }
        }
        else
        {
            return redirect('admin/metas');
        }
    }

    /**
     * Delete Meta
     *
     * @param array $request    Input values
     * @return redirect     to Metas View
     */
    public function delete(Request $request)
    {
        Metas::find($request->id)->delete();

        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect('admin/metas');
    }
}
