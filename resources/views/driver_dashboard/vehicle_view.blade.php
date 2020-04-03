<title>Vehicle View</title>
@extends('template_driver_dashboard') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;" ng-controller="facebook_account_kit">
  <div class="page-lead separated--bottom  text--center text--uppercase">
    <h1 class="flush-h1 flush">{{trans('messages.header.account.vehicle_view')}}</h1>
  </div>
  <div style="display: flex; flex-wrap: wrap;">
    <div>
      @if ($car_active_image != "")
        <img src="{{$car_active_image}}" width="250px"/>
      @endif
    </div>
    <div style="align-self: center; padding-left: 50px; text-align:center">
      <div><h2>{{$vehicle_name}}</h2></div>
      <div>{{$vehicle_number}}</div>
      <div>{{$car_type}}</div>
    </div>
  </div>

</div>
</div>
</div>
</div>
</div>
</main>
@stop