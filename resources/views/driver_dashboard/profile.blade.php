<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div style="height: 100%; width: 100%; display: flex" id="profileWrp" class="mainWrp1">

  <div style="display: flex; width: 100%; height: 100%; padding-top: 1.5em" id="profileRightWrp">
      <div style="margin-top: 1em; display: flex; flex-direction: column;padding: 15px 10px">
      
      </div>

      <div style=" width: 100%; flex-direction: column; padding-left: 1.5em" data-tab="profile">
          
            {{ Form::open(array('url' => 'driver_update_profile/'.$result->id,'id'=>'form','class' => 'layout layout--flush','files' => 'true','enctype'=>'multipart/form-data','name'=>'driver_profile')) }}
          
          @include('dashboard.mobile_number_change')
          <input type="hidden" name="user_type" value="{{ $result->user_type }}">
          <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="code" id="code" />
          <input type="hidden" id="user_id" name="user_id" value="{{ $result->id }}">
          <input type="hidden" name="id" value="{{ @$result->id}}">
          <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12 profile_update-loader" style="display: flex; justify-content: space-between; align-items: center; border-bottom:0px !important; margin-bottom: 1.25em; padding-left: 0">
            <div style="display :flex">
              <span style="font-size: 130%; color: #1B187F; font-weight: bold; font-family:'MontserratReg'">Profile Details</span>
             <div  style="display: flex; align-items: center;justify-content: space-between; margin-left: 2em">
              
            @if(@Auth::user()->status == 'Active')
              <div class="">
                <span style="background: #5cb85c; color: #fff; border: 1px solid #5cb85c; border-radius: 9px; padding: 0.4em; padding-left: 1.7em; padding-right: 1.7em;font-size:90%" class="label-success"> {{ @Auth::user()->trans_status}} </span>
              </div>
            @else
              <div class="">
                <span style="background-color: #F1F1F1;border: solid 1px #C6C6C6;color: #939393;" class="label label-success"> {{ @Auth::user()->trans_status}} </span>
              </div>
            @endif
           </div>
         </div>
          
          </div>
         
     
      <div style="display: flex; width: 100%">
  
           <div style="display: flex; flex-direction: column;">
            <label style="padding:2px 0px;"> 
              Update Photo
            </label>
          <div style="display: flex; align-items: center; flex-direction: column;">
            @if(@Auth::user()->profile_picture->src == '')
                                <img src="{{ url('images/user.jpeg')}}" class="profileWrpPic1 profile_picture">

                                @else
                                <img src="{{ @Auth::user()->profile_picture->src }}"   class="profileWrpPic1 profile_picture">
                                @endif

                            
              <div style="display: flex; flex-direction: column; align-items: center">
            
              <button type="button" class="btn  doc-button" style="background: #6e7175;margin-top: 0.8em;border-radius: 7px; color: white" ng-click="selectFile()">
                <span style="padding: 0px 15px !important;font-size: 11px !important;" id="span-cls">Browse
                </span>
              </button>

              <input type="file" ng-model="profile_image" style="display:none" accept="image/*"
              id="file" name='profile_image' onchange="angular.element(this).scope().fileNameChanged(this)" />
              </div>
            </div>
              
      


        </div>

        <div style="display: flex; flex-direction: column; padding-left: 2em; border-left: 1px solid rgba(0, 0, 0, 0.15); margin-left: 2em; width: 100%">
        {{-- <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="border-bottom:0px !important;">
          <a href="{{ url('documents/'.@Auth::user()->id) }}" style="    padding: 0px 30px !important;
          font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue">{{trans('messages.driver_dashboard.manage_documents')}}</a>
        </div> --}}
        <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" >
          <div style="margin-bottom: 0.6em; display: flex; flex-direction: column">
            <label style="padding:2px 0px;">
              {{trans('messages.profile.email')}} <em class="text-danger">*</em>
            </label>
            <div  style="padding:2px 0px;">
              <input class="_style_3vhmZK" name="email" value="{{ @$result->email}}" placeholder="{{trans('messages.profile.email')}}">
              <span class="text-danger"> {{ $errors->first('email') }} </span>
            </div>
          </div>
        </div>
        <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" >
          <div style="margin-bottom: 0.6em; display: flex; flex-direction: column">
            <label class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:2px 0px;">{{trans('messages.profile.phone')}}<em class="text-danger">*</em></label>
            <div style="display: flex; align-items: center">
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2" style="padding:2px 0px;margin: 0px 2px">
              <input class="_style_3vhmZK" type="text" name="phone_code" value="+{{ @$result->country_code}}" readonly="">
              <input type="hidden" id="mobile_country" name="mobile_country" value="{{ @$result->country_code}}">
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-9" style="padding:2px 0px;">
              <input class="_style_3vhmZK" id="mobile" name="mobile_number" value="{{ @$result->mobile_number}}" placeholder="{{trans('messages.profile.mobile')}}" readonly="">
              <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin-left: 5px;padding:2px 0px;">
              <input type="button"  style="background: transparent; border: none; font-family: 'MontserratReg" name="change_number" value="{{ trans('messages.profile.change') }}" id="submit-btn" ng-click="changeNumberPopup('show_popup')">
            </div>
              </div>
          </div>
        </div>
           
       
        <div id="addrInfo" style="display: flex;  align-items: center" class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" >
          

           <div style="margin-bottom: 0.6em; display: flex; width: 100%; flex-direction: column">
            <label style="padding:2px 0px;">{{trans('messages.profile.addr')}}</label>
            <div  style="padding:2px 0px;">
             

            
                   {!! Form::text('address_line1', @$result->driver_address->address_line1.@$result->driver_address->address_line2, ['class' => '_style_3vhmZK','placeholder' => trans('messages.profile.addr'),'id' => 'home_address','autocomplete' => 'false']) !!}  
           
        
                            
            </div>
            <span class="text-danger">{{ $errors->first('address_line1') }}</span>
          </div>
          <div style="margin-bottom: 0.6em; display: flex; flex-direction: column">
            <label style="padding:2px 0px;"> 
              {{trans('messages.profile.profile_city')}}
            </label>
            <div  style="padding:2px 0px;">
              {!! Form::text('city', @$result->driver_address->city, ['class' => '_style_3vhmZK','placeholder' => trans('messages.profile.profile_city'),'id' => 'city']) !!}
              <input type="hidden" name="state" id="state" value="{{ @$result->driver_address->state }}">
              <input type="hidden" name="country" id="country" value="">
            
              <input type="hidden" name="postal_code" id="postal_code" value="{{ @$result->driver_address->postal_code }}">
              <input type="hidden" name="latitude" id="latitude" value="">
              <input type="hidden" name="longitude" id="longitude" value="">
            </div>
            <span class="text-danger">{{ $errors->first('city') }}</span>
         
        </div>
          <div style="margin-bottom: 0.6em; display: flex; flex-direction: column;margin-left:1em; margin-right: 1em; width: 17%">
            <label style="padding:2px 0px;"> {{trans('messages.profile.profile_postal_code')}}</label>
            <div  style="padding:2px 0px;">
              <input class="_style_3vhmZK" name="postal_code" value="{{ @$result->driver_address->postal_code}}" placeholder="{{trans('messages.profile.profile_postal_code')}}">
            </div>
            <span class="text-danger">{{ $errors->first('postal_code') }}</span>
          </div>

          <div style="margin-bottom: 0.6em; display: flex; flex-direction: column">
            <label style="padding:2px 0px;"> {{trans('messages.profile.country')}} </label>
            <select class="_style_3vhmZK col-lg-4 col-md-4 col-sm-4 col-xs-12" name="country_code" tabindex="-1" title="" disabled="">
              @foreach($country as $key => $value)
              <option value="{{$value->phone_code}}" {{$value->phone_code == @$result->country_code ? 'selected' : ''}} data-value="+{{ $value->phone_code}}"> {{ $value->long_name}} </option>
              @endforeach
            </select>
          </div>
          <span class="text-danger">{{ $errors->first('country_code') }}</span>

        </div>
       

        <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: flex; border-bottom:0px !important;">
          <button style="    padding: 0px 40px !important;
          font-size: 12px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.user.update')}}</button>

         
        </div>
      
    </div>

        {{ Form::close() }}
     
      </div>
      <div style="display: flex; width: 100%; justify-content: space-between;margin-top: 2em;">

      <div style="display: flex; flex-direction: column; width: 50%">
       <span style="font-size: 130%; color: #1B187F; font-weight: bold;margin-bottom: 0.7em; font-family:'MontserratReg'">Password</span>
         <label style="padding:2px 0px;">Current password</label>
            <div  style="padding:2px 0px;">
              <input class="_style_3vhmZK" type="password" name="postal_code" value="{{ @$result->driver_address->postal_code}}" placeholder="{{trans('messages.profile.profile_postal_code')}}">
            </div>
            <div style="display: flex">
              <div style="display: flex; flex-direction: column;margin-right: 1.2em; width: 100%">
                     <label style="padding:2px 0px;">New password</label>
                <div  style="padding:2px 0px;">
                  <input class="_style_3vhmZK" type="password" name="postal_code" value="{{ @$result->driver_address->postal_code}}" placeholder="{{trans('messages.profile.profile_postal_code')}}">
                </div>
              </div>
              <div style="display: flex; flex-direction: column;width:100%; ">
                     <label style="padding:2px 0px;"> Confirm new password</label>
                <div  style="padding:2px 0px;">
                  <input class="_style_3vhmZK" type="password" name="postal_code" value="{{ @$result->driver_address->postal_code}}" placeholder="{{trans('messages.profile.profile_postal_code')}}">
                </div>
              </div>
            </div>
          <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: flex; border-bottom:0px !important;">
         <button type="button" class="btn  doc-button" style="background: #6e7175;margin-top: 0.8em;border-radius: 7px; color: white" ng-click="selectFile()">
                <span style="padding: 0px 30px !important;font-size: 11px !important;" id="span-cls">Update
                </span>
              </button>
         
        </div>
      </div>
       <div style=" display: flex; flex-direction: column;padding: 15px 10px;margin-right: 2.5em">
            <span style="font-size: 130%; color: #1B187F;font-weight: bold; margin-bottom: 0.4em; font-family:'MontserratReg'">Membership</span>
              <div style="display: flex; flex-direction: row; align-items: center ">
                <span style="font-size: 120%; font-weight: bold">Executive</span>
              <span style="font-size: 90%; margin-left: 1.5em; color: #8f2a06; font-weight: bold">Cancel</span>
            </div>
           </div>
          </div>
      
  </div>
</div>
</div>
</div>
</div>
</div>
</main>
@stop


<script>
  $(function() {
    $("#profileLeftWrp span").click(function() {
       $("#profileRightWrp > div.current").removeClass("current");
       $("#profileRightWrp > div[data-tab='" + $(this).data("tab") + "']").addClass("current");
      $("#profileLeftWrp span.current").removeClass("current");
      $(this).addClass("current");

    })
  });
</script>