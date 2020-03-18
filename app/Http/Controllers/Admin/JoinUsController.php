<?php

/**
 * JoinUs Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    JoinUs
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\JoinUs;
use App\Http\Start\Helpers;
use Validator;

class JoinUsController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load View and Update JoinUs Data
     *
     * @return redirect     to join_us
     */
    public function index(Request $request)
    {
        if(!$_POST)
        {
            $data['result'] = JoinUs::get();
            
            return view('admin.join_us', $data);
        }
        else if($request->submit)
        {
            // // JoinUs Validation Rules
            // $rules = array(
                
            //         'facebook'    => 'url',
            //         'google_plus' => 'url',
            //         'twitter'     => 'url',
            //         'linkedin'    => 'url',
            //         'pinterest'   => 'url',
            //         'youtube'     => 'url',
            //         'instagram'   => 'url',
            //         'app_store_rider'   => 'url',
            //         'app_store_driver'   => 'url',
            //         'play_store_rider'   => 'url',
            //         'play_store_driver'   => 'url',
            //         );

            // // JoinUs Validation Custom Names
            // $niceNames = array(

            //             'facebook'    => 'Facebook',
            //             'google_plus' => 'Google Plus',
            //             'twitter'     => 'Twitter',
            //             'linkedin'    => 'Linkedin',
            //             'pinterest'   => 'Pinterest',
            //             'youtube'     => 'Youtube',
            //             'instagram'   => 'Instagram',
            //             'app_store_rider'   => 'App store rider link',
            //             'app_store_driver'   => 'App store driver link',
            //             'play_store_rider'   => 'Play store rider link',
            //             'play_store_driver'   => 'Play store driver link',

            //             );

            // $validator = Validator::make($request->all(), $rules);
            // $validator->setAttributeNames($niceNames); 

            // if ($validator->fails()) 
            // {
            //     return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            // }
            // else
            // {
                JoinUs::where(['name' => 'facebook'])->update(['value' => $request->facebook]);
                JoinUs::where(['name' => 'twitter'])->update(['value' => $request->twitter]);
                JoinUs::where(['name' => 'linkedin'])->update(['value' => $request->linkedin]);
                JoinUs::where(['name' => 'pinterest'])->update(['value' => $request->pinterest]);
                JoinUs::where(['name' => 'youtube'])->update(['value' => $request->youtube]);
                JoinUs::where(['name' => 'instagram'])->update(['value' => $request->instagram]);
                JoinUs::where(['name' => 'app_store_rider'])->update(['value' => $request->app_store_rider]);
                 JoinUs::where(['name' => 'app_store_driver'])->update(['value' => $request->app_store_driver]);
                JoinUs::where(['name' => 'play_store_rider'])->update(['value' => $request->play_store_rider]);
                JoinUs::where(['name' => 'play_store_driver'])->update(['value' => $request->play_store_driver]);


                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function
            
                return redirect('admin/join_us');
            // }
        }
        else
        {
            return redirect('admin/join_us');
        }
    }
}
