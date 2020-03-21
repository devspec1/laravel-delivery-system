<?php

/**
 * Request Helper
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Request
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */
namespace App\Http\Helper;

use App\Http\Helper\RequestHelper;

class OtpHelper {

	public function __construct() {
		$this->request_helper = new RequestHelper();
	}

	public function sendOtp($mobile_number,$country_code){
		$otp = rand(1000,9999);
        $text = 'Your OTP number is '.$otp;
        $to = '+'.$country_code.$mobile_number;
        $message_responce=$this->request_helper->send_message($to,$text);
        if ($message_responce['status_code']==1) {
        	session([
				'signup_mobile' => $mobile_number,
				'signup_country_code' => $country_code,
				'signup_otp' => $otp,
			]);
        }
		return $message_responce;
    }
    
    public function sendPhoneSMS($mobile_number,$text){
		
        $message_responce=$this->request_helper->send_message($mobile_number,$text);
       
		return $message_responce;
	}

	public function resendOtp(){
		$otp = rand(1000,9999);
        $text = 'Your OTP number is '.$otp;
        $to = '+'.session('signup_country_code').session('signup_mobile');
        $twillio_responce=$this->request_helper->send_message($to,$text);

        if ($twillio_responce['status_code']==1) {
            session(['signup_otp' => $otp]);
            $twillio_responce['message'] = trans('messages.signup.otp_resended');
        }

		return $twillio_responce;
	}

	public function checkOtp($otp,$mobile_number = null){
		$data = ['status_code' => 0,'message'=>trans('messages.signup.wrong_otp')];
		if ($otp == session('signup_otp') && ($mobile_number==null || $mobile_number==session('signup_mobile'))) {
			$data = ['status_code' => 1,'message'=>'success'];
		}
		return $data;
	}
}