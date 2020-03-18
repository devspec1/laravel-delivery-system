<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Admin Panel</title>
		<link rel="shortcut icon" href="{{ $favicon }}">
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.5 -->
		<link rel="stylesheet" href="{{ url('admin_assets/bootstrap/css/bootstrap.min.css') }}">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{ url('admin_assets/dist/css/AdminLTE.css') }}">

		<link rel="stylesheet" href="{{ url('admin_assets/plugins/login_slider/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ url('admin_assets/plugins/login_slider/style.css') }}">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="hold-transition login-page">
		<div class="flash-container" style="left:15px;">
			@if(Session::has('message') && !Auth::guard('admin')->check())
			<div class="alert {{ Session::get('alert-class') }}" role="alert">
				<a href="#" class="alert-close" data-dismiss="alert">&times;</a>
				{{ Session::get('message') }}
			</div>
			@endif
		</div>
		<div class="login-box">
			<div class="register-container">
				<div class="register">
					<form action="{{ url('admin/authenticate') }}" method="post">
						{!! Form::token() !!}
						<h2>LOGIN TO <span class="red"><strong>{{ $site_name }}</strong></span></h2>
						<div class="login-tabs">
							<ul>
								<li class="btn login_btn {{old('user_type','Admin')=='Admin'?'active':''}}" user="Admin">
									Admin
								</li>
								<li class="btn login_btn {{old('user_type','Admin')=='Dispatcher'?'active':''}}" user="Dispatcher">
									Dispatcher
								</li>
								<li class="btn login_btn {{old('user_type','Admin')=='Company'?'active':''}}" user="Company">
									Company
								</li>
							</ul>
							<div class="tab-content">
								<input type="hidden" name="user_type" class="user_type" value="Admin">
								<div class="form-group">
									<label for="username" label="Username" class="username_label">Username</label>
									<input type="text" id="username" value="" name="username" placeholder="Enter the username">
								</div>
								<div class="form-group">
									<label for="password">Password</label>
									<input type="password" id="password" value="" name="password" placeholder="Enter the password">
								</div>
							</div>
						</div>
						<button type="submit">LOGIN</button>
					</form>
				</div>
			</div>
		</div>
		<!-- /.login-box -->
		<!-- jQuery 2.1.4 -->
		<script src="{{ url('admin_assets/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
		<!-- Bootstrap 3.3.5 -->
		<script src="{{ url('admin_assets/bootstrap/js/bootstrap.min.js') }}"></script>
		<script src="{{ url('admin_assets/plugins/login_slider/scripts.js') }}"></script>
		<script src="{{ url('admin_assets/plugins/login_slider/jquery.backstretch.min.js') }}"></script>
		<script>
		$(document).ready(function() {
			$('.login_btn').click(function() {
				$('.login_btn').removeClass('active')
				$(this).addClass('active')
				login_change();
			});
			login_change();
			function login_change() {
				user = $('.login_btn.active').attr('user')
				$('.user_type').val(user);
				if (user == 'Admin') {
					$('#username').attr('placeholder','Username')
					$('.username_label').text('Username')
					$(".username_label").attr('label','Username')
				}
				else if(user == 'Dispatcher') {
					$('#username').attr('placeholder','Username')
					$('.username_label').text('Username')
					$(".username_label").attr('label','Username')
				}
				else if(user == 'Company') {
					$('#username').attr('placeholder','Email / Mobile Number')
					$('.username_label').text('Email / Mobile Number')
					$(".username_label").attr('label','Email / Mobile Number')
				}
			}
		});
		</script>
		<style type="text/css">
			.login_panel {
				display: none;
			}
			.login_panel.active {
				display: block;
			}
		</style>
	</body>
</html>
