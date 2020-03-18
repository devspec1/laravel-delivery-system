<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Start\Helpers;
use App\Models\EmailSettings;
use App\Models\User;
use App\Models\Company;
use App\Mail\MailQueue;
use Validator;
use Mail;

class EmailController extends Controller
{
	protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load View and Update Email Settings Data
     *
     * @return redirect     to email_settings
     */
    public function index(Request $request)
    {
        if($request->method() != 'POST') {
            $data['result'] = EmailSettings::get();
            return view('admin.email.email_settings', $data);
        }
        else if($request->submit) {
            $user =($request->driver=='mailgun') ? 'domain' : 'username' ;
            $pass =($request->driver=='mailgun') ? 'secret' : 'password' ;
            $username =($request->driver=='mailgun') ? 'Domain' : 'Username' ;
            $password =($request->driver=='mailgun') ? 'Secret' : 'Password' ;
            // Email Settings Validation Rules
            $rules = array(
	                    'driver'       => 'required|in:smtp,sendmail,mailgun,mandrill,ses,sparkpost',
	                    'host'         => 'required',
	                    'port'         => 'required',
	                    'from_address' => 'required',
	                    'from_name'    => 'required',
	                    'encryption'   => 'required',
	                    $user         => 'required',
	                    $pass         => 'required'
	                );

            // Email Settings Validation Custom Names
            $niceNames = array(
	                        'driver'       => 'Driver',
	                        'host'         => 'Host',
	                        'port'         => 'Port',
	                        'from_address' => 'From Address',
	                        'from_name'    => 'From Name',
	                        'encryption'   => 'Encryption',
	                        $user          => $username,
	                        $pass          => $password
	                    );

            $messages = [ 'in' => 'Enter Valid :attribute.',];
            $validator = Validator::make($request->all(), $rules,$messages);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else {
                EmailSettings::where(['name' => 'driver'])->update(['value' => $request->driver]);
                EmailSettings::where(['name' => 'host'])->update(['value' => $request->host]);
                EmailSettings::where(['name' => 'port'])->update(['value' => $request->port]);
                EmailSettings::where(['name' => 'from_address'])->update(['value' => $request->from_address]);
                EmailSettings::where(['name' => 'from_name'])->update(['value' => $request->from_name]);
                EmailSettings::where(['name' => 'encryption'])->update(['value' => $request->encryption]);
                if($request->driver == 'mailgun') {
                	EmailSettings::where(['name' => 'domain'])->update(['value' => $request->domain]);
                	EmailSettings::where(['name' => 'secret'])->update(['value' => $request->secret]);
                }
                else {
                	EmailSettings::where(['name' => 'username'])->update(['value' => $request->username]);
                	EmailSettings::where(['name' => 'password'])->update(['value' => $request->password]);
                }

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function
                return redirect('admin/email_settings');
            }
        }
        else {
            return redirect('admin/email_settings');
        }
    }

    /**
     * Send Email to users
     *
     * @return redirect     to send_email
     */
    public function send_email(Request $request)
    {
        if($request->isMethod('GET')) {
            
            $results = User::select('email','user_type')->get();

            $user_mails = $results->map( function($user) {
                return $user->user_type.' - '.$user->email;
            })->toArray();

            $results = Company::select('email')->where('id','!=',1) ->get();
            $comp_mails = $results->map( function($user) {
                return ['Company - '.$user->email];
            })->toArray();

            $result = array_merge($user_mails,$comp_mails);

            $data['email_address_list'] = json_encode($result);
            return view('admin.email.send_email', $data);
        }
        else if($request->submit) {
            // Send Email Validation Rules
            $rules = array(
                'subject' => 'required',
                'message' => 'required',
            );

            if($request->to != 'to_all') {
                $rules['email'] = 'required';
            }

            // Send Email Validation Custom Names
            $attributes = array(
                'subject' => 'Subject',
                'message' => 'Message',
                'email'   => 'Email',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

        	$result = array();
            if($request->to == 'to_all') {
                $results = User::select('email','user_type')->get();

                $user_mails = $results->map( function($user) {
                    return [$user->user_type,$user->email];
                })->toArray();

                $results = Company::select('email')->where('id','!=',1) ->get();
                $comp_mails = $results->map( function($user) {
                    return ['Company',$user->email];
                })->toArray();

                $result = array_merge($user_mails,$comp_mails);
            }
            else {
                $results = collect(explode(',', rtrim($request->email,',')));
                $result = $results->map( function($email) {
                    $email_value = explode(' - ', $email);
                    if(isset($email_value[1])) {
                        return [trim($email_value[0]),trim($email_value[1])];
                    }
                    return ['Rider',trim($email_value[0])];
                })->toArray();
            }

            $emails         = $result;
            $data['url'] 	= url('/').'/';
            $data['locale'] = \App::getLocale();

            foreach ($emails as $email) {
                if($email[0] == 'Company') {
                    $user = Company::where('email', $email[1])->first();
                    if($user != '') {
                        $email = $user->email;
                        $first_name = $user->name;
                    }
                }
                else {
                    $user = User::where('email', $email[1])->first();
                    if($user != '') {
                        $email = $user->email;
                        $first_name = $user->first_name;
                    }
                }
                if($user != '') {
                    $data['first_name'] = $first_name;
                    $data['content']    = $request->message;
                    $data['subject']    = $request->subject;
                    $data['view_file']  = 'emails.custom_email';

                    // return view($data['view_file'],$data);
                    Mail::to($email,$first_name)->queue(new MailQueue($data));
                }
            }

            $this->helper->flash_message('success', 'Email Sent Successfully');

            return redirect('admin/send_email');
        }
    }
}
