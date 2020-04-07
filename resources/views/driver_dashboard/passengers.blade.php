<title>Passengers</title>
@extends('template_driver_dashboard') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;">
  <div class="page-lead separated--bottom  text--center text--uppercase">
    <h1 class="flush-h1 flush">{{trans('messages.header.passengers')}}</h1>
  </div>
  <ul class="user-list">
    @foreach ($referrals as $user)
      <li>
        <span>
          <img class="user-profile-image" src="{{ $user["profile_image"] }}" alt="user-profile" />
        </span>
        <span>{{ $user["name"] }}</span>
      </li>
    @endforeach
  </ul>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
@stop