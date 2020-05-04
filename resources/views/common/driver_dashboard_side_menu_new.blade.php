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
                                <img src="{{ url('images/user.jpeg')}}" class="randomPicLarge img--full fixed-ratio__content">

                                @else
                                <img src="{{ @Auth::user()->profile_picture->src }}"  class="randomPicLarge img--full fixed-ratio__content profile_picture">
                                @endif
                            </a>
                            </div>
                        </div>
                     

             
                    <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2em">
                        <span style="font-size: 170%; opacity: 0.9; font-family: 'MontserratReg';font-weight: bold">{{ @Auth::user()->first_name}} {{ @Auth::user()->last_name}}</span>
                         <span style="font-size: 95%; opacity: 0.8; font-weight: bold; font-family: 'MontserratReg';">Community Leader</span>
                        </div>
                 
                    <li style="padding-top: 0.9em; padding-bottom: 0.9em; border-bottom: 1px solid rgba(0, 0, 0, 0.10); border-top: 1px solid rgba(0, 0, 0, 0.10)" class="{{ (Route::current()->uri() == 'driver/passengers') ? 'active' : '' }}">
                        <a href="{{ url('driver/passengers') }}">Training<i class="fa fa-angle-right pull-right"></i></a>
                    </li>
                    <li style="padding-top: 0.9em; padding-bottom: 0.9em; border-bottom: 1px solid rgba(0, 0, 0, 0.10)" class="driver-dashboard-treeview {{ (Route::current()->uri() == 'driver/edit_profile' || Route::current()->uri() == 'driver/vehicle_view'  || Route::current()->uri() == 'driver/documents' || Route::current()->uri() == 'driver/membership' || Route::current()->uri() == 'driver/bank_details' || Route::current()->uri() == 'driver/referral') ? 'active' : ''  }}">
                        <a href="#">
                            <span>{{trans('messages.header.account.root')}}</span><i class="fa fa-angle-right pull-right"></i>
                        </a>

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

                    <li style="padding-top: 0.9em; padding-bottom: 0.9em; border-bottom: 1px solid rgba(0, 0, 0, 0.10)"  class="{{ (Route::current()->uri() == 'driver/help') ? 'active' : ''  }}">
                        <a href="{{ url('driver/help') }}">{{trans('messages.header.help')}}<i class="fa fa-angle-right pull-right"></i></a>


                    @endif
                     <li>
                        <a href="{{ route('driver_payout_preference') }}" aria-selected="{{ (Route::current()->uri() == 'payout_preferences') ? 'true' : 'false' }}" class="sidenav-item">Subscription</a>
                    </li>
                    <li>
                        <a href="{{ url('sign_out')}}">{{trans('messages.header.logout')}}</a>
                    </li>
                    


                </ul>
            </div>