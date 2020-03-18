@extends('template_footeronly')
@section('main')
<div class="flash-container">
  @if(Session::has('message'))
  <div class="alert text-center participant-alert " style="    background: #1fbad6 !important;color: #fff !important;margin-bottom: 0;" role="alert">
    <a href="#" class="alert-close text-white" data-dismiss="alert">&times;</a>
    {!! Session::get('message') !!}
  </div>
  @endif
</div>
<div class=" text-center signupdrive" ng-controller="facebook_account_kit">
  @include('user.otp_popup')
  <div class="join-page" >
    <div class="layout layout--join layout--hero driver_banner_join" >
      <div class="layout__item layout--join__left-item secondary-font text-white hidden--portable float--left" style="padding:0px;" >
        <div class="bit bit--logo text--center" data-reactid="6" style="padding: 0px !important;">
          <a href="{{ url('/') }}">
            <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height: 50px;background-size: contain;">
          </a>
        </div>
        <h1 class="push--bottom" style="line-height: 1.14; font-weight: 200; text-align: left; margin-bottom: 48px; letter-spacing: -0.02em; font-size: 50px ! important;" >{{$site_name}} {{trans('messages.user.need_partner')}}
        </h1>
        <p style="width: 70%; font-weight: 400; text-align: left;" >{{trans('messages.user.drive_with_gofer')}} {{$site_name}} {{trans('messages.user.need_partner_content')}}
        </p>
      </div>
      <div class="layout__item layout--join__hero-item soft-gutter z-30" style="padding-top:0 !important;" >
        <div class="layout" style=" margin-left: -40px;"
          >
          <div class="layout__item driver-signup-form-container" >
            {{ Form::open(array('url' => 'driver_register','class' => 'layout layout--flush driver-signup-form-join-legacy','id'=>'form')) }}
            {{csrf_field()}}
            {!! Form::hidden('request_type', '', ['id' => 'request_type' ]) !!}
            {!! Form::hidden('otp', '', ['id' => 'otp' ]) !!}
            <input type="hidden" name="user_type" value="Driver">
            <div name="driver-signup-form-scroll" >
            </div>
            <div class="layout__item one-whole push-tiny--ends" style="margin-top:12px; ">
              <div class="layout__item one-whole">
              </div>
            </div>
            <div class="layout__item one-whole createacc" >
              <a class="btn btn--primary btn--full" href="{{ url('signin_driver')}}" style="background-color:#09091a !important;border:none;padding:12px;" >
                <span class="micro" style="line-height: 2.4; padding-left: 5px; font-size: 11px; text-transform: uppercase;">{{trans('messages.ride.already_have_account')}}</span>
              </a>
              <h6 style="margin: 12px 0px; font-weight: 500; font-size: 16px; letter-spacing: 0.005em;" >{{trans('messages.user.create_account')}}
              </h6>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 push-tiny--bottom--legacy pull-left" id="pad-sm-right" style="padding:0px;" >
              <div class="_style_3EKecM" >
                {!! Form::text('first_name', '', ['class' => '_style_3vhmZK','placeholder' => trans('messages.user.firstname'),'id' => 'fname' ]) !!}
              </div>
              <span class="text-danger first_name_error">{{ $errors->first('first_name') }}</span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 legacy-one-half push-tiny--bottom--legacy" id="pad-sm-right"  style="padding:0px;">
              <div class="_style_3EKecM" >
                {!! Form::text('last_name', '', ['class' => '_style_3vhmZK','placeholder' => trans('messages.user.lastname'),'id' => 'lname' ]) !!}
              </div>
              <span class="text-danger last_name_error">{{ $errors->first('last_name') }}</span>
              
            </div>
            <div class="layout__item one-whole push-tiny--bottom--legacy" >
              <div class="_style_3EKecM" >
                {!! Form::text('email', '', ['class' => '_style_3vhmZK','placeholder' => trans('messages.user.email') ]) !!}
              </div>
              <span class="text-danger email_error">{{ $errors->first('email') }}</span>
            </div>
            <div class="mobile-code">
              <div class="layout col-md-12 layout--flush float mobile-container left two-char" >
                
                <div id="select-title-stage">{{old('country_code')!=null ? '+'.old('country_code') : '+1' }}</div>
                <input type="hidden" name="country_code" value="{{ old('country_code',(isset($country_code) ? $country_code : '')) }}">
                <div class="select select--xl" ng-init="old_country_code={{old('country_code')!=null? old('country_code') : '1'}}">
                  <label for="mobile-country"><div class="flag US"></div></label>
                  <select name="country_code" tabindex="-1" id="mobile_country" class="square borderless--right">
                    @foreach($country as $key => $value)
                    <option value="{{$value->phone_code}}" {{ ($value->phone_code == (old('country_code')!=null? old('country_code') : '1')) ? 'selected' : ''  }} data-value="+{{ $value->phone_code}}">{{ $value->long_name}}
                    </option>
                    @endforeach
                  </select>
                  <span class="text-danger country_code_error">{{ $errors->first('country_code') }}</span>
                  
                </div>
              </div>
              <div class="layout__item one-whole push-tiny--bottom--legacy" >
                <div class="_style_3EKecM" ng-init="old_mobile_number='{{old('mobile_number')!=null?old('mobile_number'):''}}'">
                  {!! Form::tel('mobile_number', isset($phone_number)?$phone_number:'', ['class' => '_style_3vhmZK','placeholder' => trans('messages.profile.mobile'),'id' => 'mobile' ]) !!}
                </div>
                <!-- <span class="text-danger mobile-text-danger" style="display: none">Mobile Number is required</span>            -->
                <span class="text-danger mobile_number_error">{{ $errors->first('mobile_number') }}</span>
              </div>
            </div>
            <div class="layout__item one-whole push-tiny--bottom--legacy" >
              <div class="_style_3EKecM" >
                {!! Form::password('password', array('class' => '_style_3vhmZK','placeholder' => trans('messages.user.paswrd'),'id' => 'password') ) !!}
              </div>
              <span class="text-danger password_error">{{ $errors->first('password') }}</span>
              
            </div>
            <div class="layout__item one-whole push-tiny--bottom--legacy" >
              <div >
                <div style="position:relative;" >
                  <div style="background-color:#FFFFFF;border-color:#E5E5E4;border-style:solid;border-width:1px;box-sizing:border-box;height:auto;flex-wrap:wrap;margin-bottom:24px;transition:all 400ms ease;margin:0;border-radius:3px;-moz-box-sizing:border-box;-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;-webkit-transition:all 400ms ease;-webkit-box-lines:multiple;" class="_style_3jmRTe" >
                    <div class="autocomplete-input-container">
                      <div class="autocomplete-input">
                        {!! Form::text('home_address', '', ['class' => '_style_3vhmZK','placeholder' => trans('messages.profile.profile_city'),'id' => 'home_address','autocomplete' => 'false','style' => 'width:100%']) !!}
                      </div>
                      <ul class="autocomplete-results home_address">
                      </ul>
                    </div>
                    
                    
                    <input type="hidden" name="city" id='city' value="">
                    <input type="hidden" name="state" id="state" value="">
                    <input type="hidden" name="country" id="country" value="">
                    <input type="hidden" name="address_line1" id="address_line1" value="">
                    <input type="hidden" name="address_line2" id="address_line2" value="">
                    <input type="hidden" name="postal_code" id="postal_code">
                    <input type="hidden" name="latitude" id="latitude" value="">
                    <input type="hidden" name="longitude" id="longitude" value="">
                  </div>
                  <span class="text-danger home_address_error">{{ $errors->first('home_address') }}</span>
                  <div style="box-sizing:border-box;border:1px solid #E5E5E4;position:absolute;width:100%;background:#FFFFFF;z-index:1000;visibility:hidden;-moz-box-sizing:border-box;" >
                    <div style="max-height:300px;overflow:auto;" >
                      <div aria-live="assertive" >
                        <div style="font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:400;font-size:14px;line-height:24px;padding:8px 18px;border-bottom:1px solid #E5E5E4;" class="_style_1cBulK" >No results
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
            <div class="layout__item one-whole push-tiny--bottom--legacy" >
              <div class="_style_3EKecM" >
                {!! Form::text('referral_code','', array('class' => '_style_3vhmZK text-uppercase','placeholder' => trans('messages.referrals.referral_code'),'id' => 'referral_code') ) !!}
              </div>
              <span class="text-danger referral_code_error">{{ $errors->first('home_address') }}</span>
            </div>
            <div class="layout__item one-whole push-small--bottom" >
              <input type="hidden" name="step" value="basics">
              <button name="step" value="basics" class="btn--arrow btn--full error-retry-btn _style_3CjDXv" id="submit-btn" ng-click="showPopup('send_otp');" type="button"  style="box-sizing:border-box;text-decoration:none;color:#FFFFFF;display:inline-block;vertical-align:middle;text-align:center;margin:0;cursor:pointer;overflow:visible;background-color:#11939A;font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:600;font-size:14px;padding:11px 20px;border-radius:0px;border:2px solid #11939A;text-transform:uppercase;outline:none;line-height:18px;position:relative;transition:all 400ms ease;-moz-box-sizing:border-box;-webkit-transition:all 400ms ease;" >{{trans('messages.user.submit')}}
              </button>
            </div>
            <div class="layout__item one-whole">
              <p class="legal flush">{{trans('messages.user.proceed')}} {{$site_name}} {{trans('messages.user.contact')}}</p>
            </div>
            <span >
            </span>
            <input type="hidden" name="code" id="code" />
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <div class="three-section-join" >
      <div class="portable-space clearfix" >
        <div class="top-section-sub clearfix col-md-12" style="margin-top:10px;">
          <div class="col-md-4 col-xs-12 text-left" >
            <div class="top-section-sub-cont" >
              <div class="" style="height:100px;width:100px;" >
                <img src="images/icon/money_good.jpg" style="vertical-align:middle;height:100px;width:100px;" >
              </div>
              <div class="sub-top" >
                <h4 >{{trans('messages.user.money_make')}}
                </h4>
                <div class="" >
                  <p >{{trans('messages.user.money_make_content',['site_name' => $site_name])}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12 text-left" >
            <div class="top-section-sub-cont" >
              <div class="" style="height:100px;width:100px;" >
                <img src="images/icon/drive_when.jpg" style="vertical-align:middle;height:100px;width:100px;" >
              </div>
              <div class="sub-top" >
                <h4 >{{trans('messages.user.drive_when_want')}}
                </h4>
                <div class="" >
                  <p>{{trans('messages.user.drive_when_want_content')}} {{$site_name}}, {{trans('messages.user.imp_moments')}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-xs-12 text-left" >
            <div class="top-section-sub-cont" >
              <div class="" style="height:100px;width:100px;">
                <img src="images/icon/no_office.jpg" style="vertical-align:middle;height:100px;width:100px;" >
              </div>
              <div class="sub-top" >
                <h4 >{{trans('messages.user.no_office')}}
                </h4>
                <div class="" >
                  <p >{{trans('messages.user.no_office_content')}} {{$site_name}} {{trans('messages.user.freedom')}}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-uber-white-footer" >
      <hr class="horizon">
      <div class="col-md-12" >
        <div class="layout" >
          <div class="copyrightsection col-md-6 text-left hide" >
            <p >&copy; 2017 Gofer Technologies Inc.
            </p>
          </div>
          <div class="col-md-3 col-sm-6 col-xs-2 privacy text-left hide">
            <a href="#" class="" >
              <!-- react-text: 110 -->Privacy
              <!-- /react-text -->
            </a>
          </div>
          <div class="col-md-3 col-sm-6 col-xs-6 termss text-left hide" >
            <a href="#" class="" >
              <!-- react-text: 113 -->Terms
              <!-- /react-text -->
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<style>
.logo-link
{
display: none;
}
.funnel
{
height: 0px !important;
}

</style>
@stop