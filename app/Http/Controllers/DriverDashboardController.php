<?php

/**
 * Dashboard Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Dashboard
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trips;
use App\Models\DriverAddress;
use App\Models\Request as RideRequest;  
use App\Models\ProfilePicture;
use App\Models\DriverDocuments;
use App\Models\Vehicle;
use Auth;
use App;
use DB;
use Validator;
use PDF;
use session;

class DriverDashboardController extends Controller
{
    public function __construct()
    {
        $this->invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
        $this->helper = resolve('App\Http\Start\Helpers');
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');
    }

    /*
    * Driver Profile
    */
	public function driver_profile()
    {
        $data['result'] = User::find(@Auth::user()->id);
        return view('driver_dashboard.profile',$data);
    }

    /**
     * Driver Download invoice Page
     */
    public function download_invoice(Request $request)
    {
        $trip = Trips::find($request->id);

        $invoice_data = $this->invoice_helper->getWebInvoice($trip);
        
        $pdf = PDF::loadView('dashboard.download_invoice', compact('trip','invoice_data'));
        return $pdf->download('invoice.pdf');
    }

    /**
    * Driver print invoice Page
    */
    public function print_invoice(Request $request)
    {
        $trip = Trips::find($request->id);
        if(!$trip) {
            abort(404);
        }

        $invoice_data = $this->invoice_helper->getWebInvoice($trip);

        return view('dashboard.print_invoice',compact('trip','invoice_data'));
    }

    /**
    *    Driver Profile update
    **/
    public function driver_update_profile(Request $request)
    {
        $rules = array(
            'email'             => 'required|email',
            'mobile_number'     => 'required|numeric|regex:/[0-9]{6}/',
            'profile_image'     => 'mimes:jpg,jpeg,png,gif'
        );
       
        $messages = array(
            'required'                => ':attribute '.trans('messages.home.field_is_required').'',
            'mobile_number.regex'   => trans('messages.user.mobile_no'),
        );

        $attributes = array(
            'email'         => trans('messages.user.email'),
            'mobile_number' => trans('messages.profile.phone'),
            'profile_image' => trans('messages.user.profile_image'),
        );

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->where('id','!=',$request->id)->count();

        if($user_email) {
            return back()->withErrors(['email' => trans('messages.user.email_exists')])->withInput(); // Form calling with Errors and Input values
        }

        $user = User::find($request->id);

        if($request->code) {

            $token_exchange_url = 'https://graph.accountkit.com/'.ACCOUNTKIT_VERSION.'/access_token?'.
            'grant_type=authorization_code'.
            '&code='.$request->code.
            "&access_token=AA|".ACCOUNTKIT_APP_ID."|".ACCOUNTKIT_APP_SECRET;
            $data = $this->helper->doCurl($token_exchange_url);

            if(isset($data['error'])) {                    
                $this->helper->flash_message('danger', $data['error']['message']);
                return redirect('driver_profile');
            }

            $user_id = $data['id'];
            $user_access_token = $data['access_token'];
            $refresh_interval = $data['token_refresh_interval_sec'];

            // Get Account Kit information
            $me_endpoint_url = 'https://graph.accountkit.com/'.ACCOUNTKIT_VERSION.'/me?'.
            'access_token='.$user_access_token;
            $data = $this->helper->doCurl($me_endpoint_url);

            $country_code = $data['phone']['country_prefix'];
            $mobile_number = $data['phone']['national_number'];
            $type ='Driver';

            $check_user = User::where('mobile_number', $mobile_number)->where('user_type', $request->user_type)->where('id','!=',$request->id)->count();

            if($check_user) {
                return back()->withErrors(['mobile_number' => trans('messages.user.mobile_no_exists')])->withInput();
            }

            $user->mobile_number    = $mobile_number;
            $user->country_code     = $country_code;
        }

        $user->email            = $request->email;
        $user->save();

        $driver_address = DriverAddress::where('user_id',$user->id)->first();
        if(!$driver_address)
        {
            $driver_address = new DriverAddress;
            $driver_address->user_id = $user->id;
        }
        $driver_address->city = $request->city ? $request->city : '';
        $driver_address->address_line1 = $request->address_line1 ? $request->address_line1 : '';
        $driver_address->address_line2 = $request->address_line2 ? $request->address_line2 : '';
        $driver_address->state = $request->state ? $request->state : '';
        $driver_address->postal_code = $request->postal_code ? $request->postal_code : '';
        $driver_address->save();

        $user_profile_image = ProfilePicture::find($request->id);
        if(!$user_profile_image)
        {
            $user_profile_image = new ProfilePicture;
            $user_profile_image->user_id = $user->id;
        }

        $user_profile_image->photo_source = 'Local';
        $profile_image          =   $request->file('profile_image');
        $path = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user->id;
                            
        if(!file_exists($path)) 
        {
            mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user->id, 0777, true);
        }
        if($profile_image)
        { 
            $profile_image_extension      =   $profile_image->getClientOriginalExtension();
            $profile_image_filename       =   'profile_image' . time() .  '.' . $profile_image_extension;

            $success = $profile_image->move('images/users/'.$user->id, $profile_image_filename);
            if(!$success) {
                return back()->withError(trans('messages.user.license_image'));
            }
            $user_profile_image->src   =url('images/users').'/'.$user->id.'/'.$profile_image_filename;
            $user_profile_image->save();
        }

        $this->helper->flash_message('success', trans('messages.user.update_success')); // Call flash message function
        return redirect('driver_profile');
    }

    /*
    * Profile upload
    */
    public function profile_upload(Request $request)
    {
        $errors    = array();
        $acceptable = view()->shared('acceptable_mimes');

        if((!in_array($_FILES['file']['type'], $acceptable)) && (!empty($_FILES['file']["type"]))) {
            return ['success' => 'false','status_message' => 'Invalid file type. Only  JPG, GIF and PNG types are accepted.'];
        }

        $user = User::find(@Auth::user()->id);
        $user_profile_image = ProfilePicture::find($user->id);

        if(!$user_profile_image) {
            $user_profile_image = new ProfilePicture;
            $user_profile_image->user_id = $user->id;
        }

        $user_profile_image->photo_source = 'Local';
        $profile_image          =   $request->file('file');
        $path = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user->id;
                            
        if(!file_exists($path)) {
            mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user->id, 0777, true);
        }

        if($profile_image) { 
                $profile_image_extension      =   $profile_image->getClientOriginalExtension();
                $profile_image_filename       =   'profile_image' . time() .  '.' . $profile_image_extension;

                $success = $profile_image->move('images/users/'.$user->id, $profile_image_filename);
                if(!$success) {
                    return back()->withError('Could not upload profile Image');
                }
                $user_profile_image->src   =url('images/users').'/'.$user->id.'/'.$profile_image_filename;
                $user_profile_image->save();
        }

        return ['success' => 'true','profile_url' => $user_profile_image->src,'status_message'=>'Uploaded Successfully'];
    }

    public function documents(Request $request)
    {
        $data['user'] = User::find(@Auth::user()->id);
        return view('driver_dashboard.documents',$data);
    }

    /*
    * Driver document upload
    */
    public function document_upload(Request $request)
    {
        $errors    = array();
        $acceptable = view()->shared('acceptable_mimes');

        if($_FILES[$request->document_type]['name'] == "") {
            return ['status' => 'false','status_message' => trans('validation.required', ['attribute' => 'File'])];               
        }
        
        if((!in_array($_FILES[$request->document_type]['type'], $acceptable)) && (!empty($_FILES[$request->document_type]["type"]))) {
            return ['status' => 'false','status_message' => trans('messages.user.invalid_file_type')];            
        }

        $user_id = $request->id;
        $user_details =  User::find($user_id);
         
        $document_type = $request->document_type;
        $file_name = time().'_'.$_FILES[$request->document_type]['name'];
        $type      = pathinfo($file_name, PATHINFO_EXTENSION);

        $file_tmp  = $_FILES[$request->document_type]['tmp_name'];

            
        $dir_name = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id;        
        $f_name   = dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id.'/'.$file_name;

        if(!file_exists($dir_name)) {
            mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/users/'.$user_id, 0777, true);
        }
        //upload image from temp_file  to server file
        if(move_uploaded_file($file_tmp,$f_name)) {
        } 

        $b_name           = basename($file_name,'.'.$type);
        $normal           = url('/').'/images/users/'.$user_id.'/'.$file_name;

        if ($document_type == 'insurance' || $document_type == 'rc' || $document_type == 'permit') {
            $count = @Vehicle::where('user_id', $user_id)->get();
            if ($count->count()) {
                $document_count = @$count[0]['document_count'] != '' ? $count[0]['document_count'] : '0';

                $document = @$count[0][$document_type] != '' ? $count[0][$document_type] : '';
            }
            else {
                $document_count = '0';
                $document = '';
            }

            if ($document_count < 3 && $document == '') {
                $vehicle_document_count = $document_count + 1;
            }
            else {
                $vehicle_document_count = $document_count;
            }

            if ($vehicle_document_count >= 3) {
                $vehicle_document_count = 3;
            }

            $driver_document_count = @DriverDocuments::where('user_id',$user_id)->first()->document_count;
            //return file based on image size.
            $data = [
                'user_id'           => $user_id,
                'company_id'        => $user_details->company_id,
                $document_type      => $normal,
                'document_count'    => @$vehicle_document_count,
            ];
            if ($driver_document_count==null) {
                DriverDocuments::updateOrCreate(['user_id' => $user_id],['document_count'=>0]);
            }
            Vehicle::updateOrCreate(['user_id' => $user_id], $data);
        }
        else{
            $count = @DriverDocuments::where('user_id', $user_id)->get();
            if ($count->count()) {
                $document_count = @$count[0]['document_count'] != '' ? $count[0]['document_count'] : '0';
                $document = @$count[0][$document_type] != '' ? $count[0][$document_type] : '';
            }
            else {
                $document_count = '0';
                $document = '';
            }

            if ($document_count < 2 && $document == '') {
                $driver_document_count = $document_count + 1;
            }
            else {
                $driver_document_count = $document_count;
            }

            if ($driver_document_count >= 2) {
                $driver_document_count = 2;
            }

            $vehicle_document_count = @Vehicle::where('user_id',$user_id)->first()->document_count;

            //return file based on image size.

            $data = [
                'user_id' => $user_id,
                $document_type => $normal,
                'document_count' => @$driver_document_count,
            ];

            DriverDocuments::updateOrCreate(['user_id' => $user_id], $data);
        }

        if ($driver_document_count == 2 && $vehicle_document_count==3) {
            User::where('id', $user_id)->update(['status' => 'Pending']);
        }
        Vehicle::where('id', $user_id)->update(['status' => 'Inactive']);
                         
        return ['status' => 'true'];
    }

    /*
    * return add vehicle page
    */
    public function add_vehicle()
    {
        return view('driver_dashboard.add_vehicle');
    }

    /*
    * Driver payment page
    */
    public function driver_payment()
    {
        $data['total_earnings'] = Trips::where('driver_id',Auth::id())
                     ->where('status','Completed')
                     ->get()->sum('company_driver_earnings');

        $total_count = RideRequest::where('driver_id',@Auth::user()->id)->count();
        $acceptance_count = RideRequest::where('driver_id',@Auth::user()->id)->where('status','Accepted')->count();
        if($acceptance_count != '0' || $total_count != '0') {
            $data['acceptance_rate'] = round(($acceptance_count/$total_count)*100).'%';
        }
        else {
            $data['acceptance_rate'] = '0%';
        }

        $data['completed_trips'] = Trips::where('driver_id',@Auth::user()->id)->where('status','Completed')->count();
        $data['cancelled_trips'] = Trips::where('driver_id',@Auth::user()->id)->where('status','Cancelled')->count();
        $data['all_trips'] = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)->orderBy('created_at', 'desc');
        $data['all_trips'] = $data['all_trips']->paginate(4)->toJson();
        return view('driver_dashboard.payment',$data);
    }

    /*
    * Driver invoice page
    */
    public function driver_invoice(Request $request)
    {
        $trip = Trips::findOrFail($request->id);

        $invoice_data = $this->invoice_helper->getWebInvoice($trip);
        $all_invoice = false;

        return view('driver_dashboard.invoice',compact('trip','invoice_data','all_invoice')); 
    }

    /*
    * Show all trips
    */
    public function show_invoice(Request $request)
    {
        if($request->limit == 'undefined') {
            return ['status' => false];
        }

        if($request->limit) {
            $data = Trips::where('driver_id',@Auth::user()->id)->with(['currency'])->orderBy('created_at', 'desc')->paginate($request->limit);
            return $data;
        }
        $data['trips'] = Trips::where('driver_id',@Auth::user()->id)->with(['currency'])->orderBy('created_at', 'desc')->paginate(10)->toJson();
        $data['all_invoice'] = true;
        return view('driver_dashboard.invoice',$data);
    }

    public function driver_banking()
    {
        return view('driver_dashboard.banking');
    }

    /*
    * show All Driver Trips 
    */
    public function driver_trip()
    {
        return view('driver_dashboard.trip');
    }

    /*
    * Driver Trip Details
    */
    public function driver_trip_detail(Request $request)
    {
        $trip = Trips::find($request->id);
        if(!$trip) {
            abort(404);
        }
        $invoice_data = $this->invoice_helper->getWebInvoice($trip);
        return view('driver_dashboard.trip_detail',compact('trip','invoice_data'));
    }

    /*
    * Get payment information
    */
    public function ajax_payment(Request $request)    
    {
        if($request->data == 'all') {
            $data['completed_trips'] = Trips::where('driver_id',@Auth::user()->id)
                                ->where('status','Completed')
                                ->count();
            $data['cancelled_trips'] = Trips::where('driver_id',@Auth::user()->id)
                                ->where('status','Cancelled')
                                ->count();
            return $data;
        }
        elseif($request->data == 'current') {
            $from = date('Y-m-d');
            $to   = date('Y-m-d');
            $data['completed_trips'] = Trips::where('driver_id',@Auth::user()->id)
                                ->where('status','Completed')
                                ->where('created_at','>=',$from)
                                ->where('created_at','<=',$to)
                                ->count();
            $data['cancelled_trips'] = Trips::where('driver_id',@Auth::user()->id)
                                ->where('status','Cancelled')
                                ->where('created_at','>=',$from)
                                ->where('created_at','<=',$to)
                                ->count();
            return $data;
        }
        elseif($request->data == 'all_trips') {
            if($request->begin_trip != '' || $request->end_trip != '')
                $data = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)
                        ->where('created_at','>=',$request->begin_trip)
                        ->where('created_at','<=',$request->end_trip)->orderBy('created_at', 'desc');
            else
                $data = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)->orderBy('created_at', 'desc');

            $data =  $data->paginate(4)->toJson();
            return $data;
        }
        elseif($request->data == 'completed_trips') {
            if($request->begin_trip != '' || $request->end_trip != '') {
                $data = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)
                    ->where('created_at','>=',$request->begin_trip)
                    ->where('created_at','<=',$request->end_trip)
                    ->where('status','Completed')->orderBy('created_at', 'desc');
            }
            else {
                $data = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)->where('status','Completed')->orderBy('created_at', 'desc');
            }

            $data =  $data->paginate(4)->toJson();
            return $data;
        }
        elseif($request->data == 'cancelled_trips') {   
            if($request->begin_trip != '' || $request->end_trip != '') {
                $data = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)
                    ->where('created_at','>=',$request->begin_trip)
                    ->where('created_at','<=',$request->end_trip)
                    ->where('status','Cancelled')->orderBy('created_at', 'desc');
            }
            else {
                $data = Trips::with(['currency'])->where('driver_id',@Auth::user()->id)
                    ->where('status','Cancelled')->orderBy('created_at', 'desc');
            }

            $data =  $data->paginate(4)->toJson();
            return $data;
        }
        else {
            $date = explode('/', $request->data);
            $from = date('Y-m-d',strtotime($date[0]));
            $to   = date('Y-m-d',strtotime($date[1]));
            $data['completed_trips'] = Trips::where('driver_id',@Auth::user()->id)
                                ->where('status','Completed')
                                ->where('created_at','>=',$from)
                                ->where('created_at','<=',$to)
                                ->count();
            $data['cancelled_trips'] = Trips::where('driver_id',@Auth::user()->id)
                                ->where('status','Cancelled')
                                ->where('created_at','>=',$from)
                                ->where('created_at','<=',$to)
                                ->count();
            return $data;
        }
    }   

    public function change_mobile_number(Request $request)
    {
        $rules = array(
            'mobile_number' => 'required|numeric|regex:/[0-9]{6}/',
        );

        $messages = array(
            'required'              => ':attribute '.trans('messages.home.field_is_required').'',
            'mobile_number.regex'   => trans('messages.user.mobile_no'),
        );

        $attributes = array(
            'mobile_number' => trans('messages.user.mobile'),
        );
        if ($request->request_type == 'send_otp') {

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
                
            $validator->after(function ($validator) use($request) {
                $user = User::where('mobile_number', $request->mobile_number)->where('country_code', $request->country_code)->where('user_type', Auth::user()->user_type)->count();

                if ($user) {
                    $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists')); // Form calling with Errors and Input values
                }
            });

            if (count($validator->errors())) {
                return json_encode(['status_code' => 0,'messages' => $validator->errors()]);
            }

            $otp_responce = $this->otp_helper->sendOtp($request->mobile_number,$request->country_code);
            if ($otp_responce['status_code'] == 0) {
                $data = [
                    'status_code' => 0,
                    'messages' => ['mobile_number' => [$otp_responce['message']]],
                ];
                return json_encode($data);
            }

            return json_encode(['status_code' => 1,'messages' => 'success']);
        }
        elseif($request->request_type == 'check_otp') {
            $rules['otp'] = 'required';
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if($validator->fails()) {
                $messages = $validator->messages();
                if($messages->has('mobile_number')) {
                    return json_encode([
                        'status_code' => 1,
                        'message' => $validator->messages()->first()
                    ]);
                }
                if($messages->has('otp')) {
                    return json_encode([
                        'status_code' => 0,
                        'message' => $validator->messages()->first()
                    ]);
                }
            }

            $check_otp_responce = $this->otp_helper->checkOtp($request->otp,$request->mobile_number);
            
            if ($check_otp_responce['status_code'] == 1) {
                $user                   = Auth::user();
                $user->mobile_number    = session('signup_mobile');
                $user->country_code     = session('signup_country_code');
                $user->save();
            }
            return json_encode($check_otp_responce);
        }
    }

    /*
    * Shows All Driver Inbox
    */
    public function show_inbox()
    {
        return view('driver_dashboard.inbox');
    }


    /*
    * Shows All Driver Earnings
    */
    public function show_earnings()
    {
        return view('driver_dashboard.earnings');
    }


    /*
    * Shows All Driver DriverTeam
    */
    public function show_driverteam()
    {
        

        return view('driver_dashboard.driverteam');
    }

    /*
    * Shows All Driver Passengers
    */
    public function show_passengers()
    {
        return view('driver_dashboard.passengers');
    }

    /*
    * Shows All Driver Account
    */
    public function show_account()
    {
        return view('driver_dashboard.account');
    }

    /*
    * Shows All Driver Help
    */
    public function show_help()
    {
        $faq_array_1 = array("question" => "What is the booking commission for drivers?" ,
        "answer" => "Zero. Zip. Nada. Drivers earn a combination of fares, profit bonuses and residual income for their membership fee of $9.95 per week." );

        $faq_array_2 = array("question" => "When do we launch / start in my city?" ,
                "answer" => "As a driver owned enterprise, we are in the pre-launch phase in 3 countries which means we are recruiting our base of RODO (driver owners) and complying with all the regulatory requirements in each market. Each country and city has different expected launch dates. Keep connected to learn more as and when we know." );

        $faq_array_3 = array("question" => "Who or what are RODOs?" ,
                "answer" => "RODO is an abbreviation of Ride On Driver Owners. RODOs are member owners of The Ride On Driver Owner Group Pty Ltd (a company registered specifically for drivers). RODOs have all the freedoms to change their workload anytime earning as much as they want." );

        $faq_array_4 = array("question" => "What's the benefit of joining pre-launch?" ,
                    "answer" => "We offer RODOs that join pre-launch a range of benefits that include Founder Status, a free BLACK RIDE ON Card and the maximum earning capacity from our compensation plan including double the residuals on driver team referrals." );



        $faq_array_5 = array("question" => "What is a RODO's compensation plan?" ,
                "answer" => "Earning opportunities at Ride On are exceptional. There are four income streams. First, all drivers will earn the usual fare income for distance and time traveled by a passenger. Secondly, all qualified RODOs can earn up to 10% from the revenue of the owners group through a monthly proﬁt share. This depends on such factors as performance, customer reviews and a number of hours they worked. Thirdly, RODOs who recruit or refer other drivers to Ride On can earn a residual income from a 1% rider on their team's booking income for as long as they continue to drive for Ride On. You can listen to the Compensation Plan here: https://tinyurl.com/w9d93e6" );


        $faq_array_6 = array("question" => "Car inspections, medical check ups, free coffee?" ,
                    "answer" => "Like all other companies operating in your market, we comply with the regulatory requirements of each country, city and state. Car inspections and medical check ups are done by the same 3rd party providers such as Red Books, Job Fit etc. If you already drive for Uber, you will already have those documents and they can be used in your application with Ride On. We don't spend money on fancy offices with free coffee and kids in black t-shirts. We would rather give that money back to our RODOs in profit bonuses. If you want the free coffee, stick to Uber." );

        $faq_array_7 = array("question" => "What is your company registration number?" ,
                    "answer" => "In Australia we trade as Ride On Driver Owners Group under ABN 21 554 054 343 Intercargo Logistics Pty Ltd. This is a 10 year old company originally incorporated by Yossi Lavy, our co-founder." );

        $faq_array_8 = array("question" => "Why did you delete my posts on Facebook?" ,
                    "answer" => "We have a zero tolerance policy for rude, whinny, trolls. If you don't like what we are doing, how we're doing it or if you feel entitled to things and feel you can dump on us and other people's dreams of building something new; you're not for us and more importantly, our customers. Thank you. Love and kisses." );

        $faq = array( $faq_array_1 , $faq_array_2 , $faq_array_3 , $faq_array_4 , $faq_array_5 , $faq_array_6 , $faq_array_7 , $faq_array_8);

        // return response()->json([
        // 'status_code'       => '1' , 
        // 'status_message'    => 'Success',
        // 'faq'        => $faq
        // ]);
        $data['faq_array'] = $faq;
        //return view('', $faq);
        return view('driver_dashboard.help', $data);
    }

    /*
    * Shows Info about Driver's Vehicle
    */
    public function vehicle_view()
    {
		$user = User::where('id', @Auth::user()->id)->first();

        if ($user == '') {
			abort(404);
		}
        // $vehicle_details = array(
    	//     ["key" => "car_id", "value" => @$user->driver_documents->vehicle_id ?: '1'],
    	//     ["key" => "car_type", "value" => $user->car_type],
        //     ["key" => "car_image", "value" => @$user->driver_documents->car_type->vehicle_image],
        //     ["key" => "car_active_image", "value" => @$user->driver_documents->car_type->active_image],
        //     ["key" => "vehicle_name", "value" => @$user->driver_documents->vehicle_name ?? ''],
        //     ["key" => "vehicle_number", "value" => @$user->driver_documents->vehicle_number ?? ''],
        // );
        $vehicle_details['car_id'] = @$user->driver_documents->vehicle_id ?: '1';
        $vehicle_details['car_type'] = $user->car_type;
        $vehicle_details['car_image'] = @$user->driver_documents->car_type->vehicle_image;
        $vehicle_details['car_active_image'] = @$user->driver_documents->car_type->active_image;
        $vehicle_details['vehicle_name'] = @$user->driver_documents->vehicle_name ?? '';
        $vehicle_details['vehicle_number'] = @$user->driver_documents->vehicle_number ?? '';
        
		//return response()->json($vehicle_details);
        return view('driver_dashboard.vehicle_view', $vehicle_details);
    }

    /*
    * Manage membership
    */
    public function membership()
    {
        return view('driver_dashboard.manage_membership');
    }

    
}