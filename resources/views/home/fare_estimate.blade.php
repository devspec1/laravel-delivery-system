@extends('template')

@section('main')
	<div class="container-fluid" id="pad-sm-zero" style="padding:0px !important;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="pad-sm-zero" style="padding:0px 0px;">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px 0px;" id="pad-sm-zero">
<div id="map_wrapper" style="width:100% !important;    height: 100% !important;">
    <div id="map_canvas" class="mapping"></div>
</div>
	</div>
	<div class="pos-abs-map">
	<div class=" col-lg-4 col-md-5 col-sm-6 col-xs-12 lap-one-half hard--left soft--right" data-reactid="284">
	<div class="position--relative inline-estimate__route-entry" data-reactid="285">
	<div class="bg-primary-layer-color pointer-events--all locate-content" style="padding:30px; display: block;   margin-bottom: 20px;">
	<h2 class="_style_3zaJwR" style="    margin: 0px 0px 10px 0px !important;
">{{$site_name}} {{trans('messages.home.fare')}}</h2>
	<p class="cmln__paragraph">{{trans('messages.home.how_much')}}{{$site_name}}{{trans('messages.home.cost')}} </p>
	</div>
	<div class="bg-primary-layer-color pointer-events--all locate-pin" style="padding:15px;">
	<div class="position--relative" data-reactid="290">
	<div class="fare-estimate__input-connector z-10" data-reactid="291"></div>
	<div class="fare-estimate__pickup push-tiny--bottom" data-reactid="292">
	<div class="autocomplete-container" data-reactid="293">
	<div class="autocomplete position--relative" data-reactid="294">
	<div class="autocomplete__input hard flush--bottom autocomplete__input--icon" data-reactid="295">
	<div style="height:100% !important;" data-reactid="296">
	<input style="height:100% !important;" value="" placeholder="Enter pickup location" autocomplete="off" aria-label="Enter a pickup location: address, city and state required" data-reactid="297">
	</div></div></div></div>
	<i class="icon icon_location fare-estimate__location-icon position--absolute" tabindex="0" role="button" aria-label="Locate me" data-reactid="298"></i></div>
	<div class="fare-estimate__destination-row" data-reactid="299">
	<div class="fare-estimate__destination" data-reactid="300">
	<div class="autocomplete-container" data-reactid="301">
	<div class="autocomplete position--relative" data-reactid="302">
	<div class="autocomplete__input hard flush--bottom autocomplete__input--icon" data-reactid="303"><div style="height:100% !important;" data-reactid="304">
	<input style="height:100% !important;" value="" placeholder="Enter destination" autocomplete="off" aria-label="Enter a destination: address, city and state required" data-reactid="305"></div></div></div></div></div><button tabindex="-1" class="btn btn--bit position--absolute right transition fare-estimate__button--inactive pointer-events--none" aria-label="Request estimate" data-reactid="306"><i class="icon_right-arrow-thin icon" data-reactid="307"></i></button></div></div></div>
<a class="btn btn--primary btn--arrow position--relative error-retry-btn get-started">
<div class="block-context soft-small--right" style="    width: 180px;">{{trans('messages.home.get_started')}} Get Started</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
	</div></div>
</div>
	</div>
	</div>
</main>
@stop
<style type="text/css">
	body{
		position: fixed;
		height: 100% !important;
	}
</style>