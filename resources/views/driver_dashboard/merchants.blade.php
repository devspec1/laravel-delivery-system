<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div id="leaderboardMainWrp">
           <div style="display: flex;width: 100%">
              <span style="font-size: 200%; color: #1B187F;opacity: 0.8; font-weight: bold; font-family:'MontserratReg'">Merchants</span>
              
            </div>
            <div id="merchantsSub" style="display: flex; flex-direction: column;" >
              <div class="merchantsSubHeader">
                <span>Trading Name</span>
                <span>Location</span>
                <span>Since</span>   
              </div>
              <div class="subWrapper1" id="merchantsSubWrp">
              <?php foreach($merchants as $m) { ?>
                <div>
                <div>
                 {{-- @if(!isset($m->profile_picture->src))
                                <img src="{{ url('images/user.jpeg')}}">

                                @else
                                <img src="{{ $m->profile_picture->src }}" >
                                @endif <span> <\?php echo $m->first_name. " " . $m->last_name; \?> </span> </div> --}}
                <span><?php echo $m->trading_name; ?></span></div>
                <span><?php echo $m->address; ?></span>
                <span><?php echo $m->since; ?></span>
                </div>
              <?php  } ?>
            </div>
               
              
            </div>



  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
</main>
@stop