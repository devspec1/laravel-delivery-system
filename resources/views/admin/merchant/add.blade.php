@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="driver_management">
	<section class="content-header">
		<h1> Add Driver </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/merchants') }}"> Merchants </a>
			</li>
			<li class="active"> Add </li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Add Merchant Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/add_merchant', 'class' => 'form-horizontal','files' => true]) !!}
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_name" class="col-sm-3 control-label">Merchant Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('name', '', ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name']) !!}
								<span class="text-danger">{{ $errors->first('name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_description" class="col-sm-3 control-label">Description<em class="text-danger">*</em></label>
							<div class="col-sm-6">
                                {!! Form::textarea('description', null, ['class'=>'form-control', 'id' => 'input_description', 'rows' => 2, 'cols' => 40, 'placeholder' => 'Long description']) !!}
								<span class="text-danger">{{ $errors->first('description') }}</span>
							</div>
                        </div>
                        <div class="form-group">
							<label for="input_base_fee" class="col-sm-3 control-label">Base fee<em class="text-danger">*</em></label>
							<div class="col-sm-6">
                                {!! Form::number('base_fee', null, ['class'=>'form-control', 'id' => 'input_base_fee', 'rows' => 2, 'cols' => 40, "step" => "0.01", 'placeholder' => '0.00']) !!}
								<span class="text-danger">{{ $errors->first('base_fee') }}</span>
							</div>
                        </div>
                        <div class="form-group">
							<label for="input_base_distance" class="col-sm-3 control-label">Base distance, KM<em class="text-danger">*</em></label>
							<div class="col-sm-6">
                                {!! Form::number('base_distance', null, ['class'=>'form-control', 'id' => 'input_base_distance', 'rows' => 2, 'cols' => 40, "step" => "0.01", 'placeholder' => '0.00']) !!}
								<span class="text-danger">{{ $errors->first('base_distance') }}</span>
							</div>
                        </div>
                        <div class="form-group">
							<label for="input_surchange_fee" class="col-sm-3 control-label">Surchange fee, per KM<em class="text-danger">*</em></label>
							<div class="col-sm-6">
                                {!! Form::number('surchange_fee', null, ['class'=>'form-control', 'id' => 'input_surchange_fee', 'rows' => 2, 'cols' => 40, "step" => "0.01", 'placeholder' => '0.00']) !!}
								<span class="text-danger">{{ $errors->first('surchange_fee') }}</span>
							</div>
                        </div>
                        <div class="form-group" style="margin-bottom: 1em">
                            <label for="input_integration_type" class="col-sm-3 control-label">Integration Type</label>
                            <div class="col-sm-6">
                                {!! Form::select('integration_type', $integrations, '1', ['class'=>'form-control', 'id' => 'input_integration_type']) !!}
                                <span class="text-danger">{{ $errors->first('integration_type') }}</span>
                           </div>	
                        </div>

                        
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection