<title>Help</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
 

  <div style="display: flex; width: 100%" id="helpWrp">
    <span style="font-size: 200%; color: #1B187F;opacity: 0.8; font-weight: bold; font-family:'MontserratReg'">Help</span>
    <div style="display: flex; flex-direction: row; justify-content: space-between;" id="helpTopWrp">
   
      <div style="display: flex; align-items: center;">
        <span>Call support 1800 841 799</span> <img src="{{asset('images/icon/call.png') }}">
      </div>
      <div style="display: flex; align-items: center;">
        <span>Live chat support</span> <img src="{{asset('images/icon/msgbubble.png') }}">
      </div>

    </div>
    <div style="display: flex; width: 100%;" id="helpRightWrp">
       <div  style=" width: 100%; flex-direction: column" id="helpFaqWrp" data-tab="livechat">
        <h3 style="font-size: 130%; color: #3B5998; margin: 0; font-family: 'MontserratBold'"> Live chat support</h3>
        
      </div>
      <div class="current" style=" width: 100%; flex-direction: column; height: 35em" id="helpFaqWrp" data-tab="faq">
        <span style="font-size: 170%;  margin-bottom: 0.75em;margin-top: 0.75em; font-family: 'MontserratReg'; font-weight: bold"> FAQ's</span>
        <div class="current">
          <span>Lorem ipsum</span>
          <span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. Fusce commodo convallis lorem, eget laoreet nisl tempor non.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. <br><br>Fusce commodo convallis lorem, eget laoreet nisl tempor non.Nulla ac diam a purus dictum pellentesque. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce eget mollis nisl. Sed nisi augue, viverra quis condimentum ac, accumsan ut orci.
          </span>


        </div>
         <div>
          <span>Lorem ipsum</span>
           <span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. Fusce commodo convallis lorem, eget laoreet nisl tempor non.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. <br><br>Fusce commodo convallis lorem, eget laoreet nisl tempor non.Nulla ac diam a purus dictum pellentesque. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce eget mollis nisl. Sed nisi augue, viverra quis condimentum ac, accumsan ut orci.
          </span>
        </div>
         <div>
          <span>Lorem ipsum</span>
            <span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. Fusce commodo convallis lorem, eget laoreet nisl tempor non.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. <br><br>Fusce commodo convallis lorem, eget laoreet nisl tempor non.Nulla ac diam a purus dictum pellentesque. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce eget mollis nisl. Sed nisi augue, viverra quis condimentum ac, accumsan ut orci.
          </span>
        </div>
         <div>
          <span>Lorem ipsum</span>
            <span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. Fusce commodo convallis lorem, eget laoreet nisl tempor non.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. <br><br>Fusce commodo convallis lorem, eget laoreet nisl tempor non.Nulla ac diam a purus dictum pellentesque. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce eget mollis nisl. Sed nisi augue, viverra quis condimentum ac, accumsan ut orci.
          </span>
        </div>
         <div>
          <span>Lorem ipsum</span>
            <span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. Fusce commodo convallis lorem, eget laoreet nisl tempor non.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas imperdiet sollicitudin diam. Phasellus malesuada in ante sit amet accumsan. <br><br>Fusce commodo convallis lorem, eget laoreet nisl tempor non.Nulla ac diam a purus dictum pellentesque. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce eget mollis nisl. Sed nisi augue, viverra quis condimentum ac, accumsan ut orci.
          </span>
        </div>
      </div>
      <div  style=" width: 100%; flex-direction: column" id="helpFaqWrp" data-tab="callsupp">
        <h3 style="font-size: 130%; color: #3B5998; margin-bottom: 1.5em; font-family: 'MontserratBold'"> Call support</h3>
        
      </div>
    </div>

  
</div>

</div>
</div>
</div>
</div>
</div>
</div>
</main>


@stop
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
<script>
  $(function() {
    $("#helpLeftWrp span").click(function() {
       $("#helpRightWrp > div.current").removeClass("current");
       $("#helpRightWrp > div[data-tab='" + $(this).data("tab") + "']").addClass("current");
      $("#helpLeftWrp span.current").removeClass("current");
      $(this).addClass("current");

    })

    $("#helpFaqWrp > div").click(function() {
    
      $("#helpFaqWrp > div.current").removeClass("current");
      $(this).addClass("current");
      $(this).find("span").eq(1).show();
    })
  })
</script>
<style>
  li > div > div.question {
    display: flex; 
    justify-content: space-between; 
    padding-right:10px;
    font-size: 20px;
    padding-bottom: 15px;
  }
  li > div > div.question:hover {
    cursor: pointer;
  }
  li > div > div.answer {
    display: flex; 
    display: none;
    padding-right:10px;
    font-size: 16px;
    padding-bottom: 15px;
  }
  li > div > div.answer.active {
    display: flex; 
    padding-right:10px;
    font-size: 16px;
    background-color: white;
  }
</style>

<script>
  function toggleItem(obj) {
    var answerDiv = obj.nextElementSibling;
    var itemClass = answerDiv.className;
    if (itemClass.indexOf(" active") >= 0)  {
      itemClass = itemClass.replace(" active", "");
    } else {
      var answers = document.getElementsByClassName("answer");
      for(var answer of answers) {
        answer.className = answer.className.replace(" active", "");
      }
      itemClass = itemClass + " active";
    }

    answerDiv.className = itemClass;
    // for (i = 0; i< question.length; i++) {
    //   answer[i].className = 'answer close';
    // }
    // if (itemClass == 'answer close') {
    //   this.parentNode.className = 'answer open';
    // }
  }
</script>