<?php

/**
 * Company Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Company
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CompanyDataTable;
use App\Models\Country;
use App\Models\Company;
use App\Models\CompanyDocuments;
use App\Models\Vehicle;
use App\Models\CompanyPayoutPreference;
use App\Models\CompanyPayoutCredentials;
use App\Models\ScheduleRide;
use App\Models\User;
use Validator;
use DB;
use Image;
use Auth;

class CompanyController extends Controller
{
    /**
     * Load Datatable for Company
     *
     * @param array $dataTable  Instance of Company DataTable
     * @return datatable
     */
    public function index(CompanyDataTable $dataTable)
    {
        return $dataTable->render('admin.company.view');
    }

    /**
     * Add a New Company
     *
     * @param array $request  Input values
     * @return redirect     to Company view
     */
    public function add(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['country_code_option']=Country::select('long_name','phone_code')->get();
            $data['country_name_option']=Country::pluck('long_name', 'short_name');
            return view('admin.company.add',$data);
        }

        $rules = array(
            'name'    => 'required|unique:companies,name,'.$request->id,
            'email'         => 'required|email',
            'country_code'  => 'required',
            'mobile_number' => 'required|regex:/[0-9]{6}/',
            'status'        => 'required',
            'password'      => 'required|min:6',
            'profile' => 'mimes:jpg,jpeg,png',
            'license'  => 'mimes:jpg,jpeg,png',
            'insurance'     => 'mimes:jpg,jpeg,png',
            'license_exp_date'    => 'nullable|after_or_equal:tomorrow',
            'insurance_exp_date'  => 'nullable|after_or_equal:tomorrow',
            'address_line'  => 'required',
            'postal_code'  => 'required',
            'company_commission' => 'required|numeric|max:100',
        );

        $attributes = array(
            'name'    => 'Name',
            'email'         => 'Email',
            'country_code'  => 'Country Code',
            'mobile_number' => 'Mobile Number',
            'status'        => 'Status',
            'password'      => 'Password',
            'profile' => 'Profile',
            'license'  => 'License',
            'insurance'     => 'Insurance',
            'license_exp_date'    => 'License Expiry Date',
            'insurance_exp_date'  => 'Insurance Expiry Date',
            'address_line'  => 'Address Line',
            'postal_code'  => 'Postal Code',
            'company_commission' => 'Company Commission',
        );
        
        $messages =array(
            'required'            => ':attribute is required.',
            'mobile_number.regex' => trans('messages.user.mobile_no'),
        );

        $validator = Validator::make($request->all(), $rules,$messages,$attributes);

        $validator->after(function ($validator) use($request) {
            $company = Company::where('mobile_number', $request->mobile_number)->count();
            $company_email = Company::where('email', $request->email)->count();

            if($company) {
               $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
            }

            if($company_email) {
               $validator->errors()->add('email',trans('messages.user.email_exists'));
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $company = new Company;
        $company->name          = $request->name;
        $company->vat_number    = $request->vat_number;
        $company->email         = $request->email;
        $company->country_code  = $request->country_code;
        $company->mobile_number = $request->mobile_number;
        $company->password      = $request->password;
        $company->status        = $request->status;
        $company->address       = $request->address_line;
        $company->city          = $request->city;
        $company->state         = $request->state;
        $company->country       = $request->country_code;
        $company->postal_code   = $request->postal_code;
        $company->company_commission  = $request->company_commission;
        $company->save();

        $company_doc = new CompanyDocuments;
        $company_doc->company_id=$company->id;
        $company_doc->license_exp_date=$request->license_exp_date;
        $company_doc->insurance_exp_date=$request->insurance_exp_date;

        $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
        $target_dir = '/images/companies/'.$company->id;
        $target_path = asset($target_dir).'/';

        if($request->hasFile('profile')) {
            $profile    =   $request->file('profile');

            $extension = $profile->getClientOriginalExtension();
            $file_name = "profile_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($profile,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }

            $company->profile = $target_path.$upload_result['file_name'];
            $company->save();
        }

        if($request->hasFile('license')) {
            $license    =   $request->file('license');
            $extension = $license->getClientOriginalExtension();
            $file_name = "license_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($license,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $company_doc->license_photo = $target_path.$upload_result['file_name'];
        }

        if($request->hasFile('insurance')) {
            $insurance = $request->file('insurance'); 
            $extension = $insurance->getClientOriginalExtension();
            $file_name = "insurance_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($insurance,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $company_doc->insurance_photo = $target_path.$upload_result['file_name'];
        }

        $company_doc->save();
       
        flashMessage('success', trans('messages.user.add_success'));

        return redirect(LOGIN_USER_TYPE.'/company');
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
            $data['result']             = Company::find($request->id);

            if (LOGIN_USER_TYPE=='company' && $request->id != Auth::guard('company')->user()->id) {
                abort(404);
            }

            if($data['result']) {
                $data['documents']   = CompanyDocuments::where('company_id',$request->id)->first();
                $data['country_code_option']=Country::select('long_name','phone_code')->get();
                $data['path']               = url('images/users/'.$request->id);
                return view('admin.company.edit', $data);
            }
            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/company');
        }

        $rules = array(
            'name'    => 'required|unique:companies,name,'.$request->id,
            'email'         => 'required|email',
            'country_code'  => 'required',
            'password'      => 'nullable|min:6',
            'profile' => 'mimes:jpg,jpeg,png',
            'license'  => 'mimes:jpg,jpeg,png',
            'insurance'     => 'mimes:jpg,jpeg,png',
            'license_exp_date'    => 'nullable|after_or_equal:tomorrow',
            'insurance_exp_date'  => 'nullable|after_or_equal:tomorrow',
            'address_line'  => 'required',
            'postal_code'  => 'required',
            'mobile_number'  => 'nullable|regex:/[0-9]{6}/',
        );

        //Admin only can update status and company commission.Company could not update
        if (LOGIN_USER_TYPE != 'company') {
            $rules['status'] = 'required';
            if ($request->id != 1) {
                $rules['company_commission'] = 'required|numeric|max:100';
            }
        }

        $attributes = array(
            'name'    => 'Name',
            'email'         => 'Email',
            'country_code'  => 'Country Code',
            'mobile_number' => 'Mobile Number',
            'status'        => 'Status',
            'password'      => 'Password',
            'profile' => 'Profile',
            'license'  => 'License',
            'insurance'     => 'Insurance',
            'license_exp_date'    => 'License Expiry Date',
            'insurance_exp_date'  => 'Insurance Expiry Date',
            'address_line'  => 'Address Line',
            'postal_code'  => 'Postal Code',
            'company_commission' => 'Company Commission',
        );
        
        $messages =array(
            'required'            => ':attribute is required.',
            'mobile_number.regex' => trans('messages.user.mobile_no'),
        );

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        $validator->after(function ($validator) use($request) {
            if ($request->mobile_number != '') {
                $company = Company::where('mobile_number', $request->mobile_number)->where('id','!=',$request->id)->count();

                if($company) {
                   $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
                }
            }

            $company_email = Company::where('email', $request->email)->where('id','!=',$request->id)->count();

            if($company_email) {
               $validator->errors()->add('email',trans('messages.user.email_exists'));
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $company = Company::find($request->id);
        $company->name         = $request->name;
        $company->vat_number   = $request->vat_number;
        $company->email        = $request->email;
        $company->country_code = $request->country_code;
        if($request->mobile_number != "") {
            $company->mobile_number= $request->mobile_number;
        }
        if (isset($request->password)) {
            $company->password = $request->password;
        }
        if (LOGIN_USER_TYPE != 'company') {
            $company->status       = $request->status;
            $company->company_commission  = $request->company_commission;
        }
        $company->address      = $request->address_line;
        $company->city         = $request->city;
        $company->state        = $request->state;
        $company->country      = $request->country_code;
        $company->postal_code  = $request->postal_code;
        $company->save();

        $company_doc = CompanyDocuments::where('company_id',$company->id)->first();
        if ($company_doc == null) {
            $company_doc = new CompanyDocuments();
        }
        $company_doc->company_id=$company->id;
        $company_doc->license_exp_date=$request->license_exp_date;
        $company_doc->insurance_exp_date=$request->insurance_exp_date;

        $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
        $target_dir = '/images/companies/'.$company->id;
        $target_path = asset($target_dir).'/';

        if($request->hasFile('profile')) {
            $profile    =   $request->file('profile');

            $extension = $profile->getClientOriginalExtension();
            $file_name = "profile_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($profile,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }

            $company->profile = $target_path.$upload_result['file_name'];
            $company->save();
        }

        if($request->hasFile('license')) {
            $license    =   $request->file('license');
            $extension = $license->getClientOriginalExtension();
            $file_name = "license_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($license,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $company_doc->license_photo = $target_path.$upload_result['file_name'];
        }

        if($request->hasFile('insurance')) {
            $insurance = $request->file('insurance'); 
            $extension = $insurance->getClientOriginalExtension();
            $file_name = "insurance_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($insurance,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $company_doc->insurance_photo = $target_path.$upload_result['file_name'];
        }

        $company_doc->save();

        flashMessage('success', 'Updated Successfully');
        
        if (LOGIN_USER_TYPE == 'company') {
            return redirect('company/edit_company/'.Auth::guard('company')->user()->id);
        }
        return redirect(LOGIN_USER_TYPE.'/company');
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {     
        if($request->id == 1) {
            flashMessage('danger', 'Could not delete default company');
            return redirect(LOGIN_USER_TYPE.'/company');
        }
        
        $company_drivers = User::where('user_type','Driver')->where('company_id',$request->id)->count();
        
        if($company_drivers>=1) {
            flashMessage('danger', 'Company have some drivers, So can\'t delete this company');
            return redirect(LOGIN_USER_TYPE.'/company');
        }

        $company_schedule = ScheduleRide::where('company_id',$request->id)->count();
        if($company_schedule) {
            flashMessage('danger', 'Company have some schedule rides, So can\'t delete this company');
            return redirect(LOGIN_USER_TYPE.'/company');
        }
        
        Vehicle::where('company_id',$request->id)->delete();
        CompanyDocuments::where('company_id',$request->id)->delete();
        CompanyPayoutPreference::where('company_id',$request->id)->delete();
        CompanyPayoutCredentials::where('company_id',$request->id)->delete();
        
        Company::find($request->id)->delete();
        flashMessage('success', 'Deleted Successfully');
        return redirect(LOGIN_USER_TYPE.'/company');
    }
}