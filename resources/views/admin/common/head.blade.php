<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> {{ ucfirst(LOGIN_USER_TYPE) }}  Panel</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="{{ $favicon }}">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ url('admin_assets/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('admin_assets/dist/css/bootstrap-datetimepicker.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('admin_assets/dist/css/AdminLTE.css') }}">
  <link rel="stylesheet" href="{{ url('css/selectize.default.css') }}">
  <link rel="stylesheet" href="{{ url('admin_assets/plugins/datatables/dataTables.bootstrap.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
  folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ url('admin_assets/dist/css/skins/_all-skins.css') }}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{{ url('admin_assets/plugins/morris/morris.css') }}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{ url('admin_assets/plugins/datepicker/bootstrap-datepicker3.css') }}">
  <!-- text editor -->
  <link rel="stylesheet" href="{{ url('admin_assets/plugins/editor/editor.css') }}">

  <link rel="stylesheet" href="{{ url('admin_assets/plugins/jQueryUI/jquery-ui.css') }}">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="{{ url('admin_assets/dist/css/bootstrap-select.min.css') }}">

  @if (!isset($exception) && Route::current()->uri() == 'admin/referral_settings')
    <link href="{{ url('admin_assets/bootstrap/css/bootstrap-toggle.min.css') }}" rel="stylesheet">
  @endif

</head>
<body class="skin-purple hold-transition sidebar-mini" ng-app="App">
  <div class="wrapper">