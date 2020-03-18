<style>
  .goog-te-banner-frame.skiptranslate {
    display: none !important;
  } 
  body {
    top: 0px !important; 
  }
</style>
<header class="main-header hide">
    <!-- Logo -->
    <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>{{ $site_name }}</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{{ $site_name }}</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
        <span id="show_date_time" class="show_date_time" style="color:white; font-size:16px; line-height: 46px;"></span>
      <div class="navbar-custom-menu">
         <div id="google_translate_element" class="google-translate-element"></div>
         @php
            if(LOGIN_USER_TYPE=='company'){
              $user = Auth::guard('company')->user();
              $company_user = true;
            }else{
              $user = Auth::guard('admin')->user();
              $company_user = false;
            }
          @endphp
        <ul class="nav navbar-nav">
          <input type="hidden" id="current_time" value="{{ date('F d, Y H:i:s', time()) }}">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                @if(!$company_user || $user->profile ==null)
                  <img src="{{ url('admin_assets/dist/img/avatar04.png') }}" class="user-image" alt="User Image">
                @else
                  <img src="{{ $user->profile }}" class="user-image" alt="User Image">
                @endif
              
              <span class="hidden-xs">{{ (!$company_user)?$user->username:$user->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">

               @if(!$company_user || $user->profile ==null)
                  <img src="{{ url('admin_assets/dist/img/avatar04.png') }}" class="img-circle" alt="User Image">
                @else
                  <img src="{{ $user->profile }}" class="img-circle" alt="User Image">
                @endif

                <p>
                  {{ (!$company_user)?$user->username:$user->name }}
                  <small>Member since {{ date('M. Y', strtotime($user->created_at)) }}</small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                @if($company_user)
                  <div class="pull-left">
                    <a href="{{ url('company/profile') }}" class="btn btn-default btn-flat">Profile</a>
                  </div>
                @endif

                 <div class="pull-right">
                  <a href="{{ url($company_user ? 'company/logout' : 'admin/logout') }}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
      @if($company_user)
        <select id="js-currency-select" class="form-control" style="display: none;">
          @foreach($currency_select as $code)
          <option value="{{$code}}" @if(session('currency') == $code ) selected="selected" @endif >{{$code}}</option>
          @endforeach
        </select>
      @endif
    </nav>
  </header>
  
  <div class="flash-container hide">
    @if(Session::has('message'))
      <div class="alert text-center {{ Session::get('alert-class') }}" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
        {{ Session::get('message') }}
    </div>
    @endif
  </div>

<style type="text/css">
  #js-currency-select{
    padding: 1px 7px;
    float:right;
    font-size: 13px;
    display: inline-block;
    color: #000;
    height: 24px;
    margin:13px 6px 3px;
    border-color: rgb(169, 169, 169);
    width: auto;
  }
</style>