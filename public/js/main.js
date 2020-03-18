 $(document).ready(function(){ 
$('.nav-click').click(function(e){
$('.nav-div').css('left' , '0px');
e.stopPropagation();
});
$('.remove-bold').click(function(e){
$('.nav-div').css('left' , '-500px');
e.stopPropagation();
});
$('body').click(function(){
$('.nav-div').css('left' , '-500px');
});
$('.nav-div').click(function(e){
$('.nav-div').css('left' , '0px');
e.stopPropagation();
});
$('.green-map').mouseover(function(){
$('.map-tooltip').show();
});
$('.green-map').mouseout(function(){
$('.map-tooltip').hide();
});
$('.user-flyout').mouseover(function(){
$('.flyout__content ').show();
});
$('.user-flyout').mouseout(function(){
$('.flyout__content ').hide();
});

$('.map-tooltip').mouseover(function(){
$('.map-tooltip').show();
});
$('.map-tooltip').mouseout(function(){
$('.map-tooltip').hide();
});
$('.ride-link').click(function(){
$('.show-list-nav').hide();
$('.signin-link').hide();
$('.ride-div').show();
});
$('.back-ride').click(function(){
$('.show-list-nav').show();
$('.signin-link').show();
$('.ride-div').hide();
});
$('.drive-link').click(function(){
$('.show-list-nav').hide();
$('.signin-link').hide();
$('.drive-div').show();
});
$('.back-drive').click(function(){
$('.show-list-nav').show();
$('.signin-link').show();
$('.drive-div').hide();
});
$('.city-link').click(function(){
$('.show-list-nav').hide();
$('.signin-link').hide();
$('.city-div').show();
});
$('.back-city').click(function(){
$('.show-list-nav').show();
$('.signin-link').show();
$('.city-div').hide();
});
$('.get-started').click(function(){
$('.locate-pin').show();
$('.locate-content').hide();
$('.get-started').hide();
});
  $('.popup-btn1').click(function(e){
              e.preventDefault();
                 $("body").addClass("pos-fix");
                $(".popup1").show();
            });
             $('.login-close').click(function(event){
                    $("body").removeClass("pos-fix");
                $(".popup1").hide(); 
            });
                  $('.close-btn').click(function(event){

                    $("body").removeClass("pos-fix");
                $(".popup1").hide(); 

            });
            $('.top-home').click(function(event){
    event.stopPropagation();
    });
             $('.popup-btn2').click(function(e){
              e.preventDefault();
                 $("body").addClass("pos-fix");
                $(".popup2").show();
            });
             $('.login-close').click(function(event){
                    $("body").removeClass("pos-fix");
                $(".popup2").hide(); 
            });
                  $('.close-btn').click(function(event){
                    
                    $("body").removeClass("pos-fix");
                $(".popup2").hide(); 

            });

$('.bxslider').bxSlider({
  minSlides: 1,
  maxSlides: 2,
  slideWidth: 500,
  slideMargin: 25
});

var slider = $("div#mySliderTabs").sliderTabs();

$('.bx-prev').click(function(){
	$('.pos-abs-hide').addClass('pos-abs-left');
$('.pos-abs-hide').removeClass('pos-abs-right');
$(this).hide();
$('.bx-next').show();
	});
$('.bx-next').click(function(){
	$('.pos-abs-hide').addClass('pos-abs-right');
$('.pos-abs-hide').removeClass('pos-abs-left');
$(this).hide();
$('.bx-prev').show();
	});
$('#category-tabs li a').click(function(){
    $(this).next('ul').slideToggle('500');
    $(this).parent().toggleClass('back-ash');
    $(this).find('i').toggleClass('icon_right-arrow icon_down-arrow')
    });
});
   $(function() {
    $(".rslides").responsiveSlides();
    $("#slider1").responsiveSlides({
        auto: false,
        pager: true,
        nav: true,
        speed: 500,
        maxwidth: 800,
        namespace: "centered-btns"
      });
     $("#slider2").responsiveSlides({
        auto: false,
        pager: true,
        nav: true,
        speed: 500,
        maxwidth: 800,
        namespace: "centered-btns"
      });
  });
