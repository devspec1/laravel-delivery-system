<?php

/**
 * Driver Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\SubscribedDriverDataTable;
use App\Models\User;
use App\Models\Trips;
use App\Models\DriverAddress;
use App\Models\DriverDocuments;
use App\Models\DriversSubscriptions;
use App\Models\StripeSubscriptionsPlans;
use App\Models\Country;
use App\Models\CarType;
use App\Models\ProfilePicture;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\ReferralUser;
use App\Models\ReferralSetting;
use App\Models\DriverOweAmount;
use App\Models\PayoutPreference;
use App\Models\PayoutCredentials;
use Validator;
use DB;
use Image;
use Auth;
use App;

use Illuminate\Support\Facades\Hash;


use App\Http\Start\Helpers;
use App\Models\PasswordResets;
use App\Mail\ForgotPasswordMail;
use Mail;
use URL;

class SubscribedDriverController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');        
    }


    /**
     * Load Datatable for Subscribed Driver
     *
     * @param array $dataTable  Instance of Subscribed Driver DataTable
     * @return datatable
     */
    public function index(SubscribedDriverDataTable $dataTable)
    {
        return $dataTable->render('admin.subscribed_driver.view');
    }

    /**
     * Update Subscribed Driver Details
     *
     * @param array $request    Input values
     * @return redirect     to Subscribed Driver View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['result']        = DriversSubscriptions::find($request->id);
            $data['driver']        = User::find($data['result']->user_id);
            $data['all_plans']     = StripeSubscriptionsPlans::get();

            return view('admin.subscribed_driver.edit', $data);
        }
        
        if($request->submit) {
            // Edit Driver Validation Rules
            $rules = array(
                'plan'       => 'required',
                'status'     => 'required'
            );

            // Edit Driver Validation Custom Fields Name
            // $attributes = array(
            //     'plan'          => trans('messages.user.plan'),
            //     'status'        => trans('messages.user.status'),
            // );

            // Edit Rider Validation Custom Fields message
            // $messages = array(
            //     'required'            => ':attribute is required.',
            //     'mobile_number.regex' => trans('messages.user.mobile_no'),
            // );

            $validator = Validator::make($request->all(), $rules); // $messages, $attributes
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $subscribed_driver = DriversSubscriptions::find($request->id);

            $subscribed_driver->plan        = $request->plan;
            $subscribed_driver->status      = $request->status;

            $subscribed_driver->save();
            
            flashMessage('success', 'Updated Successfully');
        }
        return redirect('admin/subscriptions/driver');
    }
}
