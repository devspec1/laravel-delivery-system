@extends('template_without_header_footer') 
@section('main')
<style> body, html { height: 100%; }</style>
<div id="newLoginWrp" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
    <div style="height: 80%; width: 40%; padding: 1em; padding-top: 4em; padding-bottom: 4em; display: flex; flex-direction: column; align-items: center; justify-content: space-between; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); font-family: 'MontserratReg">

        <img src="{{ asset('images/logos/logo.png') }}" style="height: 13em">

        <h1 style="font-family: 'MontserratReg'; font-weight: bold">Login</h1>
         <div style="display: flex; align-items: center; margin-top: 0.4em;margin-bottom: 0.9em;padding: 1.5em; width: 65%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)"> <img src="{{ asset('images/icon/mail.png')}}" style="heigth: 1.3em; width: 1.8em; opacity: 0.5; margin-right: 2em"> <input style="border: none; background: transparent; font-size: 115%; font-weight: bold" type="text" placeholder="Email"> </div>
        <div style="display: flex; align-items: center; margin-top: 0.4em;margin-bottom: 0.9em;padding: 1.5em; width: 65%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)"> <img src="{{ asset('images/icon/password.png')}}" style="heigth: 1.3em; width: 1.8em; opacity: 0.7; margin-right: 2em"> <input style="border: none; background: transparent; font-size: 115%; font-weight: bold" type="text" placeholder="Password"> </div>
        <span class="span1"> Forgot password ?</span>
        <span class="spanB1" style="background-color: #3B5998;">Login <img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 6em;  height: 1.2em; width: 1.2em"></span>

        <span class="span1">Don't have account ? </span>
        <span class="spanB1" style="background-color: #4B4B4B;r">Apply</span>

    </div>
</div>
</main>
@stop