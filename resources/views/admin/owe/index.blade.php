@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
    Manage {{ $main_title }}
    <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"> {{ $main_title }} </li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage {{ $main_title }} </h3>
            <div style="float:right;">
              @if(isset($total_owe_amount))
              <a class="btn btn-success" href="{{ route('owe_details',['type' => 'overall']) }}"> Total Owe Amount : {{ $currency_code.$total_owe_amount }} </a>
              {{-- <a class="btn btn-success" href="{{ route('owe_details',['type' => 'applied']) }}"> Applied Owe Amount : {{ $currency_code.$applied_owe_amount }} </a> --}}
              <p class="btn btn-warning" style="cursor: default;"> Remaining Owe Amount : {{ $currency_code.$remaining_owe_amount }} </p>
              @endif
            </div> 
          </div>
          @if(isset($total_owe_amount))
          <div class="box-header">
            <h3 class="payment_header_text">
              {{ $sub_title }}
            </h3>
          </div>
          @endif
          <!-- /.box-header -->
          <div class="box-body">
            {!! $dataTable->table() !!}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
@push('scripts')
<style>
  .min_width {
    width: 200px;
    overflow: hidden;
    word-wrap: break-word;
  }
</style>
<script src="{{ url('admin_assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('admin_assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush