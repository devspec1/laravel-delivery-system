<?php

/**
 * Rating Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Rating
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\RatingDataTable;
use App\Models\Rating;
use App\Http\Start\Helpers;
use Validator;
use Auth;

class RatingController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Rating
     *
     * @param array $dataTable  Instance of RatingDataTable
     * @return datatable
     */
    public function index(RatingDataTable $dataTable)
    {
        return $dataTable->render('admin.rating.view');
    }

   
    /**
     * Delete Rating
     *
     * @param array $request    Input values
     * @return redirect     to Rating View
     */
    public function delete(Request $request)
    {
      
        Rating::where('id',$request->id)
        ->where(function($query)  {
            //For company user login, only get that company's driver rating
            if(LOGIN_USER_TYPE=='company') {
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                });
            }
        })
        ->delete();
        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function
      
        return redirect(LOGIN_USER_TYPE.'/rating');  //redirect depends on login user is admin or company
    }
}
