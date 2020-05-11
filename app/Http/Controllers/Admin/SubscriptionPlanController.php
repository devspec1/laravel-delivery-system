<?php

/**
 * Subscription Plan Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Subscription Plan
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\SubscriptionPlanDataTable;
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

class SubscriptionPlanController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');        
    }


    /**
     * Load Datatable for Subscription Plan
     *
     * @param array $dataTable  Instance of Subscription Plan DataTable
     * @return datatable
     */
    public function index(SubscriptionPlanDataTable $dataTable)
    {
        return $dataTable->render('admin.subscription_plan.view');
    }

    /**
     * Add a New Subscription Plan
     *
     * @param array $request  Input values
     * @return redirect     to Driver view
     */
    public function add(Request $request)
    {
        if($request->isMethod("GET")) {
            return view('admin.subscription_plan.add');
        }

        if($request->submit) {
            // Add Driver Validation Rules
            $rules = array(
                'plan_id'       => 'required',
                'plan_name'     => 'required',
            );
            
            // Add Driver Validation Custom Names
            // $attributes = array(
            //     'plan_id'       => trans('messages.user.firstname'),
            //     'plan_name'     => trans('messages.user.lastname'),
            // );
            // Edit Rider Validation Custom Fields message
            // $messages =array(
            //     'required'            => ':attribute is required.',
            // );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $plan = new StripeSubscriptionsPlans;

            $plan->plan_id   = $request->plan_id;
            $plan->plan_name    = $request->plan_name;
            $plan->save();

            flashMessage('success', trans('messages.user.add_success'));

            return redirect(LOGIN_USER_TYPE.'/subscriptions/plan');
        }

        return redirect(LOGIN_USER_TYPE.'/subscriptions/plan');
    }

    /**
     * Update Driver Details
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['result'] = StripeSubscriptionsPlans::find($request->id);

            return view('admin.subscription_plan.edit', $data);
        }
        
        if($request->submit) {
            // Edit Driver Validation Rules
            $rules = array(
                'plan_id'       => 'required',
                'plan_name'     => 'required',
            );

            // Edit Driver Validation Custom Fields Name
            // $attributes = array(
            //     'plan_id'       => trans('messages.user.firstname'),
            //     'plan_name'     => trans('messages.user.lastname'),
            // );

            // Edit Rider Validation Custom Fields message
            // $messages = array(
            //     'required'            => ':attribute is required.',
            // );

            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $plan = StripeSubscriptionsPlans::find($request->id);

            $plan->plan_id      = $request->plan_id;
            $plan->plan_name    = $request->plan_name;       

            $plan->save();

            flashMessage('success', 'Updated Successfully');
        }
        return redirect(LOGIN_USER_TYPE.'/subscriptions/plan');
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {
        StripeSubscriptionsPlans::find($request->id)->delete();

        flashMessage('success', 'Deleted Successfully');

        return redirect(LOGIN_USER_TYPE.'/subscriptions/plan');
    }
}
