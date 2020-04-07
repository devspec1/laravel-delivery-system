<!-- <div class="container-fluid page-footer-back" style="padding: 0">
	<div class="footer-img1  footercontent"><img src="{{url('images/icon/footer2_2.png')}}"></div>
</div> -->
<style type="text/css">
    .padd-side-85 {
        padding-left: 85px;
        padding-right: 85px;
    }
    .footer-copyright{
        background: #3b312f;
    
    }
    .footer-copyright .p-font{
        font-size:24px !important;
        font-family: 'Myriad Pro';
        font-weight: italic;
    }
    .top-border{
        /*padding-top: 40px !important;*/
        border-top: 4px solid #7d7d7d !important;
       /* padding-bottom: 40px !important;
        float: left;
        width: 100%;*/
    }
    
        
    </style>
    
    <footer style="display: none" class="container-fluid top-border">
    
        <div
            class="col-lg-12 col-md-12 col-sm-12 col-xs-12 footer-back pull-app-gutter--sides soft-app-gutter--sides padd-side-85">
            <div class="footer-head nt_fot col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="layout" style="display: flex; align-items: center;     justify-content:space-between;">
                    <div class="layout_item col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <a href="{{ url('/') }}"><img src="{{asset('images/footer-logo.png')}}" width="108px"></a>
                    </div>
    
                    <div class="app-links clearfix">
                        @if($app_links[2]->value !="" )
                        <a href="{{$app_links[2]->value}}" target="_blank" class="ios_class">
                            <img src="{{ url('images/icon/google-play1.png') }}" alt="Get it on Googleplay"
                                style="width:190px" class="CToWUd bot_footimg">
                        </a>
                        @endif
    
                        @if($app_links[0]->value !="" )
                        <a class="googleplay_class" href="{{$app_links[0]->value}}" target="_blank">
                            <img src="{{ url('images/appstore.svg') }}" alt="Download on the Appstore" style="width:190px"
                                class="CToWUd">
                        </a>
                        @endif
    
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 footer-back pull-app-gutter--sides soft-app-gutter--sides"
            style="padding-top: 35px !important; padding-left:85px; padding-right: 85px;">
            <div class="footer-head" style=" border-bottom: 0px solid #7d7d7d !important;">
                
                <div class="col-lg-4 col-md-2 col-sm-2 col-xs-12">
                    <ul class="nav-list-one " style="padding:0px 15px; font-size: 24pt;">
                        <!-- <li>
                            <a href="{{ url('ride') }}" class="city-link">{{trans('messages.footer.ride')}}
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('drive') }}" class="city-link">{{trans('messages.footer.drive')}}
                            </a>
                        </li> -->
                        <li>
                            <a class="" href="http://localhost/laravel-app-2.1/public/about_us">
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('safety') }}" class="city-link">{{trans('messages.footer.safety')}}
                            </a>
                        </li>

                        <li>
                            <a class="_style_2HGMjk" href="http://localhost/laravel-app-2.1/public/how_it_works">
                                How It Works
                            </a>
                        </li>
    
                    </ul>
                </div>
                <div class="col-lg-6 col-md-3 col-sm-3 col-xs-12">
                    <ul class="nav-list-one " id="top-city-footer-small" style="padding:0px 15px; font-size: 24pt;">
                        <li>
                            <a class="" href="http://localhost/laravel-app-2.1/public/terms_of_service">
                                Terms of Service
                            </a>
                        </li>
                        <li>
                            <a class="" href="http://localhost/laravel-app-2.1/public/privacy_policy">
                                Privacy Policy
                            </a>
                        </li>
                       
                    </ul>
    
                   <!--  <ul class="nav-list-one " id="top-city-footer-small" style="padding:0px 15px; font-size: 24pt;">
                        @foreach($company_pages as $company_page)
                        <li>
                            <a class="" href="{{ url($company_page->url) }}">
                                {{ $company_page->name }}
                            </a>
                        </li>
                        @endforeach
                        
                    </ul> -->
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 social-icons">
                    <div class="foot_soc" style="display: flex; justify-content: space-between;">
                        <a href="https://www.facebook.com/Trioangle.Technologies/" target="_blank">
                            <span class="fa fa-facebook"></span>
                        </a>
                        <a href="https://www.instagram.com/trioangletech" target="_blank">
                            <span class="fa fa-instagram"></span>
                        </a>
                        <a href="https://www.linkedin.com/company/13184720" target="_blank">
                            <span class="fa fa-linkedin"></span>
                        </a>
                        <a href="https://www.youtube.com/channel/UC2EWcEd5dpvGmBh-H4TQ0wg" target="_blank">
                            <span class="fa fa-youtube"></span>
                        </a>
                        
                    </div>
    
                    <!-- <div class="foot_soc">
                        @for($i=0; $i < $join_us->count(); $i++)
                            @if($join_us[$i]->value)
                            <a href="{{ $join_us[$i]->value }}" target="_blank">
                                <span class="fa fa-{{ str_replace('_','-',$join_us[$i]->name) }}"></span>
                            </a>
                            @endif
                        @endfor
                    </div> -->
                </div>
            </div>
        
     
        <div class="footer-copyright">
        <div class="text-center footlo">
            <span class="_style_zVjAb p-font" dir="ltr" data-reactid="661"><em>© Ride On Drivers Owners Group Pty Ltd</em></span>
        </div></div>
    </footer>
 
    <style type="text/css">
    footer .nav-list-one li {
        padding-bottom: 5px;
    }
    
    #top-city-footer li {
        display: inline-block;
        padding-right: 15px;
    }
    
    #top-city-footer li a {
        font-size: 13px !important;
    }
    </style>