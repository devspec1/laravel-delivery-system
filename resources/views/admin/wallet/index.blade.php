@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
     {{ $main_title }}
    <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Wallet</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"> {{ $sub_title }} </h3>
            <div style="float:right;"><a class="btn btn-success" href="{{ route('add_wallet',['user_type' => $user_type]) }}">Add Wallet </a></div>
          </div>
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
  <script src="{{ url('admin_assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ url('admin_assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
  <link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
  <script src="{{ url('js/dataTables.buttons.js') }}"></script>
  <script src="{{ url('js/buttons.server-side.js') }}"></script>
  {!! $dataTable->scripts() !!}
@endpush