<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div id="leaderboardMainWrp">
           <div style="display: flex;width: 100%">
              <span style="font-size: 200%; color: #1B187F;opacity: 0.8; font-weight: bold; font-family:'MontserratReg'">Leaderboard</span>
              
            </div>
            <div style="display: flex; flex-direction: column; width: 100%" id="leaderboardSubWrp">
             <div class="leaderboardSubHeader">
                <span style="width: 30%">Driver Name</span>
                <span>Location</span>
                <span>Since</span>
                <span style="width: 7em">Status</span>   
              </div>
               <div>
                <div> <img src="{{ url('images/user.jpeg')}}"> <span> Joe Corrigan </span> </div>
                <span>The Ants Cafe, 2259 14A</span>
                <span>Jan 2019</span>
                <span class="statusActive1 status1">Active</span> 
                </div>
                <div>
                <div> <img src="{{ url('images/user.jpeg')}}"> <span> Joe Corrigan </span> </div>
                <span>The Ants Cafe, 2259 14A</span>
                <span>Jan 2019</span>
                <span class="statusPending1 status1">Pending</span> 
                </div>
                <div>
                <div> <img src="{{ url('images/user.jpeg')}}"> <span> Joe Corrigan </span> </div>
                <span>The Ants Cafe, 2259 14A</span>
                <span>Jan 2019</span>
                <span class="statusActive1 status1">Active</span> 
                </div>
                <div>
                <div> <img src="{{ url('images/user.jpeg')}}"> <span> Joe Corrigan </span> </div>
                <span>The Ants Cafe, 2259 14A</span>
                <span>Jan 2019</span>
                <span class="statusActive1 status1">Active</span> 
                </div>
                <div>
                <div> <img src="{{ url('images/user.jpeg')}}"> <span> Joe Corrigan </span> </div>
                <span>The Ants Cafe, 2259 14A</span>
                <span>Jan 2019</span>
                <span class="statusActive1 status1">Active</span> 
                </div>
                <div>
                <div> <img src="{{ url('images/user.jpeg')}}"> <span> Joe Corrigan </span> </div>
                <span>The Ants Cafe, 2259 14A</span>
                <span>Jan 2019</span>
                <span class="statusActive1 status1">Active</span> 
                </div>
              
            </div>



  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>

</main>
@stop