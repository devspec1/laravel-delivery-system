<div style="margin-top: 3em; display: flex; width: 100%; justify-content: flex-end; margin-bottom: 1em;">
    <div style="display: flex; align-items: center">
      <div class="img--circle img--bordered img--shadow fixed-ratio fixed-ratio--1-1">

                                 <a href="{{ url('driver_profile') }}">
                                   
                                @if(@Auth::user()->profile_picture->src == '')
                                <img src="{{ url('images/user.jpeg')}}" class="randomPicLarge img--full fixed-ratio__content">

                                @else
                                <img src="{{ @Auth::user()->profile_picture->src }}"  class="randomPicLarge img--full fixed-ratio__content profile_picture">
                                @endif
                            </a>
                            </div>
                         <div style="display: flex; flex-direction: column; align-items: center; margin-left: 1em">
                        <span style="font-size: 170%; opacity: 0.9; font-family: 'MontserratReg';font-weight: bold">{{ @Auth::user()->first_name}} {{ @Auth::user()->last_name}}</span>
                         <span style="font-size: 95%; opacity: 0.8; font-weight: bold; font-family: 'MontserratReg';">Community Leader</span>
                        </div>
      
    </div>
  </div>

