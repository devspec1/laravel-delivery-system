<?php

/**
 * Cancel Reasons Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Cancel Reasons
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCancelReasonRequest;
use App\Repositories\Repository;
use App\DataTables\CancelReasonDataTable;
use App\Models\CancelReason;
use App\Models\Cancel;
use App\Models\ScheduleCancel;
use App\Http\Start\Helpers;
use Validator;

class CancelReasonController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct(CancelReason $cancel_reason)
    {
        $this->helper = new Helpers;
        $this->model = new Repository($cancel_reason);
    }

    /**
     * Load Datatable for CancelReason
     *
     * @param array $dataTable  Instance of CancelReasonDataTable
     * @return datatable
     */
    public function index(CancelReasonDataTable $dataTable)
    {
        return $dataTable->render('admin.cancel_reason.view');

    }

    /**
     * Add a New CancelReason
     *
     * @param array $request  Input values
     * @return redirect     to CancelReason view
     */
    public function add(StoreCancelReasonRequest $request)
    {
        if(!$_POST)
        {
            return view('admin.cancel_reason.add');
        }
        else if($request->submit)
        {
            $data = $request->all();
            $cancel_reason = $this->model->create($data);
            $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function
            return redirect('admin/cancel-reason');
        }
        else
        {
            return redirect('admin/cancel-reason');
        }
    }

    /**
     * Update CancelReason Details
     *
     * @param array $request    Input values
     * @return redirect     to CancelReason View
     */
    public function update(StoreCancelReasonRequest $request)
    {
        if(!$_POST)
        {
			$data['result'] = $this->model->find($request->id);
            return view('admin.cancel_reason.edit', $data);
        }
        else if($request->submit)
        {
            $data = $request->all();
            $cancel_reason = $this->model->update($data,$request->id);
            
            $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function
            
            return redirect('admin/cancel-reason');
        }
        else
        {
            return redirect('admin/cancel-reason');
        }
    }

    /**
     * Delete CancelReason
     *
     * @param array $request    Input values
     * @return redirect     to CancelReason View
     */
    public function delete(Request $request)
    {
        if(Cancel::where('cancel_reason_id',$request->id)->exists() || ScheduleCancel::where('cancel_reason_id',$request->id)->exists()){
            $this->helper->flash_message('error','Sorry this cancel reason is already in use. So canont delete.');
            return back();
        }

        $active_cancel_reason = CancelReason::active()->count();
        $cancel_reason = $this->model->find($request->id);
        if($cancel_reason->status == 'Active' && $active_cancel_reason<=1){
            $this->helper->flash_message('error','Sorry, Minimum one Active cancel reason is required.');
            return back();
        }
        
        $this->model->delete($request->id);

        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect('admin/cancel-reason');
    }

}
