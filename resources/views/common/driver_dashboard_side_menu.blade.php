<div class="container mar-zero" style="padding:0px;">
    <div class="col-lg-10 col-md-10 col-sm-13 col-xs-12  height--full dash-panel">
        <div class="height--full pull-left separated--sides full-width">
            <div style="padding:0px; background-color:black; height:100vh" class="col-lg-3 col-md-3 col-sm-3 col-xs-12 flexbox__item one-fifth page-sidebar hidden--portable hide-sm-760">
                <ul class="driver-nav">
                    <div class="soft--ends">
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
                    </div>
                    <li class="{{ (Route::current()->uri() == 'driver/inbox') ? 'active' : '' }}">
                        <a href="{{ url('driver/inbox') }}" >
                            {{trans('messages.header.inbox')}}
                        </a>
                    </li>
                    <li class="driver-dashboard-treeview {{ (Route::current()->uri() == 'driver/trips_payments' || Route::current()->uri() == 'driver/pay_statements') ? 'active' : ''  }}">
                        <a href="#">
                            <span>{{trans('messages.header.earnings.root')}}</span><i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="driver-dashboard-treeview-menu">
                            <li class="{{ (Route::current()->uri() == 'driver/trips_payments') ? 'active' : ''  }}">
                                <a href="{{ url('driver/trips_payments') }}">
                                    <span>{{trans('messages.header.earnings.trips_payments')}}</span>
                                </a>
                            </li>
                            
                            <li class="{{ (Route::current()->uri() == 'driver/pay_statements') ? 'active' : ''  }}">
                                <a href="{{ url('driver/pay_statements') }}">
                                    <span>{{trans('messages.header.earnings.pay_statements')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ (Route::current()->uri() == 'driver/driverteam') ? 'active' : '' }}">
                        <a href="{{ url('driver/driverteam') }}">{{trans('messages.header.driverteam')}}</a>
                    </li>
                    <li class="{{ (Route::current()->uri() == 'driver/passengers') ? 'active' : '' }}">
                        <a href="{{ url('driver/passengers') }}">{{trans('messages.header.passengers')}}</a>
                    </li>
                    <li class="driver-dashboard-treeview {{ (Route::current()->uri() == 'driver/edit_profile' || Route::current()->uri() == 'driver/vehicle_view'  || Route::current()->uri() == 'driver/documents' || Route::current()->uri() == 'driver/membership' || Route::current()->uri() == 'driver/bank_details' || Route::current()->uri() == 'driver/referral') ? 'active' : ''  }}">
                        <a href="#">
                            <span>{{trans('messages.header.account.root')}}</span><i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="driver-dashboard-treeview-menu">
                            <li class="{{ (Route::current()->uri() == 'driver/edit_profile') ? 'active' : ''  }}">
                                <a href="{{ url('driver/edit_profile') }}">
                                    <span>{{trans('messages.header.account.edit_profile')}}</span>
                                </a>
                            </li>
                            
                            <li class="{{ (Route::current()->uri() == 'driver/vehicle_view') ? 'active' : ''  }}">
                                <a href="{{ url('driver/vehicle_view') }}">
                                    <span>{{trans('messages.header.account.vehicle_view')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Route::current()->uri() == 'driver/documents') ? 'active' : ''  }}">
                                <a href="{{ url('driver/documents') }}">
                                    <span>{{trans('messages.header.account.documents')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Route::current()->uri() == 'driver/membership') ? 'active' : ''  }}">
                                <a href="{{ url('driver/membership') }}">
                                    <span>{{trans('messages.header.account.manage_membership')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Route::current()->uri() == 'driver/bank_details') ? 'active' : ''  }}">
                                <a href="{{ url('driver/bank_details') }}">
                                    <span>{{trans('messages.header.account.bank_details')}}</span>
                                </a>
                            </li>

                            <li class="{{ (Route::current()->uri() == 'driver/referral') ? 'active' : ''  }}">
                                <a href="{{ url('driver/referral') }}">
                                    <span>{{trans('messages.header.account.referral')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ (Route::current()->uri() == 'driver/help') ? 'active' : ''  }}">
                        <a href="{{ url('driver/help') }}">{{trans('messages.header.help')}}</a>
                    </li>
                </ul>
            </div>

            <script>
                var treeContainer = document.getElementsByClassName("driver-dashboard-treeview");
                for (var tree of treeContainer) {
                    tree.addEventListener("click", function() {
                        if (this.className.indexOf("active") >= 0) {
                            this.className = this.className.replace("active", "");
                        } else {
                            for (var tree1 of treeContainer) {
                                if (tree1 != this) {
                                    tree1.className = tree1.className.replace("active", "");
                                }
                            }
                            this.className = this.className + " active";
                        }
                    });
                }
            </script>
