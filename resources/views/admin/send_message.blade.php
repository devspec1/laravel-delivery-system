@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper ng-cloak" >
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Send Message
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><a href="#">Send Message</a></li>
        
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Send Message Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => LOGIN_USER_TYPE.'/send_message', 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label class="col-sm-3 control-label radio_label">To<em class="text-danger">*</em></label>
                  <div class="col-sm-2">
                  <input type="radio" id="all_user" name="to" value="to_all" class="send_to get_user_class" checked>
                  <label class="user_label" for="all_user">All</label>
                  </div>
                  <div class="col-sm-2">
                    <input type="radio" id="specific_user"  name="to" value="to_specific" class="send_to get_user_class">
                    <label class="user_label" for="specific_user" >Specific Users</label>
                  </div>
                </div>

                @if(LOGIN_USER_TYPE!='company')
                <div class="form-group">
                  <label class="col-sm-3 control-label radio_label">User Type<em class="text-danger">*</em></label>
                    <div class="col-sm-3">
                    <input type="radio" id="all_type" name="user_type" value="all" class="get_user_class get_user_type" checked>
                    <label class="user_label" for="all_type">Rider & Driver & Company</label>
                    </div>
                    <div class="col-sm-2">
                      <input type="radio" id="all_rider"  name="user_type" value="Rider" class="get_user_class get_user_type">
                      <label class="user_label" for="all_rider" >Rider</label>
                    </div>
                  <div class="col-sm-2">
                    <input type="radio" id="all_driver"  name="user_type" value="Driver" class="get_user_class get_user_type" {{LOGIN_USER_TYPE=='company'?'checked':''}}>
                    <label class="user_label" for="all_driver" >Driver</label>
                  </div>
                    <div class="col-sm-2">
                      <input type="radio" id="all_company"  name="user_type" value="Company" class="get_user_class get_user_type">
                      <label class="user_label" for="all_company" >Company</label>
                    </div>
                </div>
                @endif

                <div class="form-group" id="email_textbox" style="display:none">
                  <label for="input_email" class="col-sm-3 control-label">
                    {{ (LOGIN_USER_TYPE == 'company') ? 'Drivers': 'Users' }} 
                  <em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    <input type="text" id="input-tags3" name="users" value="" />
                    
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label radio_label">Message Type<em class="text-danger">*</em></label>
                  <div class="col-sm-2">
                    <input type="radio" id="all_sms"  name="message_type" value="sms" class="message_type" checked>
                    <label class="user_label" for="all_sms" >SMS</label>
                  </div>
                  <div class="col-sm-2">
                    <input type="radio" id="all_push"  name="message_type" value="push" class="message_type">
                    <label class="user_label" for="all_push" >Push Notification</label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_message" class="col-sm-3 control-label">Message<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    <textarea id="txtEditor" ng-model="inputMessage" maxlength="160" name="txtEditor"></textarea>
                    <p class="text-right">@{{ 160 - inputMessage.length + ' remaining' }}</p>
                    <span class="text-danger">{{ $errors->first('txtEditor') }}</span><br>
                    <span class="text-danger">{{ $errors->first('users') }}</span>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <a href="{{url(LOGIN_USER_TYPE.'/send_message')}}" class="btn btn-default">Cancel</a>
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
              </div>
              <!-- /.box-footer -->
            {!! Form::close() !!}
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@stop

@push('scripts')
<script src="{{ url('js/selectize.js') }}"></script>
<script type="text/javascript">
$('.send_to').click(function()
{
  if($(this).val() == 'to_specific')
    $('#email_textbox').show();
  else
    $('#email_textbox').hide();
});
$(document).ready(function(){
$('#input-tags3').selectize({
    plugins: ['remove_button'],
    delimiter: ',',

    create: false
});
var err_user="{{ $errors->first('users') }}";

if(err_user!="")
{
  $("#specific_user").prop('checked', true);
  $('#email_textbox').show();
  init_user();

}
$(".get_user_class").change(function(){
init_user();
})
$(".message_type").change(function(){
  if ($(".message_type:checked").val() == 'push') {
    $("#all_company").attr("disabled", true);
    if ($(".get_user_type:checked").val() == 'Company') {
      $("#all_company").prop("checked", false);
      var select = $("#input-tags3").selectize();
      var selectize = select[0].selectize;
      selectize.clear();
      selectize.clearOptions();
    }
  }else{
    $("#all_company").attr("disabled", false);
  }
})
function init_user()
{
  var toval=$(".send_to:checked").val();
  var usertype= (LOGIN_USER_TYPE != 'company') ? $(".get_user_type:checked").val() : 'Driver';
  if(toval!="to_all")
  {
    var select = $("#input-tags3").selectize();
    var selectize = select[0].selectize;
    selectize.disable();
    $.ajax({
      type: 'POST',
      url: APP_URL+'/{{LOGIN_USER_TYPE}}/get_send_users',
      data: "type="+usertype,
      dataType: "json",
      success: function(resultData) {
        var select = $("#input-tags3").selectize();
        var selectize = select[0].selectize;
        selectize.clear();
        selectize.clearOptions();
        $.each(resultData, function (key, value) {
          selectize.addOption({value:value.user_type+'-'+value.id,text:value.first_name + ' - ' +value.mobile_number});
        });
        selectize.enable();
      }
    });
  }
}
})
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>
@endpush