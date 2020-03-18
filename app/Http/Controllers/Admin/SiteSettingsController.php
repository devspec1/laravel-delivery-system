<?php

/**
 * Site Settings Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Site Settings
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Start\Helpers;
use App\Models\Currency;
use App\Models\SiteSettings;
use App\Models\PaymentGateway;
use App\Models\Language;
use App\Models\Country;
use Validator;

class SiteSettingsController extends Controller
{
	public function __construct()
	{
		$this->helper = new Helpers;
	}

	/**
	 * Load View and Update Site Settings Data
	 *
	 * @return redirect     to site_settings
	 */
	public function index(Request $request)
	{
		$payment_methods = collect(PAYMENT_METHODS);
		$payment_methods = $payment_methods->reject(function($value) {
			if($value['key'] == 'cash') {
				return false;
			}
			$is_enabled = payment_gateway('is_enabled',ucfirst($value['key']));
			return ($is_enabled != '1');
		});

		if ($request->isMethod('GET')) {
			$data['result'] = SiteSettings::get();

			$data['currency'] = @Currency::codeSelect();
			$data['payment_methods'] = $payment_methods;
			$data['countries'] = Country::codeSelect();
			$data['default_currency'] = @Currency::defaultCurrency()->first()->code;
			return view('admin.site_settings', $data);
		}

		$payment_types = $payment_methods->pluck('key')->implode(',');

		// Site Settings Validation Rules
		$rules = array(
			'site_name' => 'required',
			'logo' => 'image|mimes:jpg,png,jpeg,gif',
			'page_logo' => 'image|mimes:jpg,png,jpeg,gif',
			'favicon' => 'image|mimes:jpg,png,jpeg,gif',
			'default_currency' => 'required',
			'driver_km' => 'required',
			'admin_contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
			'admin_country_code' => 'required',
			'heat_map' => 'required|In:On,Off',
			'trip_default_paymode' => 'required|in:'.$payment_types,
			'update_loc_interval' => 'required',
		);

		if($request->heat_map == 'On') {
			$rules['heat_map_hours'] = 'required|Integer|min:1';
		}

		// Site Settings Validation Custom Names
		$attributes = array(
			'site_name' => 'Site Name',
			'logo' => 'logo Image',
			'logo' => 'Page logo Image',
			'favicon' => 'favicon logo',
			'default_currency' => 'Default Currency',
			'driver_km' => 'Driver Kilo meter',
			'admin_contact' => 'Admin Contact Number',
			'admin_country_code' => 'Country Code',
		);

		$validator = Validator::make($request->all(), $rules,[],$attributes);

		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}

		$image_uploader = resolve('App\Contracts\ImageHandlerInterface');
		$target_dir = '/images/logos';

		if ($request->hasFile('logo')) {
			$image = $request->file('logo');

            $extension = $image->getClientOriginalExtension();
            $file_name = "logo.".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }

			SiteSettings::where(['name' => 'logo'])->update(['value' => $upload_result['file_name']]);
		}

		if ($request->hasFile('page_logo')) {
			$image = $request->file('page_logo');
			
            $extension = $image->getClientOriginalExtension();
            $file_name = "page_logo.".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }

			SiteSettings::where(['name' => 'page_logo'])->update(['value' => $upload_result['file_name']]);
		}

		if ($request->hasFile('favicon')) {
			$image = $request->file('favicon');

            $extension = $image->getClientOriginalExtension();
            $file_name = "favicon.".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }

			SiteSettings::where(['name' => 'favicon'])->update(['value' => $upload_result['file_name']]);
		}

		Currency::where('status', 'Active')->update(['default_currency' => '0']);
		Currency::where('code', $request->default_currency)->update(['default_currency' => '1']);
          
        Language::where('default_language',1)->update(['default_language' => 0]);
        Language::where('value', $request->default_language)->update(['default_language' => 1]);

        PaymentGateway::where(['name' => 'trip_default', 'site' => 'Common'])->update(['value' => $request->trip_default_paymode]);

		SiteSettings::where(['name' => 'site_name'])->update(['value' => $request->site_name]);
		SiteSettings::where(['name' => 'version'])->update(['value' => $request->version]);
		SiteSettings::where(['name' => 'payment_currency'])->update(['value' => $request->payment_currency]);
		SiteSettings::where(['name' => 'location_fare'])->update(['value' => $request->location_fare]);
		SiteSettings::where(['name' => 'head_code'])->update(['value' => $request->head_code]);
		SiteSettings::where(['name' => 'driver_km'])->update(['value' => $request->driver_km]);
		SiteSettings::where(['name' => 'admin_contact'])->update(['value' => $request->admin_contact]);
		SiteSettings::where(['name' => 'admin_country_code'])->update(['value' => $request->admin_country_code]);
		SiteSettings::where(['name' => 'heat_map'])->update(['value' => $request->heat_map]);
		SiteSettings::where(['name' => 'heat_map_hours'])->update(['value' => $request->heat_map_hours]);
		SiteSettings::where(['name' => 'update_loc_interval'])->update(['value' => $request->update_loc_interval]);

		flashMessage('success', 'Updated Successfully');
		return redirect('admin/site_setting');
	}
}
