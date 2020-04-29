<div class="container mar-zero" style="padding:0px;">
    <div class="col-lg-10 col-md-10 col-sm-13 col-xs-12  height--full dash-panel">
        <div class="height--full pull-left separated--sides full-width">
            <div style="padding:0px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-12 flexbox__item one-fifth page-sidebar hidden--portable hide-sm-760">
                <ul class="site-nav">
                    <li class="soft--ends">
                        <div class="center-block three-quarters push-half--bottom">
                            <div class="img--circle img--bordered img--shadow fixed-ratio fixed-ratio--1-1">
                                @if(@Auth::user()->profile_picture->src == '')
                                <img src="{{ url('images/user.jpeg')}}" class="img--full fixed-ratio__content">
                                @else
                                <img src="{{ @Auth::user()->profile_picture->src }}"  class="img--full fixed-ratio__content profile_picture">
                                @endif
                            </div>
                        </div>
                        <div class="text--center">
                            <div style="    font-size: 16px;
                            font-weight: 200;">{{ @Auth::user()->first_name}} {{ @Auth::user()->last_name}}</div>
                            <div class="soft-half--top">
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="{{ url('driver_profile') }}" aria-selected="{{ (Route::current()->uri() == 'driver_profile') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.header.profil')}}</a>
                    </li>
                    <li>
                        <a href="{{ url('driver_payment') }}" aria-selected="{{ (Route::current()->uri() == 'driver_payment') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.header.payment')}}</a>
                    </li>
                    <li>
                        <a href="{{ url('driver_invoice') }}" aria-selected="{{ (Route::current()->uri() == 'driver_invoice') ? 'true' : 'false' }}"  class="side-nav-a" >{{trans('messages.header.invoice')}}</a>
                    </li>
                    <li>
                        <a href="{{ url('driver_trip') }}" aria-selected="{{ (Route::current()->uri() == 'driver_trip') ? 'true' : 'false' }}"  class="side-nav-a">{{trans('messages.header.mytrips')}}</a>
                    </li>
                    <li>
                        <a href="{{ route('driver_payout_preference') }}" aria-selected="{{ (Route::current()->uri() == 'payout_preferences') ? 'true' : 'false' }}" class="sidenav-item">{{trans('messages.account.payout')}}</a>
                    </li>
                    @if(Auth::user()->company_id == '1')
                    <li>
                        <a href="{{ route('driver_referral') }}" aria-selected="{{ (Route::current()->uri() == 'driver_referral') ? 'true' : 'false' }}" class="side-nav-a">
                            {{trans('messages.referrals.referral')}}
                        </a>
                    </li>
                    @endif
                     <li>
                        <a href="{{ route('driver_payout_preference') }}" aria-selected="{{ (Route::current()->uri() == 'payout_preferences') ? 'true' : 'false' }}" class="sidenav-item">Subscription</a>
                    </li>
                    <li>
                        <a href="{{ url('sign_out')}}">{{trans('messages.header.logout')}}</a>
                    </li>
                    


                </ul>
            </div>