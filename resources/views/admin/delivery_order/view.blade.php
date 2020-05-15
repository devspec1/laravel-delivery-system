@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Delivery Orders
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Delivery Orders</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Manage Delivery Orders</h3>
                        @if((LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status == 'Active') || (LOGIN_USER_TYPE=='admin' && Auth::guard('admin')->user()->can('create_driver')))
                            <div style="float:right;"><a class="btn btn-success" href="{{ url(LOGIN_USER_TYPE.'/add_home_delivery') }}">Add Order</a></div>
                        @endif
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
<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<link rel="stylesheet" href="{{ url('css/toastr.min.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
<script src="{{ url('js/toastr.min.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function(){
        Get_Expired_Order();
    });

    function Get_Expired_Order(){
           $.ajax({
               type: 'GET',
               url: APP_URL+'/admin/home_delivery_test',
               async: false,
               dataType: "json",
               success: function(resultData) {
                   console.log(resultData.Order_ID_List.length);
                   for(var i=0;i<resultData.Order_ID_List.length;i++){
                       var toastr_button = "<a type='button' href='"+APP_URL+"/admin/home_delivery_orders/"+resultData.Order_ID_List[i]+"' style='color:lawngreen;font-size:14px; font-weight:bold'>Click here to see more details of Order</a>";
                       toastr.error(toastr_button,'Order '+resultData.Order_ID_List[i]+'has been Expired',  {timeOut: 10000, closeButton: false, tapToDismiss: false, draggable: true});
                   }
                }
           });
       }
       setInterval(Get_Expired_Order, 300000);
</script>
{!! $dataTable->scripts() !!}
@endpush
