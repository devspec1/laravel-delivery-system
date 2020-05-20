@extends('template_without_header_footer') 
@section('main')
<style> body, html { height: 100%; }</style>
<div id="newLoginWrp" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
    <form id="newLoginForm" action="{{ url('driver/submit_password_reset') }}" method="post" >
        <input type="hidden" name="id" value="{{ $result->id }}">

        @csrf

        <img src="{{ asset('images/logos/logo.png') }}" id="logo1">
         <div style="display: flex; flex-direction: column; width: 100%; align-items: center">
        <h1 style="font-family: 'MontserratReg'; font-weight: bold; font-size: 170%">Reset password</h1>
        @if($errors->any())
        <h4 style="font-size: 110%; color: #a84632">{{$errors->first()}}</h4>
        @endif
        <div style="display: flex; flex-direction: column; width: 100%;margin-top: 2em; margin-bottom: 2em; align-items: center">
         <div style="display: flex; align-items: center; margin-top: 0.4em;margin-bottom: 0.9em;padding: 1.5em; width: 65%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)"> <img src="{{ asset('images/icon/password.png')}}" style="heigth: 1.3em; width: 1.4em; opacity: 0.5; margin-right: 2em"> <input style="border: none; background: transparent; font-size: 90%; font-weight: bold; width: 100%" type="password" id="new_pass" name="new_password" placeholder="New password"> </div>
         <div style="display: flex; align-items: center; margin-top: 0.4em;margin-bottom: 0.9em;padding: 1.5em; width: 65%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)"> <img src="{{ asset('images/icon/password.png')}}" style="heigth: 1.3em; width: 1.4em; opacity: 0.5; margin-right: 2em"> <input style="border: none; background: transparent; font-size: 90%; font-weight: bold; width: 100%" type="password" id="confirm_new_pass" name="confirm_password" placeholder="Confirm new password"> </div>
     </div>
    
        <span class="spanB1" id="loginBtn" style="background-color: #3B5998;">Submit<img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 6em;  height: 1.2em; width: 1.2em"></span>
    </div>
    <div></div>


    </form>
</div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>


    
<script>

    $(function() {

        $("#newLoginForm input").keyup(function(e) {
            if(e.keyCode == 13) {

                 var b = 1;
                 $("#newLoginForm input").each(function() {
                     $(this).parent().css("box-shadow", "none");
                     $(this).parent().css("border", "1px solid rgba(0, 0, 0, 0.12)");
                    if(!($(this).val())) {
                    
                        b = 0;
                        $(this).parent().css("box-shadow", "0px 0px 1px #9c432f");
                        $(this).parent().css("border", "1px solid #eb6044");
                        $(this).keyup(function() {
                            if($(this).val().length > 0) {
                                $(this).parent().css("box-shadow", "none");
                                $(this).parent().css("border", "1px solid rgba(0, 0, 0, 0.12)");
                                $(this).unbind("keyup");
                            }
                        })
                    }
            })
            if(b)
                $("#newLoginForm")[0].submit();
            }
        })

        $("#loginBtn").click(function() {
                 var b = 1;
            $("#newLoginForm input").each(function() {
                     $(this).parent().css("box-shadow", "none");
                     $(this).parent().css("border", "1px solid rgba(0, 0, 0, 0.12)");
                    if(!($(this).val())) {
                    
                        b = 0;
                        $(this).parent().css("box-shadow", "0px 0px 1px #9c432f");
                        $(this).parent().css("border", "1px solid #eb6044");
                        $(this).keyup(function() {
                            if($(this).val().length > 0) {
                                $(this).parent().css("box-shadow", "none");
                                $(this).parent().css("border", "1px solid rgba(0, 0, 0, 0.12)");
                                $(this).unbind("keyup");
                            }
                        })
                    }
            })
            if($("#new_pass").val() != $("#confirm_new_pass").val())
                b = 0;
            if(b)
                $("#newLoginForm")[0].submit();
        })
    })
</script>
@stop