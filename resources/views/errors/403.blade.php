 @extends('template')
 
   @section('main')
    
    <main id="site-content" role="main">
      
<div class="page-container-responsive" style="min-height: 485px">
  <div class="row row-space-top-8 row-space-8">
    <div class="col-md-12 text-center">
      <h1 class="text-jumbo text-ginormous hide-sm">{{trans('messages.errors.unauthorize')}}</h1>
      <!-- <h1 class="text-jumbo text-ginormous hide-sm">Coming Soon!</h1> -->
      <!-- <h2></h2> -->
      <!-- <h2>We are working on this page, will update it soon.</h2> -->
      <h4>{{trans('messages.errors.content_block')}}</h4>
    </div>
  </div>
</div>

    </main>

@stop
<style type="text/css">
  .row.row-space-top-8.row-space-8{
    margin-top: 230px !important;
    margin-bottom: 250px !important;
  }
</style>