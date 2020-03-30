<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EmailController;
use App\Models\PayoutPreference;
use App\Models\CompanyPayoutPreference;
use App\Models\CompanyPayoutCredentials;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App;
use Session;
use Validator;

class CompanyController extends Controller
{
	public function __construct()
    {
        $this->helper = resolve('App\Http\Start\Helpers');
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
	}

	/**
     * Add a Payout Method and Load Payout Preferences File
     *
     * @param array $request Input values
     * @return redirect to Payout Preferences page and load payout preferences view file
     */
    public function payout_preferences(Request $request)
    {
        $company_id = auth('company')->id();

        $data['country']   = Country::all()->pluck('long_name','short_name');
        $data['currency']   = Currency::all()->pluck('name','name');
        $data['country_list'] = Country::getPayoutCoutries();
        $data['iban_supported_countries'] = Country::getIbanRequiredCountries();
        $data['country_currency'] = $this->helper->getStripeCurrency();
        $data['mandatory']         = PayoutPreference::getAllMandatory();
        $data['branch_code_required'] = Country::getBranchCodeRequiredCountries();

        $payout_methods = getPayoutMethods();

        $payout_credentials = CompanyPayoutCredentials::with('company_payout_preference')->where('company_id', $company_id)->get();

        $payout_data = array();
        foreach ($payout_methods as $method) {
            $payout_credential = $payout_credentials->where('type',snakeToCamel($method,true))->first();
            $preference = optional($payout_credential)->company_payout_preference;
            $payout = array(
                'address1'      => $preference->address1 ?? '',
                'address2'      => $preference->address2 ?? '',
                'city'          => $preference->city ?? '',
                'state'         => $preference->state ?? '',
                'country'       => $preference->country ?? '',
                'postal_code'   => $preference->postal_code ?? '',
                'paypal_email'  => $preference->paypal_email ?? '',
                'currency_code' => $preference->currency_code ?? '',
                'routing_number'=> $preference->routing_number ?? '',
                'account_number'=> $preference->account_number ?? '',
                'holder_name'   => $preference->holder_name ?? '',
                'bank_name'     => $preference->bank_name ?? '',
                'branch_name'   => $preference->branch_name ?? '',
                'branch_code'   => $preference->branch_code ?? '',
                'bank_location' => $preference->address1 ?? '',
            );
            $payout_method = array(
                'id' => optional($payout_credential)->id ?? 0,
                'key' => $method,
                'is_default' => optional($payout_credential)->default == 'yes',
                'value' => snakeToCamel($method),
                'preference_id' => optional($payout_credential)->payout_id,
                "icon"          => asset("images/icon/".$method.".png"),
                'payout_data' => $payout,
            );
            $payout_data[] = $payout_method;
        }

        $data['payouts'] = collect($payout_data);

        return view('company_payout', $data);

        flashMessage('success', trans('messages.account.payout_updated'));
        return redirect()->route('company_payout_preference');
    }

    /**
     * Add payout Preferences
     *
     * @param  Post method inputs
     * @return Response in Json
     */
    public function updatePayoutPreference(Request $request)
    {
        $company_id = auth('company')->id();
        $payout_methods = getPayoutMethods();
        $payout_methods = implode(',',$payout_methods);

        $rules = array(
            'payout_method' => 'required|in:'.$payout_methods,
        );

        if ($request->payout_method == 'stripe') {
            $rules['country'] = 'required|exists:country,short_name';
        }

        $messages   = array('required'=> ':attribute '.trans('messages.home.field_is_required').'',);
        $validator = Validator::make($request->all(), $rules,$messages);
        
        if($validator->fails()) {
            flashMessage('danger', $validator->messages()->first());
            return back();
        }

        $country = $request->country;
        $payout_default_count = CompanyPayoutCredentials::where('company_id', $company_id)->where('default', '=', 'yes');
        $account_holder_type = 'company';
        $payout_method = snakeToCamel($request->payout_method,true);
        $payout_service = resolve('App\Services\Payouts\\'.$payout_method.'Payout');

        if ($payout_method == 'Stripe') {
            $account_holder_type = 'individual';

            $request['payout_country'] = $country;
            $iban_supported_country = Country::getIbanRequiredCountries();

            $bank_data = array(
                "country"               => $country,
                "currency"              => $request->currency,
                "account_holder_name"   => $request->account_holder_name,
                "account_holder_type"   => $account_holder_type,
            );

            if (in_array($country, $iban_supported_country)) {
                $request['account_number'] = $request->account_number;
                $bank_data['account_number'] = $request->account_number;
            }
            else {
                if ($country == 'AU') {
                    $request['routing_number'] = $request->bsb;
                }
                elseif ($country == 'HK') {
                    $request['routing_number'] = $request->clearing_code . '-' . $request->branch_code;
                }
                elseif ($country == 'JP' || $country == 'SG') {
                    $request['routing_number'] = $request->bank_code . $request->branch_code;
                }
                elseif ($country == 'GB') {
                    $request['routing_number'] = $request->sort_code;
                }
                $bank_data['routing_number'] = $request['routing_number'];
                $bank_data['account_number'] = $request->account_number;
            }
        }
        else if($payout_method == 'BankTransfer') {
            $request['account_number'] = $request->bank_account_number;
        }

        $validate_data = $payout_service->validateRequest($request);

        if($validate_data) {
            return $validate_data;
        }

        if($request->hasFile('document')) {
            $image = $request->file('document');

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');

            $target_dir = '/images/companies/'.$company_id.'/payout_documents';
            $extension = $image->getClientOriginalExtension();
            $file_name = "payout_document_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $filename = $upload_result['file_name'];
            $document_path = public_path($target_dir.'/'.$filename);
        }

        if($request->hasFile('additional_document')) {
            $image = $request->file('additional_document');

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');

            $target_dir = '/images/companies/'.$company_id.'/payout_documents';
            $extension = $image->getClientOriginalExtension();
            $file_name = "payout_additional_document_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $add_filename = $upload_result['file_name'];
            $add_document_path = public_path($target_dir.'/'.$add_filename);
        }

        if ($payout_method == 'Stripe') {

            $stripe_preference = $payout_service->createPayoutPreference($request);

            if(!$stripe_preference['status']) {
                flashMessage('danger', $stripe_preference['status_message']);
                return back();
            }

            $recipient = $stripe_preference['recipient'];
            if(isset($document_path)) {
                $document_result = $payout_service->uploadDocument($document_path,$recipient->id);
                if(!$document_result['status']) {
                    flashMessage('danger', $document_result['status_message']);
                    return back();
                }
                $stripe_document = $document_result['stripe_document'];
                $payout_service->attachDocumentToRecipient($recipient->id,$recipient->individual->id,$stripe_document,'document');
            }

            if(isset($add_document_path)) {
                $add_document_result = $payout_service->uploadDocument($add_document_path,$recipient->id);
                if(!$add_document_result['status']) {
                    flashMessage('danger', $add_document_result['status_message']);
                    return back();
                }
                $add_stripe_document = $add_document_result['stripe_document'];
                $payout_service->attachDocumentToRecipient($recipient->id,$recipient->individual->id,$add_stripe_document,'additional_document');
            }

            $payout_email = isset($recipient->id) ? $recipient->id : auth('company')->user()->email;
            $payout_currency = $request->currency ?? '';
        }

        if ($payout_method == 'Paypal') {
            $payout_email = $request->email;
            $payout_currency = PAYPAL_CURRENCY_CODE;
        }

        if ($payout_method == 'BankTransfer') {
            $payout_email       = $request->account_number;
            $payout_currency    = "";
            $request['branch_code']= $request->bank_code;
        }

        $payout_preference = CompanyPayoutPreference::firstOrNew(['company_id' => $company_id,'payout_method' => $payout_method]);
        $payout_preference->company_id         = $company_id;
        $payout_preference->country         = $country;
        $payout_preference->currency_code   = $payout_currency;
        $payout_preference->routing_number  = $request->routing_number ?? '';
        $payout_preference->account_number  = $request->account_number ?? '';
        $payout_preference->holder_name     = $request->account_holder_name ?? '';
        $payout_preference->holder_type     = $account_holder_type;
        $payout_preference->paypal_email    = $payout_email;
        $payout_preference->address1    = $request->address1 ?? '';
        $payout_preference->address2    = $request->address2 ?? '';
        $payout_preference->city        = $request->city;
        $payout_preference->state       = $request->state;
        $payout_preference->postal_code = $request->postal_code;
        if (isset($document_path)) {
            $payout_preference->document_id     = $stripe_document ?? '';
            $payout_preference->document_image  = $filename;
        }
        if (isset($add_document_path)) {
            $payout_preference->additional_document_id     = $add_stripe_document ?? '';
            $payout_preference->additional_document_image  = $add_filename;
        }
        $payout_preference->phone_number    = $request->phone_number ?? '';
        $payout_preference->branch_code     = $request->branch_code ?? '';
        $payout_preference->bank_name       = $request->bank_name ?? '';
        $payout_preference->bank_location       = $request->bank_location ?? '';
        $payout_preference->branch_name     = $request->branch_name ?? '';
        $payout_preference->ssn_last_4      = $country == 'US' ? $request->ssn_last_4 : '';
        $payout_preference->payout_method   = $payout_method;
        $payout_preference->address_kanji   = isset($address_kanji) ? json_encode($address_kanji) : json_encode([]);
        $payout_preference->save();

        $payout_credentials = CompanyPayoutCredentials::firstOrNew(['company_id' => $company_id,'type' => $payout_method]);
        $payout_credentials->company_id = $company_id;
        $payout_credentials->preference_id = $payout_preference->id;
        $payout_credentials->payout_id = $payout_email;
        $payout_credentials->type = $payout_method;
        if($payout_credentials->default != 'yes') {
            $payout_credentials->default = $payout_default_count->count() == 0 ? 'yes' : 'no';
        }
        $payout_credentials->save();

        flashMessage('success', __('messages.account.payout_updated'));
        return redirect()->route('company_payout_preference');
    }

    /**
     * Delete Payouts Default Payout Method
     *
     * @param array $request Input values
     * @return redirect to Payout Preferences page
     */
    public function payoutUpdate(Request $request, EmailController $email_controller)
    {

        $rules = array(
            'payout_id' => 'required|exists:company_payout_credentials,id',
            'action'    => 'required|in:default,delete',
        );

        $messages   = array('required'=> ':attribute '.trans('messages.home.field_is_required').'',);
        $validator = Validator::make($request->all(), $rules,$messages);
        if($validator->fails()) {
            flashMessage('danger', $validator->messages()->first());
            return back();
        }

        $payout = CompanyPayoutCredentials::find($request->payout_id);

        if($request->action == 'default') {
            if($payout->default == 'yes') {
                flashMessage('danger', __('messages.account.payout_already_defaulted'));
                return redirect()->route('driver_payout_preference');
            }
            
            CompanyPayoutCredentials::where('company_id',Auth::guard('company')->user()->id)->update(['default'=>'no']);
            $payout->default = 'yes';
            $payout->save();

            flashMessage('success', __('messages.account.payout_defaulted'));
        }

        if($request->action == 'delete') {
            if($payout->default == 'yes') {
                flashMessage('danger', __('messages.account.payout_default'));
                return redirect()->route('company_payout_preference');
            }
            
            CompanyPayoutPreference::find($payout->preference_id)->delete();
            $payout->delete();

            flashMessage('success', __('messages.account.payout_deleted'));
        }

        return redirect()->route('company_payout_preference');
    }
}