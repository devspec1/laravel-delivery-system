<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div id="deliveryMainWrp">
           <div style="display: flex;width: 100%">
              <span style="font-size: 200%; color: #1B187F;opacity: 0.8; font-weight: bold; font-family:'MontserratReg'">Deliveries</span>
              
            </div>
            <div style="display: flex; flex-direction: column; width: 100%" >
              <div class="deliverySubHeader">
                <span>Order Completed</span>
                <span>Venue</span>
                <span>Address</span>
                <span>Driver Name</span>
                <span style="width: 10%">Total</span>
               
              </div>
              <div class="subWrapper1"  id="deliverySubWrp">
                <?php foreach($deliveries as $d) { 

                  $created_at = date_format(date_create($d->created_at), 'm/d/Y H:i:s');
                  ?>
               <div>
                <div> <span><?php echo str_replace(" ", " | ", $created_at); ?></span> <span> Order No. {{ $d->id }}</span> </div>
               
                <span style="font-size: 85%"><?php echo $d->pickup_loc; ?></span>
                <span style="font-size: 85%"><?php echo $d->drop_loc; ?></span>
               <span><?php echo $d->driver_name; ?></span>
                <span style="width: 10%"><?php echo $d->fee . "$"; ?></span>
                
              </div>
              <?php 
            } ?>
             
              
            </div>
          </div>



  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
</main>
@stop