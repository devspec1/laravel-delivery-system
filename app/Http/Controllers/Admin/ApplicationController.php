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
use App\DataTables\ApplicationDriverDataTable;
use App\DataTables\ApplicationMerchantDataTable;
use App\Models\Application;
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
use App\Mail\MailQueue;
use Mail;
use URL;

class ApplicationController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
		$this->request_helper = resolve("App\Http\Helper\RequestHelper");     
    }


    /**
     * Load Application for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function driver(ApplicationDriverDataTable $dataTable)
    {
        return $dataTable->render('admin.application.view_driver');
    }

    /**
     * Load Application for Merchant
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function merchant(ApplicationMerchantDataTable $dataTable)
    {
        return $dataTable->render('admin.application.view_merchant');
    }

    public function active_driver($application_id) {
        $application = Application::find($application_id);
        $user = User::find($application->user_id);
        $user->status = 'Active';
        $user->save();

        $data['first_name'] = $user->first_name;
        $data['content']    = 'Your submission was approved';
        $data['subject']    = 'Application Approved';
        $data['view_file']  = 'emails.application_approved';

        // return view($data['view_file'],$data);
        Mail::to($user->email,$user->first_name)->queue(new MailQueue($data));

        $text = 'Your submission was approved.';
        $to = '+'.$user->country_code.$user->mobile_number;
        $message_responce=$this->request_helper->send_message($to,$text);

        return redirect('admin/application_driver');
    }

    public function active_merchant($application_id) {
        $application = Application::find($application_id);
        $user = User::find($application->user_id);
        $user->status = 'Active';
        $user->save();

        $data['first_name'] = $user->first_name;
        $data['content']    = 'Your submission was approved';
        $data['subject']    = 'Application Approved';
        $data['view_file']  = 'emails.application_approved';

        // return view($data['view_file'],$data);
        Mail::to($user->email,$user->first_name)->queue(new MailQueue($data));

        $text = 'Your submission was approved.';
        $to = '+'.$user->country_code.$user->mobile_number;
        $message_responce=$this->request_helper->send_message($to,$text);

        return redirect('admin/application_merchant');
    }
}
