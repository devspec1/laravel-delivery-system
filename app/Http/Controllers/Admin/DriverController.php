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
use App\DataTables\DriverDataTable;
use App\Models\User;
use App\Models\Trips;
use App\Models\DriverAddress;
use App\Models\DriverDocuments;
use App\Models\Country;
use App\Models\CarType;
use App\Models\ProfilePicture;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\ReferralUser;
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

class DriverController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');
        
    }


    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function index(DriverDataTable $dataTable)
    {
        return $dataTable->render('admin.driver.view');
    }


       /**
     * Import driver from csv
     *
     * @param array $request  csv file
     * @return redirect     to Import Driver view
     */
    public function import_drivers(Request $request)
    {
        if (!$_POST) {
            return view('admin.import_driver.import');
        } else {


            if ($request->input('submit') != null) {

                
          

                $file = $request->file('file');

                // File Details 
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Valid File Extensions
                $valid_extension = array("csv");


                // Check file extension
                if (in_array(strtolower($extension), $valid_extension)) {

                    // File upload location
                    $location = 'uploads';

                    // Upload file
                    $file->move($location, $filename);

                    // Import CSV to Database
                    $filepath = public_path($location . "/" . $filename);


                    // Reading file
                    $file = fopen($filepath, "r");

                    $importData_arr = array();
                    $i = 0;

                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        // Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);

                   
                    // Insert to MySQL database
                    foreach ($importData_arr as $index => $importData) {

                        $user = new User;

                        $referral_code = $importData[1];
                        $first_name = $importData[2];
                        $last_name = $importData[3];
                        $email = $importData[4];
                        $full_mobile_no = $importData[5];
                        $used_referral_code = $importData[6];
                        $profile_pic = $importData[16];

                        $address_line1 = $importData[9];
                        $address_line2 = $importData[10];
                        $city = $importData[11];
                        $state = $importData[12];
                        $postal_code = $importData[13];

                        $vehicle_name = $importData[7];





                        if (!empty($full_mobile_no)) {
                            $country_code = substr($importData[5], 0, 2);
                            $mobile_number = substr($importData[5], 2);
                        } else {
                            $country_code = "00";
                            $mobile_number = "0000000000";
                        }

                        // check email or mobile number is empty then update 
                        if (empty($email) && empty($mobile_number)) {
                            // Do nothing
                        } else if (!empty($email)) {
                            $user_data = User::where('email', $email)->get();

                            if ($user_data->count() === 1) {

                                if (empty($referral_code)) {
                                    $referral_code = 'TEST' . $index;
                                }

                                $updateData = array(
                                    "first_name" => $first_name,
                                    "last_name" => $last_name,
                                    "email" => $email,
                                    "country_code" => $country_code,
                                    "mobile_number" => $mobile_number,
                                    "password" => Hash::make("testpass"),
                                    "user_type" => "Driver",
                                    "company_id" => 1,
                                    "used_referral_code" => $used_referral_code,
                                    "referral_code" => $referral_code,
                                    "status" => 'Car_details'
                                );

                                User::where('email', $email)->update($updateData);

                                $admin_referral_details = \DB::Table('referral_settings')->where('user_type', $user_data[0]->user_type)->get()->pluck('value', 'name');

                                if ($admin_referral_details['apply_referral']) {
                                    $referred_user = User::where('referral_code', $user_data[0]->used_referral_code)->first();
                                    if ($referred_user != '') {
                                        $referal_present = ReferralUser::where('user_id', $referred_user->id)->where('referral_id',$user_data[0]->id )->first();
                                        if($referal_present == null) {
                                            $referrel_user = new ReferralUser;
                                            $referrel_user->referral_id = $user_data[0]->id;
                                            $referrel_user->user_id     = $referred_user->id;
                                            $referrel_user->user_type   = $referred_user->user_type;
                                            $referrel_user->save();
                                        }
                                    }
                                }


                                $userID = $user_data[0]->id;
                                // Upload profile pic

                                $profile_data = ProfilePicture::where('user_id', $userID)->first();

                                if ($profile_data == null) {
                                    $user_pic = new ProfilePicture;

                                    $user_pic->user_id =  $userID;
                                    $user_pic->src = $profile_pic;
                                    $user_pic->photo_source = 'Local';

                                    $user_pic->save();
                                } else {

                                    $updateProfileData = array(
                                        "src" => $profile_pic,
                                        "photo_source" => 'Local'
                                    );
    
                                    ProfilePicture::where('user_id', $userID)->update($updateProfileData);

                                }

                                $driver_address_data = DriverAddress::where('user_id', $userID)->first();

                                if ($driver_address_data == null) {

                                    $user_address = new DriverAddress;

                                    $user_address->user_id =  $userID;
                                    $user_address->address_line1 = $address_line1 ? $address_line1 : '';
                                    $user_address->address_line2 = $address_line2 ? $address_line2 : '';
                                    $user_address->city = $city ? $city : '';
                                    $user_address->state = $state ? $state : '';
                                    $user_address->postal_code = $postal_code ? $postal_code : '';

                                    $user_address->save();
                                } else {
                                    $driver_add_data = array(
                                        "address_line1" => $address_line1 ? $address_line1 : '',
                                        "address_line2" => $address_line2 ? $address_line2 : '',
                                        "city" => $city ? $city : '',
                                        "state" => $state ? $state : '',
                                        "postal_code" => $postal_code ? $postal_code : '',
                                    );
    
                                    DriverAddress::where('user_id', $userID)->update($driver_add_data);
                                }


                                $user = User::find($userID);
                                $user->status = 'Document_details';
                                $user->save();

                                if ($user) {
                                    $vehicle = Vehicle::where('user_id', $user->id)->first();
                                    if ($vehicle == null) {
                                        $vehicle = new Vehicle;
                                        $vehicle->user_id = $user->id;
                                        $vehicle->company_id = $user->company_id;
                                    }
                                    $vehicle->vehicle_name = $vehicle_name;
                                    /* $vehicle->vehicle_number = $request->vehicle_number;
                                    $vehicle->vehicle_id = $request->vehicle_type;
                                    $vehicle->vehicle_type = CarType::find($request->vehicle_type)->car_name; */
                                    $vehicle->status = 'Inactive';
                                    $vehicle->save();

                                    $driver_doc = DriverDocuments::where('user_id', $user->id)->first();
                                    if ($driver_doc == null) {
                                        $driver_doc = new DriverDocuments;
                                        $driver_doc->user_id = $user->id;
                                        $driver_doc->document_count = 0;
                                        $driver_doc->save();
                                    }

                                    $data['country_code'] = $country_code;
                                    $data['mobile_no'] = $mobile_number;

                                    $this->sendMailAndMessage($user, $data);
                                }
                            } else {

                                $user->first_name = $first_name;
                                $user->last_name = $last_name;
                                $user->email = $email;
                                $user->country_code = $country_code;
                                $user->mobile_number = $mobile_number;
                                $user->password = Hash::make("testpass");
                                $user->user_type = "Driver";
                                $user->company_id = 1;
                                $user->used_referral_code = $used_referral_code;

                                $user->status = 'Car_details';
                                $user->save();


                                if (!empty($referral_code)) {
                                    User::where('id', $user->id)->update(array('referral_code' => $referral_code));
                                }



                                // Upload profile pic
                                $profile_data = ProfilePicture::where('user_id', $user->id)->first();

                                if ($profile_data == null) {
                                    $user_pic = new ProfilePicture;

                                    $user_pic->user_id = $user->id;
                                    $user_pic->src = $profile_pic;
                                    $user_pic->photo_source = 'Local';

                                    $user_pic->save();
                                }


                                $driver_address_data = DriverAddress::where('user_id', $user->id)->first();

                                if ($driver_address_data == null) {
                                    $user_address = new DriverAddress;

                                    $user_address->user_id = $user->id;
                                    $user_address->address_line1 = $address_line1 ? $address_line1 : '';
                                    $user_address->address_line2 = $address_line2 ? $address_line2 : '';
                                    $user_address->city = $city ? $city : '';
                                    $user_address->state = $state ? $state : '';
                                    $user_address->postal_code = $postal_code ? $postal_code : '';

                                    $user_address->save();
                                }


                                $user = User::find($user->id);
                                $user->status = 'Document_details';
                                $user->save();

                                if ($user) {
                                    $vehicle = Vehicle::where('user_id', $user->id)->first();
                                    if ($vehicle == null) {
                                        $vehicle = new Vehicle;
                                        $vehicle->user_id = $user->id;
                                        $vehicle->company_id = $user->company_id;
                                    }
                                    $vehicle->vehicle_name = $vehicle_name;
                                    /* $vehicle->vehicle_number = $request->vehicle_number;
                                    $vehicle->vehicle_id = $request->vehicle_type;
                                    $vehicle->vehicle_type = CarType::find($request->vehicle_type)->car_name; */
                                    $vehicle->status = 'Inactive';
                                    $vehicle->save();

                                    $driver_doc = DriverDocuments::where('user_id', $user->id)->first();
                                    if ($driver_doc == null) {
                                        $driver_doc = new DriverDocuments;
                                        $driver_doc->user_id = $user->id;
                                        $driver_doc->document_count = 0;
                                        $driver_doc->save();
                                    }

                                    $data['country_code'] = $country_code;
                                    $data['mobile_no'] = $mobile_number;

                                    $this->sendMailAndMessage($user, $data);
                                }
                            }
                        }
                    }

                    //Send response
                    $this->helper->flash_message('success', 'Succesfully imported'); // Call flash message function

                    return redirect(LOGIN_USER_TYPE . '/import_drivers');
                } else {
                    //Send response
                    $this->helper->flash_message('danger', 'Invalid file type'); // Call flash message function

                    return redirect(LOGIN_USER_TYPE . '/import_drivers');
                }
            }
        }
    }


    public function sendMailAndMessage($user, $data) {
        // Send email  to user
        $data['first_name'] = $user->first_name;

        $token = $data['token'] = str_random(20); // Generate random string values - limit 100
        $url = $data['url'] = URL::to('/') . '/';

        $data['locale']       = App::getLocale();

        $password_resets = new PasswordResets;

        $password_resets->email      = $user->email;
        $password_resets->token      = $data['token'];
        $password_resets->created_at = date('Y-m-d H:i:s');

        $password_resets->save(); // Insert a generated token and email in password_resets table
        $email      = $user->email;
        $content    = [
            'first_name' => $user->first_name,
            'url' => $url,
            'token' => $token
        ];

        // Send Forgot password email to give user email
        Mail::to($email)->queue(new ForgotPasswordMail($content));

        $message = $content['url'].('reset_password?secret='.$content['token']);

        //Send message to user mobile
        if ($data['mobile_no'] != "0000000000" && $data['country_code'] != "00") {
            $this->otp_helper->sendPassResetMsg($data['mobile_no'], $data['country_code'], $message);
        }
    }







    /**
     * Add a New Driver
     *
     * @param array $request  Input values
     * @return redirect     to Driver view
     */
    public function add(Request $request)
    {
        if($request->isMethod("GET")) {
            //Inactive Company could not add driver
            if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }
            $data['country_code_option']=Country::select('long_name','phone_code')->get();
            $data['country_name_option']=Country::pluck('long_name', 'short_name');
            $data['company']=Company::where('status','Active')->pluck('name','id');
            return view('admin.driver.add',$data);
        }

        if($request->submit) {
            // Add Driver Validation Rules
            $rules = array(
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'mobile_number' => 'required|regex:/[0-9]{6}/',
                'password'      => 'required',
                'country_code'  => 'required',
                'user_type'     => 'required',
            
                'status'        => 'required',
                'license_front' => 'required|mimes:jpg,jpeg,png,gif',
                'license_back'  => 'required|mimes:jpg,jpeg,png,gif',
            );
            
            //Bank details are required only for company drivers & Not required for Admin drivers
            if ((LOGIN_USER_TYPE!='company' && $request->company_name != 1) || (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->id!=1)) {
                $rules['account_holder_name'] = 'required';
                $rules['account_number'] = 'required';
                $rules['bank_name'] = 'required';
                $rules['bank_location'] = 'required';
                $rules['bank_code'] = 'required';
            }

            if (LOGIN_USER_TYPE!='company') {
                $rules['company_name'] = 'required';
            }

            // Add Driver Validation Custom Names
            $attributes = array(
                'first_name'    => trans('messages.user.firstname'),
                'last_name'     => trans('messages.user.lastname'),
                'email'         => trans('messages.user.email'),
                'password'      => trans('messages.user.paswrd'),
                'country_code'  => trans('messages.user.country_code'),
                'user_type'     => trans('messages.user.user_type'),
                'status'        => trans('messages.driver_dashboard.status'),
                'license_front' => trans('messages.driver_dashboard.driver_license_front'),
                'license_back'  => trans('messages.driver_dashboard.driver_license_back'),
                'account_holder_name'  => 'Account Holder Name',
                'account_number'  => 'Account Number',
                'bank_name'  => 'Name of Bank',
                'bank_location'  => 'Bank Location',
                'bank_code'  => 'BIC/SWIFT Code',
            );
                // Edit Rider Validation Custom Fields message
            $messages =array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);

            $validator->after(function ($validator) use($request) {
                $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->count();

                $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->count();

                if($user) {
                   $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
                }

                if($user_email) {
                   $validator->errors()->add('email',trans('messages.user.email_exists'));
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = new User;

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->country_code = $request->country_code;
            $user->mobile_number= $request->mobile_number;
            $user->password     = $request->password;
            $user->status       = $request->status;
            $user->user_type    = $request->user_type;
            $user->status       = $request->status;

            if (LOGIN_USER_TYPE=='company') {
                $user->company_id       = Auth::guard('company')->user()->id;
            }
            else {
                $user->company_id       = $request->company_name;
            }
            $user->save();

            $user_pic = new ProfilePicture;
            $user_pic->user_id      =   $user->id;
            $user_pic->src          =   "";
            $user_pic->photo_source =   'Local';
            $user_pic->save();

            $user_address = new DriverAddress;
            $user_address->user_id       =   $user->id;
            $user_address->address_line1 =   $request->address_line1 ? $request->address_line1 :'';
            $user_address->address_line2 =   $request->address_line2 ? $request->address_line2:'';
            $user_address->city          =   $request->city ? $request->city:'';
            $user_address->state         =   $request->state ? $request->state:'';
            $user_address->postal_code   =   $request->postal_code ? $request->postal_code:'';
            $user_address->save();

            if ($user->company_id != null && $user->company_id != 1) {
                $payout_preference = PayoutPreference::firstOrNew(['user_id' => $user->id,'payout_method' => "BankTransfer"]);
                $payout_preference->user_id = $user->id;
                $payout_preference->country = "IN";
                $payout_preference->account_number  = $request->account_number;
                $payout_preference->holder_name     = $request->account_holder_name;
                $payout_preference->holder_type     = "company";
                $payout_preference->paypal_email    = $request->account_number;

                $payout_preference->phone_number    = $request->mobile_number ?? '';
                $payout_preference->branch_code     = $request->bank_code ?? '';
                $payout_preference->bank_name       = $request->bank_name ?? '';
                $payout_preference->bank_location   = $request->bank_location ?? '';
                $payout_preference->payout_method   = "BankTransfer";
                $payout_preference->address_kanji   = json_encode([]);
                $payout_preference->save();

                $payout_credentials = PayoutCredentials::firstOrNew(['user_id' => $user->id,'type' => "BankTransfer"]);
                $payout_credentials->user_id = $user->id;
                $payout_credentials->preference_id = $payout_preference->id;
                $payout_credentials->payout_id = $request->account_number;
                $payout_credentials->type = "BankTransfer";
                $payout_credentials->default = 'yes';

                $payout_credentials->save();
            }

            $user_doc = new DriverDocuments;
            $user_doc->user_id = $user->id;

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/users/'.$user->id;
            $target_path = asset($target_dir).'/';

            if($request->hasFile('license_front')) {
                $license_front = $request->file('license_front');

                $extension = $license_front->getClientOriginalExtension();
                $file_name = "license_front_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($license_front,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $user_doc->license_front = $target_path.$upload_result['file_name'];
            }
            if($request->hasFile('license_back')) {
                $license_back = $request->file('license_back');

                $extension = $license_back->getClientOriginalExtension();
                $file_name = "license_back_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($license_back,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $user_doc->license_back = $target_path.$upload_result['file_name'];
            }
         
            $user_doc->save();
           
            flashMessage('success', trans('messages.user.add_success'));

            return redirect(LOGIN_USER_TYPE.'/driver');
        }

        return redirect(LOGIN_USER_TYPE.'/driver');
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
            $data['result']             = User::find($request->id);
            $data['profile_image'] = ProfilePicture::where('user_id',$request->id)->first();

            //If login user is company then company can edit only that company's driver details
            if($data['result'] && (LOGIN_USER_TYPE!='company' || Auth::guard('company')->user()->id == $data['result']->company_id)) {
                $data['address']            = DriverAddress::where('user_id',$request->id)->first();
                $data['driver_documents']   = DriverDocuments::where('user_id',$request->id)->first();
                $data['country_code_option']=Country::select('long_name','phone_code')->get();
                $data['company']=Company::where('status','Active')->pluck('name','id');
                $data['path']               = url('images/users/'.$request->id);
                return view('admin.driver.edit', $data);
            }

            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/driver'); 
        }


        
        if($request->submit) {
            // Edit Driver Validation Rules
            $rules = array(
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'status'        => 'required',
                // 'mobile_number' => 'required|regex:/[0-9]{6}/',
                'referral_code' => 'required',
                'used_referral_code' => 'nullable',
                'country_code'  => 'required',
                'license_front' => 'mimes:jpg,jpeg,png,gif',
                'license_back'  => 'mimes:jpg,jpeg,png,gif',
            );

            //Bank details are updated only for company's drivers.
            if ((LOGIN_USER_TYPE!='company' && $request->company_name != 1) || (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->id!=1)) {
                $rules['account_holder_name'] = 'required';
                $rules['account_number'] = 'required';
                $rules['bank_name'] = 'required';
                $rules['bank_location'] = 'required';
                $rules['bank_code'] = 'required';
            }

            if (LOGIN_USER_TYPE!='company') {
                $rules['company_name'] = 'required';
            }


            // Edit Driver Validation Custom Fields Name
            $attributes = array(
                'first_name'    => trans('messages.user.firstname'),
                'last_name'     => trans('messages.user.lastname'),
                'email'         => trans('messages.user.email'),
                'status'        => trans('messages.driver_dashboard.status'),
                'mobile_number' => trans('messages.profile.phone'),
                'country_ode'   => trans('messages.user.country_code'),
                'license_front' => trans('messages.signup.license_front'),
                'license_back'  => trans('messages.signup.license_back'),
                'license_front' => trans('messages.user.driver_license_front'),
                'license_back'  => trans('messages.user.driver_license_back'),
                'account_holder_name'  => 'Account Holder Name',
                'account_number'  => 'Account Number',
                'bank_name'  => 'Name of Bank',
                'bank_location'  => 'Bank Location',
                'bank_code'  => 'BIC/SWIFT Code',
            );

            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            if($request->mobile_number!="") {
                $validator->after(function ($validator) use($request) {
                    $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                    if($user) {
                       $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
                    }
                });
            }
           
            $validator->after(function ($validator) use($request) {
                $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                if($user_email) {
                    $validator->errors()->add('email',trans('messages.user.email_exists'));
                }

                //--- Konstantin N edits: refferal checking for coincidence
                $referral_c = User::where('referral_code', $request->referral_code)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                if($referral_c){
                    $validator->errors()->add('referral_code',trans('messages.referrals.referral_exists'));
                }
                
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $country_code = $request->country_code;

            $user = User::find($request->id);

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->status       = $request->status;
            $user->country_code = $country_code;
            $user->referral_code = $request->referral_code;
            

           
            $user->setUsedReferralCodeAttribute($request->used_referral_code);
           


            if($request->mobile_number!="") {
                $user->mobile_number = $request->mobile_number;
            }
            $user->user_type    = $request->user_type;
         
            if($request->password != '') {
                $user->password = $request->password;
            }

            if (LOGIN_USER_TYPE=='company') {
                $user->company_id       = Auth::guard('company')->user()->id;
            }
            else {
                $user->company_id       = $request->company_name;
            }

            Vehicle::where('user_id',$user->id)->update(['company_id'=>$user->company_id]);

            $user->save();

            $user_address = DriverAddress::where('user_id',  $user->id)->first();
            if($user_address == '') {
                $user_address = new DriverAddress;
            }

            $user_address->user_id       = $user->id;
            $user_address->address_line1 = $request->address_line1;
            $user_address->address_line2 = $request->address_line2;
            $user_address->city          = $request->city;
            $user_address->state         = $request->state;
            $user_address->postal_code   = $request->postal_code;
            $user_address->save();

            if ($user->company_id != null && $user->company_id != 1) {
                $payout_preference = PayoutPreference::firstOrNew(['user_id' => $user->id,'payout_method' => "BankTransfer"]);
                $payout_preference->user_id = $user->id;
                $payout_preference->country = "IN";
                $payout_preference->account_number  = $request->account_number;
                $payout_preference->holder_name     = $request->account_holder_name;
                $payout_preference->holder_type     = "company";
                $payout_preference->paypal_email    = $request->account_number;

                $payout_preference->phone_number    = $request->mobile_number ?? '';
                $payout_preference->branch_code     = $request->bank_code ?? '';
                $payout_preference->bank_name       = $request->bank_name ?? '';
                $payout_preference->bank_location   = $request->bank_location ?? '';
                $payout_preference->payout_method   = "BankTransfer";
                $payout_preference->address_kanji   = json_encode([]);
                $payout_preference->save();

                $payout_credentials = PayoutCredentials::firstOrNew(['user_id' => $user->id,'type' => "BankTransfer"]);
                $payout_credentials->user_id = $user->id;
                $payout_credentials->preference_id = $payout_preference->id;
                $payout_credentials->payout_id = $request->account_number;
                $payout_credentials->type = "BankTransfer";                
                $payout_credentials->default = 'yes';
                $payout_credentials->save();
            }

            $user_doc = DriverDocuments::where('user_id',  $user->id)->firstOrNew(['user_id' => $user->id]);

            $user_picture = ProfilePicture::where('user_id',$request->id)->first();

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/users/'.$user->id;
            $target_path = asset($target_dir).'/';

            if($request->hasFile('license_front')) {
                $license_front = $request->file('license_front');

                $extension = $license_front->getClientOriginalExtension();
                $file_name = "license_front_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($license_front,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $user_doc->license_front = $target_path.$upload_result['file_name'];
            }
            if($request->hasFile('license_back')) {
                $license_back = $request->file('license_back');

                $extension = $license_back->getClientOriginalExtension();
                $file_name = "license_back_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($license_back,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $user_doc->license_back = $target_path.$upload_result['file_name'];
            }
            if($request->hasFile('profile_image')) {
                $profile_image = $request->file('profile_image');

                $extension = $profile_image->getClientOriginalExtension();
                $file_name = "profile_image".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($profile_image,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $user_picture->src = $target_path.$upload_result['file_name'];
            }
            $user_picture->user_id =$user->id;
            $user_picture->save();
            $user_doc->user_id      = $user->id;                
            $user_doc->save();

            flashMessage('success', 'Updated Successfully');
        }
        return redirect(LOGIN_USER_TYPE.'/driver');
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {
        $result= $this->canDestroy($request->id);

        if($result['status'] == 0) {
            flashMessage('error',$result['message']);
            return back();
        }
        $driver_owe_amount = DriverOweAmount::where('user_id',$request->id)->first();
        if($driver_owe_amount->amount == 0) {
            $driver_owe_amount->delete();
        }
        try {
            User::find($request->id)->delete();
        }
        catch(\Exception $e) {
            $driver_owe_amount = DriverOweAmount::where('user_id',$request->id)->first();
            if($driver_owe_amount == '') {
                DriverOweAmount::create([
                    'user_id' => $request->id,
                    'amount' => 0,
                    'currency_code' => 'USD',
                ]);
            }
            flashMessage('error','Driver have some trips, So can\'t delete this driver');
            // flashMessage('error',$e->getMessage());
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect(LOGIN_USER_TYPE.'/driver');
    }

    // Check Given User deletable or not
    public function canDestroy($user_id)
    {
        $return  = array('status' => '1', 'message' => '');

        //Company can delete only this company's drivers.
        if(LOGIN_USER_TYPE=='company') {
            $user = User::find($user_id);
            if ($user->company_id != Auth::guard('company')->user()->id) {
                $return = ['status' => 0, 'message' => 'Invalid ID'];
                return $return;
            }
        }

        $driver_trips   = Trips::where('driver_id',$user_id)->count();
        $user_referral  = ReferralUser::where('user_id',$user_id)->orWhere('referral_id',$user_id)->count();

        if($driver_trips) {
            $return = ['status' => 0, 'message' => 'Driver have some trips, So can\'t delete this driver'];
        }
        else if($user_referral) {
            $return = ['status' => 0, 'message' => 'Rider have referrals, So can\'t delete this driver'];
        }
        return $return;
    }


}
