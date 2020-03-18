<?php

/**
 * Api Credentials Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Api Credentials
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ApiCredentials;

class ApiCredentialsController extends Controller
{
    /**
     * Load View and Update Api Credentials
     *
     * @return redirect     to api_credentials
     */
    public function index(Request $request)
    {
        if($request->isMethod('GET')) {
            return view('admin.api_credentials');
        }

        // Api Credentials Validation Rules
        $rules = array(
            'google_map_key'        => 'required',
            'google_map_server_key' => 'required',
            'twillo_sid'            => 'required',
            'twillo_token'          => 'required',
            'twillo_from'           => 'required',
            'fcm_server_key'        => 'required',
            'fcm_sender_id'         => 'required',
            'facebook_client_id'    => 'required',
            'facebook_client_secret'=> 'required',
            'google_client'         => 'required',
            'sinch_key'             => 'required',
            'sinch_secret_key'      => 'required',
            'apple_service_id'      => 'required',
            'apple_team_id'         => 'required',
            'apple_key_id'          => 'required',
            'apple_key_file'        => 'valid_extensions:p8',
        );

        $messages = [
            'valid_extensions' => trans('validation.mimes',['values'=>'p8']),
        ];

        $request->validate($rules,$messages);
        
        ApiCredentials::where(['name' => 'key', 'site' => 'GoogleMap'])->update(['value' => $request->google_map_key]);
        ApiCredentials::where(['name' => 'server_key', 'site' => 'GoogleMap'])->update(['value' => $request->google_map_server_key]);

        ApiCredentials::where(['name' => 'server_key', 'site' => 'FCM'])->update(['value' => $request->fcm_server_key]);
        ApiCredentials::where(['name' => 'sender_id', 'site' => 'FCM'])->update(['value' => $request->fcm_sender_id]);

        ApiCredentials::where(['name' => 'sid', 'site' => 'Twillo'])->update(['value' => $request->twillo_sid]);
        ApiCredentials::where(['name' => 'token', 'site' => 'Twillo'])->update(['value' => $request->twillo_token]);
        ApiCredentials::where(['name' => 'from', 'site' => 'Twillo'])->update(['value' => $request->twillo_from]);

        ApiCredentials::where(['name' => 'client_id','site' => 'Facebook'])->update(['value' => $request->facebook_client_id]);
        ApiCredentials::where(['name' => 'client_secret','site' => 'Facebook'])->update(['value' => $request->facebook_client_secret]);

        ApiCredentials::where(['name' => 'stripe_publish_key','site' => 'Stripe'])->update(['value' => $request->stripe_publish_key]); 
        ApiCredentials::where(['name' => 'stripe_secret_key','site' => 'Stripe'])->update(['value' => $request->stripe_secret_key]);

        ApiCredentials::where(['name' => 'service_id','site' => 'Apple'])->update(['value' => $request->apple_service_id]); 
        ApiCredentials::where(['name' => 'team_id','site' => 'Apple'])->update(['value' => $request->apple_team_id]);
        ApiCredentials::where(['name' => 'key_id','site' => 'Apple'])->update(['value' => $request->apple_key_id]);

        if ($request->file('apple_key_file')) {
            $key_file = $request->file('apple_key_file');
            $filename = 'key.txt';

            $success = $key_file->move(public_path(), $filename);

            if (!$success) {
                return back()->withError('Could not upload Image');
            }
            ApiCredentials::where(['name' => 'key_file','site' => 'Apple'])->update(['value' => $filename]);
        }
        
        flashMessage('success', 'Updated Successfully');

        return redirect('admin/api_credentials');
    }
}
