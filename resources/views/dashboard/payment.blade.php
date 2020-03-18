<title>Payment</title>
@extends('template_dashboard')

@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;">
<div class="page-lead separated--bottom  text--center text--uppercase">
<h1 class="flush-h1 flush">{{trans('messages.header.payment')}}</h1></div>

<div id="account-providers" class=" account-providers" style="padding:15px;"><h2 class="flush-h2">{{trans('messages.dashboard.payment_methods')}}</h2>
<div class="grid"><div class="grid__item palm-one-whole">
<div class="block-list block-list--bordered account-providers--accounts">
<div class="block-list__item account-provider-pandora" style="padding: 20px 15px;">
<div class="grid account-provider">
<span class="icon sprite_payment-type-default_icon"></span><span class="soft--sides">{{trans('messages.dashboard.personal_cash')}}••••</span>
</div>
</div>
<div class="block-list__item account-provider-spotify" style="padding: 20px 15px;">
<div class="grid account-provider">
<span class="icon sprite_payment-type-default_icon"></span><span class="soft--sides">{{trans('messages.dashboard.personal_paytm')}}••••</span>
</div>
</div>

</div>
</div>
</div>
</div>

<div id="payment__promotions" ><div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 palm-one-whole"><h2 class="flush-h2">{{trans('messages.dashboard.promotions')}}</h2></div><div class="col-lg-4 col-md-4 col-sm-4 col-xs-12  palm-one-whole"><form action="/promotions" method="POST" data-replace="data-replace" data-replace-pushstate="false" data-button-loader="#payment-promo-loader" data-button-loader-parent="#payment-promo-button"><input type="hidden" name="x-csrf-token" value="eBYrQqLCtPkRHpYKWiJe9ZqMCag6UtCe"><div class="inline-group input-group"><div class="inline-group__item"><input style="    padding: 3px !important;height: auto;margin: 0px;" type="text" name="code" value="" required="required" placeholder="Promo Code" class="text-input form-field--full"></div><div class="inline-group__item"><button id="payment-promo-button" class="btn"><span class="btn-loader collapse" id="payment-promo-loader"><span class="icon icon_spinner"></span></span><span class="soft-half--sides">{{trans('messages.dashboard.apply')}}</span></button></div></div></form></div><div class="grid__item soft--ends"><div class="section--light separated--ends soft-half text--uppercase">{{trans('messages.dashboard.no_active')}}</div></div></div>
<div id="email-subscriptions" class=" email-subscriptions"><h2 class="flush-h2">{{trans('messages.dashboard.gratuity')}}</h2><p class="soft-double--bottom">{{trans('messages.dashboard.preferred')}}{{$site_name}}.{{trans('messages.dashboard.taxi')}} {{$site_name}}X){{trans('messages.dashboard.paid')}}</p>
<div class="form-group__label weight--semibold col-lg-3 col-md-3 col-sm-3 col-xs-12" style="padding:15px;">{{trans('messages.dashboard.taxi_gratuity')}}</div>
<div class="form-group__field col-lg-6 col-md-6 col-sm-6 col-xs-12" style="    padding: 10px 0px 25px;"><div class="flexbox"><div class="flexbox__item soft--right col-lg-6 col-md-6 col-sm-6 col-xs-6">

<select class="payment-select form-field--full" tabindex="-1" title=""><option value="0">0%</option><option value="0.1">10%</option><option value="0.15">15%</option><option value="0.2" selected="selected">20%</option><option value="0.25">25%</option><option value="0.3">30%</option></select></div><div class="flexbox__item col-lg-4 col-md-4 col-sm-4 col-xs-5"><button id="payment-gratuity-button" class="btn"><span class="btn-loader collapse" id="payment-gratuity-loader"><span class="icon icon_spinner"></span></span><span>{{trans('messages.dashboard.save')}}</span></button></div></div></div>
</div>
</div>

</div>
</div>
</div>
</div>
</main>
@stop