<title>Help</title>
@extends('template_driver_dashboard') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;" ng-controller="facebook_account_kit">
  <div class="page-lead separated--bottom  text--center text--uppercase">
    <h1 class="flush-h1 flush">{{trans('messages.header.help')}}</h1>
  </div>

  <ul class="help-list">
    @foreach ($faq_array as $faq)
      <li>
        <div class="faq">
          <div class="question" onClick="toggleItem(this)"><div><span>{{ $faq["question"] }}</span></div><div>></div></div>
          <div class="answer"><span>{{ $faq["answer"] }}</span></div>
        <div>
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

<style>
  li > div > div.question {
    display: flex; 
    justify-content: space-between; 
    padding-right:10px;
    font-size: 20px;
  }
  li > div > div.question:hover {
    cursor: pointer;
  }
  li > div > div.answer {
    display: flex; 
    display: none;
    padding-right:10px;
    font-size: 16px;
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