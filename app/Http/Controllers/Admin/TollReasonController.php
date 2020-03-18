<?php

/**
 * Toll Reasons Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Toll Reasons
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Repository;
use App\DataTables\TollReasonDataTable;
use App\Http\Start\Helpers;
use App\Http\Requests\StoreTollReasonRequest;
use Validator;
use App\Models\TollReason;
use App\Models\Trips;

class TollReasonController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct(TollReason $toll_reason)
    {
        $this->helper = new Helpers;
        $this->model = new Repository($toll_reason);
        $this->base_url = url('admin/additional-reasons');
    }

    /**
     * Load Datatable for CancelReason
     *
     * @param array $dataTable  Instance of CancelReasonDataTable
     * @return datatable
     */
    public function index(TollReasonDataTable $dataTable)
    {
        return $dataTable->render('admin.toll_reason.view');

    }

    /**
     * Add a New TollReason
     *
     * @param array $request  Input values
     * @return redirect     to TollReason view
     */
    public function add(StoreTollReasonRequest $request)
    {
        if(!$_POST)
        {
            return view('admin.toll_reason.add');
        }
        else if($request->submit)
        {
            $data = $request->all();
            $toll_reason = $this->model->create($data);
            $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function
            return redirect($this->base_url);
        }
        else
        {
            return redirect($this->base_url);
        }
    }

    /**
     * Update TollReason Details
     *
     * @param array $request    Input values
     * @return redirect     to TollReason View
     */
    public function update(StoreTollReasonRequest $request)
    {
        if(!$_POST)
        {
			$data['result'] = $this->model->find($request->id);
            return view('admin.toll_reason.edit', $data);
        }
        else if($request->submit)
        {
            $data = $request->all();
            $toll_reason = $this->model->update($data,$request->id);
            
            $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function
            
            return redirect($this->base_url);
        }
        else
        {
            return redirect($this->base_url);
        }
    }

    /**
     * Delete TollReason
     *
     * @param array $request    Input values
     * @return redirect     to TollReason View
     */
    public function delete(Request $request)
    {
        if(Trips::where('toll_reason_id',$request->id)->exists()){
            $this->helper->flash_message('error','Sorry this cancel reason is already in use. So canont delete.');
            return back();
        }

        $active_toll_reason = TollReason::active()->count();
        $toll_reason = $this->model->find($request->id);
        if($toll_reason->status == 'Active' && $active_toll_reason<=1){
            $this->helper->flash_message('error','Sorry, Minimum one Active additional reason is required.');
            return back();
        }
        
        $this->model->delete($request->id);

        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect($this->base_url);
    }

}
