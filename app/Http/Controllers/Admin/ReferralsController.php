<?php

/**
 * Referrals Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Referrals
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReferralUser;
use App\DataTables\ReferralsDataTable;

class ReferralsController extends Controller
{
    public function __construct()
    {
        $this->base_url = $this->view_data['base_url'] = 'admin/referrals';
        $this->view_data['main_title'] = 'Referral';
        $this->view_data['user_type'] = $this->user_type = ucfirst(request()->segment(3));
        $this->view_data['sub_title'] = $this->user_type.' '. $this->view_data['main_title'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReferralsDataTable $dataTable)
    {
        return $dataTable->setUserType($this->user_type)->render('admin.referrals.view',$this->view_data);
    }

    /**
     * Display a referral detail
     *
     * @return \Illuminate\Http\Response
     */
    public function referral_details(Request $request)
    {
        $referral_details = ReferralUser::with('user','referral_user')->where('user_id',$request->id)->get();
        if($referral_details->count() == 0) {
            flashMessage('error','Invalid ID');
            return back();
        }
        $user_type = strtolower($referral_details[0]->user_type);
        return view('admin.referrals.details',compact('referral_details','user_type'));
    }
}