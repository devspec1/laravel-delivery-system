<?php

/**
 * Pages Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Pages
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\PagesDataTable;
use App\Models\Pages;
use App\Http\Start\Helpers;
use Validator;

class PagesController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Pages
     *
     * @param array $dataTable  Instance of PagesDataTable
     * @return datatable
     */
    public function index(PagesDataTable $dataTable)
    {
        return $dataTable->render('admin.pages.view');
    }

    /**
     * Add a New Page
     *
     * @param array $request  Input values
     * @return redirect     to Pages view
     */
    public function add(Request $request)
    {
        if(!$_POST)
        {
            return view('admin.pages.add');
        }
        else if($request->submit)
        {
            // Add Page Validation Rules
            $rules = array(
                    'name'    => 'required|unique:pages',
                    'content' => 'required',
                    'footer'  => 'required',
                    'status'  => 'required'
                    );

            
            // Add Page Validation Custom Names
            $niceNames = array(
                        'name'    => 'Name',
                        'content' => 'Content',
                        'footer'  => 'Footer',
                        // 'under'   => 'Under',
                        'status'  => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $pages = new Pages;

                $pages->name    = $request->name;
                $pages->url     = str_slug($request->name, '_');
                $pages->content = $request->content;
                $pages->footer  = $request->footer;
                $pages->status  = $request->status;

                $pages->save();

                $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function
                return redirect('admin/pages');
            }
        }
        else
        {
            return redirect('admin/pages');
        }
    }

    /**
     * Update Page Details
     *
     * @param array $request    Input values
     * @return redirect     to Pages View
     */
    public function update(Request $request)
    {
        if(!$_POST)
        {
            $data['result'] = Pages::find($request->id);
            if($data['result'])
            {
                $data['result'] = Pages::find($request->id);
                return view('admin.pages.edit', $data);
            }
            else
            {
                $this->helper->flash_message('danger', 'Invalid ID'); // Call flash message function
                return redirect('admin/pages');
            }
			
        }
        else if($request->submit)
        {
            // Edit Page Validation Rules
            $rules = array(
                    'name'    => 'required|unique:pages,name,'.$request->id,
                    'content' => 'required',
                    'footer'  => 'required',
                    'status'  => 'required'
                    );

            // Edit Page Validation Custom Fields Name
            $niceNames = array(
                        'name'    => 'Name',
                        'content' => 'Content',
                        'footer'  => 'Footer',
                        // 'under'   => 'Under',
                        'status'  => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $pages = Pages::find($request->id);

                $pages->name    = $request->name;
                $pages->url     = str_slug($request->name, '_');
                $pages->content = $request->content;
                $pages->footer  = $request->footer;
                $pages->under   = $request->under;
                $pages->status  = $request->status;

                $pages->save();

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function

                return redirect('admin/pages');
            }
        }
        else
        {
            return redirect('admin/pages');
        }
    }

    /**
     * Delete Page
     *
     * @param array $request    Input values
     * @return redirect     to Pages View
     */
    public function delete(Request $request)
    {
        Pages::find($request->id)->delete();

        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect('admin/pages');
    }
}
