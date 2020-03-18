@extends('vendor.installer.layouts.master')

@section('title', trans('installer_messages.settings.title'))
@section('container')
{!! Form::open(['url'=>route('LaravelInstaller::database'),'method'=>'post']) !!}
<ul class="list">
    <li class="list__item list__item--settings">
        Site Name<em class="error">*</em>
        {!! Form::text('site_name') !!}
    </li>
    <li class="list__item list__item--settings">
        Admin Username<em class="error">*</em>
        {!! Form::text('username') !!}
    </li>
    <li class="list__item list__item--settings">
        Admin Email<em class="error">*</em>
        {!! Form::text('email') !!}
    </li>
    <li class="list__item list__item--settings">
        Admin Password<em class="error">*</em>
        {!! Form::text('password') !!}
    </li>
</ul>
<div class="buttons">
    <button class="button button-classic">
        {{ trans('installer_messages.settings.install') }}
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
    </button>
</div>
{!! Form::close() !!}
@stop
