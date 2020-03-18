@extends('template_nofooter')

@section('main')
<div id="app-wrapper" class="signup-riders" ng-controller="user">
  <header class="funnel" style="background:url('images/header3.png') center center repeat;" >
      <div class="bit--logo text--center" >
         <div class="bit bit__content nt_bit" >
           <a href="{{ url('/') }}">
            <img class="white_logo" src="{{ $logo_url }}" style="width: 109px;height: 50px;object-fit: contain;">
          </a>
         </div>
      </div>
  </header>  
  <section class="content-signupdrive ">

<div class="signup-wrapper">
  <div class="stage">
    <section class="signup-top">
      <div class="signup-top-text ">
        <img src="{{ url('images/ride1.jpg')}}" class="car-cls">
      </div>
    </section>
      <div class="form-wrapper">
      {{ Form::open(array('url' => 'driver_register','class' => 'layout layout--flush driver-signup-form-join-legacy')) }}
      <input type="hidden" name="user_type" value="Driver">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">      
        <div class="layout layout--flush col-md-12 container-field clearfix push-small--bottom">
        <h4 class="flush primary-font text-uber-black-80 ne_h2">{{trans('messages.user.vehicle_info')}}</h4>
          
        </div>
        <div class="input-group icon-input right nt_tya">
          <div class="layout layout--flush col-md-12 container-field clearfix push-small--bottom">
          <div class="field">
            <label class="field__label" for="input-email">{{trans('messages.user.vehicle_name')}}</label>
            
            {!! Form::text('vehicle_name', '', ['class' => 'field__input one-column-form__input--text','placeholder' => trans('messages.user.vehicle_name'),'id' => 'input-email','style' => 'margin:0px !important' ]) !!}                  
          </div>
            <span class="text-danger">{{ $errors->first('vehicle_name') }}</span>
          </div>
        </div>
        <div class="input-group icon-input right nt_tya">
          <div class="layout layout--flush col-md-12 container-field clearfix push-small--bottom">
          <div class="field">
            <label class="field__label" for="input-email">{{trans('messages.user.vehicle_number')}}</label>
            
            {!! Form::text('vehicle_number', '', ['class' => 'field__input one-column-form__input--text','placeholder' => trans('messages.user.vehicle_number'),'id' => 'input-email','style' => 'margin:0px !important' ]) !!}                  
          </div>
            <span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
          </div>
        </div>
        <div class="input-group icon-input right nt_tya">
          <div class="layout layout--flush col-md-12 container-field clearfix push-small--bottom">
          <div class="field">
            <label class="field__label" for="input-email">{{trans('messages.user.vehicle_type')}}</label>
          
            <select name="vehicle_type" id="vehicle_type" class="field__input one-column-form__input--text">
              <option value="" selected="">{{trans('messages.user.vehicle_type')}}</option>
                    @foreach($car_type as $key => $value)
                      <option value="{{$value->id}}" >{{ $value->car_name}}
                      </option>
                    @endforeach
            </select>    
          </div>
            <span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
          </div>
        </div>


        <button id="submit-btn" type="submit" name="step" value="car_details" class="blue-signin-btn btn btn--large btn--full ae-button ae-form-field">
          <span class="text-center">{{trans('messages.user.continue')}}</span>
              <i class="fa fa-long-arrow-right icon icon_right-arrow-thin pull-right"></i>
        </button>
        {{ Form::close() }}
      </div>
  </div>
</section>
</div>

</main>
@stop
