<div class="container mar-zero" style="padding:0px;">
    <div class="col-lg-10 col-md-10 col-sm-13 col-xs-12  height--full dash-panel">
        <div class="height--full pull-left separated--sides full-width">
            <div style="padding:0px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-12 flexbox__item one-fifth page-sidebar hidden--portable hide-sm-760">
                <ul class="site-nav">
                    <li class="soft--ends">
                        <div class="center-block three-quarters push-half--bottom">
                            <div class="img--circle img--bordered img--shadow fixed-ratio fixed-ratio--1-1">
                                 <a href="{{ url('driver_profile') }}">
                                    @if(@Auth::user()->profile_picture->src == '')
                                <img src="{{ url('images/user.jpeg')}}" class="img--full fixed-ratio__content">
                                @else
                                <img src="{{ @Auth::user()->profile_picture->src }}"  class="img--full fixed-ratio__content profile_picture">
                                @endif
                                </a>
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
                        <a href="{{ url('driver_payment') }}" aria-selected="{{ (Route::current()->uri() == 'driver_profile') ? 'true' : 'false' }}" class="{{ (Route::current()->uri() == 'driver_payment') ? 'current' : '' }}" class="side-nav-a">Payment</a>
                    </li>

                     <li>
                        <a href="{{ url('driver_invoice') }}" aria-selected="{{ (Route::current()->uri() == 'driver_payment') ? 'true' : 'false' }}" class="side-nav-a">Invoice</a>
                    </li>
                    <li>
                        <a href="{{ url('driver_trip') }}" aria-selected="{{ (Route::current()->uri() == 'driver_payment') ? 'true' : 'false' }}" class="{{ (Route::current()->uri() == 'driver_payment') ? 'current' : '' }} side-nav-a">My Trips</a>
                    </li>
                    <li>
                        <a href="{{ url('payout_preferences') }}" aria-selected="{{ (Route::current()->uri() == 'driver_payment') ? 'true' : 'false' }}" class="side-nav-a">Payout</a>
                    </li>
                     <li>
                        <a href="{{ url('driver_referral') }}" aria-selected="{{ (Route::current()->uri() == 'driver_trip') ? 'true' : 'false' }}"  class="{{ (Route::current()->uri() == 'driver_payment') ? 'current' : '' }} side-nav-a">Referral</a>
                    </li>
                    <li>
                        <a href="{{ url('subscription') }}" aria-selected="true" class="{{ (Route::current()->uri() == 'subscription') ? 'current' : '' }}">Subscription</a>
                    </li>
                     <li>
                        <a href="{{ url('driver_help') }}" aria-selected="{{ (Route::current()->uri() == 'driver_trip') ? 'true' : 'false' }}"  class="side-nav-a">Help</a>
                    </li>
                     <li>
                        <a href="{{ url('logout') }}" aria-selected="{{ (Route::current()->uri() == 'driver_trip') ? 'true' : 'false' }}"  class="side-nav-a">Logout</a>
                    </li>
                    


                </ul>
            </div>