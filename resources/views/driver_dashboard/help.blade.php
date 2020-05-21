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
        <span style="font-size: 135%;  margin-bottom: 0.75em;margin-top: 0.75em; font-family: 'MontserratReg'; font-weight: bold"> FAQ's</span>

        <?php $faqc= 0; foreach($faq_array as $fq) { ?>
        <div <?php if($faqc == 0) echo 'class="current"'; ?>>
          <span> {{ $fq['question'] }}</span>
          <span> {{ $fq['answer'] }} </span>
        <?php $faqc++;?>
        </div>
        <?php 

         } ?>


      
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

<script src="https://wchat.freshchat.com/js/widget.js"></script>
<script>
  window.fcWidget.init({
    token: "e749c074-1b71-45cd-bfc8-4ae1d8bd2b",
    host: "https://wchat.freshchat.com",
    //Have the widget open on load by default by setting the below value to true
    open: false,
    config: {
      //Disable Events Tracking
      disableEvents: true,
      cssNames: {
        widget: 'fc_frame',
        open: 'fc_open',
        expanded: 'fc_expanded'
      },
      showFAQOnOpen: true,
      hideFAQ: true,
      agent: {
        hideName: false,
        hidePic: true,
        hideBio: true,
      },
      headerProperty: {
        backgroundColor: '#FFFF00',
        foregroundColor: '#333333',
        backgroundImage: 'https://wchat.freshchat.com/assets/images/texture_background_1-bdc7191884a15871ed640bcb0635e7e7.png',
        //Hide the chat button on load
        hideChatButton: false,
        //Set Widget to be left to right.
        direction: 'ltr'
      },
      content: {
        placeholders: {
          search_field: 'Search in my widget',
          reply_field: 'Reply in my widget',
          csat_reply: 'Reply for csat'
        },
        actions: {
          csat_yes: 'Yes, Resolved',
          csat_no: 'No, Resolved',
          push_notify_yes: 'Notify',
          push_notify_no: 'No Notify',
          tab_faq: 'Knowledge',
          tab_chat: 'Message',
          csat_submit: 'Submit Review Comments'
        },
        headers: {
          chat: 'Chat with us',
          chat_help: 'Reach out to us if you have any questions',
          faq: 'Knowledge Base',
          faq_help: 'Browse our faqs',
          faq_not_available: 'No FAQS',
          faq_search_not_available: 'No FAQS available for ',
          faq_useful: 'FAQS is useful',
          faq_thankyou: 'Thanks for feedback',
          faq_message_us: 'Message Us For FAQs',
          push_notification: 'you want to not miss conversation',
          csat_question: 'Did we address your question?',
          csat_yes_question: 'Did we resolve the conversation?',
          csat_no_question: 'Did we not resolve the conversation?',
          csat_thankyou: 'Thanks for the response',
          csat_rate_here: 'Give your rating here',
          channel_response: {
            offline: 'We are currently away',
            online: {
              minutes: {
                one: "You will get a reply in a minute",
                more: "You will get a reply in{time minutes"
              },
              hours: {
                one: "You will get a reply in a hour",
                more: "You will get a reply in{time hours",
              }
            }
          }
        }
      }
    }
  });
</script>
