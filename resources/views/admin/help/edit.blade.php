@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="help">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit Help
      </h1>
      <ol class="breadcrumb">
        <li><a href="../dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="../help">Help</a></li>
        <li class="active">Edit</li>
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
              <h3 class="box-title">Edit Help Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/edit_help/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_category" class="col-sm-3 control-label">Category<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('category_id', $category->pluck('name', 'id'), $result->category_id, ['class' => 'form-control', 'id' => 'input_category_id', 'placeholder' => 'Select', 'ng-change' => 'change_category(category_id)', 'ng-model' => 'category_id', 'ng-init' => 'category_id = '.$result->category_id]) !!}
                    <span class="text-danger">{{ $errors->first('category_id') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_subcategory" class="col-sm-3 control-label">Sub Category</label>
                  <div class="col-sm-6">
                    <select class="form-control" id="input_subcategory_id" name="subcategory_id" ng-model="subcategory_id">
                     <option value="">Select</option>
                     <option ng-repeat="item in subcategory" value="@{{ item.id }}">@{{ item.name }}</option>
                    </select>
                    <input type="hidden" id="hidden_subcategory_id" value="{{ $result->subcategory_id }}">
                    <span class="text-danger">{{ $errors->first('subcategory_id') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_question" class="col-sm-3 control-label">Question<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('question', $result->question, ['class' => 'form-control', 'id' => 'input_question', 'placeholder' => 'Question']) !!}
                    <span class="text-danger">{{ $errors->first('question') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_answer" class="col-sm-3 control-label">Answer<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    <textarea id="txtEditor" name="txtEditor"></textarea>
                    {!! Form::textarea('answer', $result->answer, ['id' => 'answer', 'hidden' => 'true']) !!}
                    <span class="text-danger">{{ $errors->first('answer') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_suggested" class="col-sm-3 control-label">Suggested</label>
                  <div class="col-sm-6">
                    {!! Form::radio('suggested', 'yes', ($result->suggested == 'yes') ? true : false) !!} Yes
                    {!! Form::radio('suggested', 'no', ($result->suggested == 'no') ? true : false) !!} No
                    <span class="text-danger">{{ $errors->first('suggested') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                  </div>
                </div>
                <div class="panel" ng-init="translations = {{json_encode(old('translations') ?: $result->translations)}}; removed_translations =  []; errors = {{json_encode($errors->getMessages())}}; result_translations = {{json_encode($result->translations)}}" ng-cloak>
                  <div class="panel-header">
                    <h4 class="box-title text-center">Translations</h4>
                  </div>
                  <div class="panel-body" ng-init="languages = {{json_encode($languages)}}">
                    <input type="hidden" name="removed_translations" ng-value="removed_translations.toString()">
                    <div class="row" ng-repeat="translation in translations">
                      <input type="hidden" name="translations[@{{$index}}][id]" value="@{{translation.id}}">
                      <div class="form-group">
                        <label for="input_language_@{{$index}}" class="col-sm-3 control-label">Language<em class="text-danger">*</em></label>
                        <div class="col-sm-6">
                          <select name="translations[@{{$index}}][locale]" class="form-control " id="input_language_@{{$index}}" ng-model="translation.locale" >
                            <option value="" ng-if="translation.locale == ''">Select Language</option>
                            <option ng-if="!languages.hasOwnProperty(translation.locale) && translation.locale != '';" value="@{{translation.locale}}" >@{{translation.language.name}} </option>
                            @foreach($languages as $key => $value)
                              <option value="{{$key}}" ng-if="(('{{$key}}' | checkKeyValueUsedInStack : 'locale': translations) || '{{$key}}' == translation.locale) && '{{$key}}' != 'en'">{{$value}}</option>
                            @endforeach
                          </select>
                          <span class="text-danger ">@{{ errors['translations.'+$index+'.locale'][0] }}</span>
                        </div>
                        <div class="col-sm-1">
                          <button class="btn btn-danger btn-xs" ng-click="translations.splice($index, 1); removed_translations.push(translation.id)">
                            <i class="fa fa-trash"></i>
                          </button>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="input_name_@{{$index}}" class="col-sm-3 control-label">Question<em class="text-danger">*</em></label>
                        <div class="col-sm-6">
                          {!! Form::text('translations[@{{$index}}][name]', '@{{translation.name}}', ['class' => 'form-control ', 'id' => 'input_name_@{{$index}}', 'placeholder' => 'Name']) !!}
                          <span class="text-danger ">@{{ errors['translations.'+$index+'.name'][0] }}</span>
                        </div>
                      </div>

                      <div class="form-group"  ng-init="multiple_editors($index)">
                        <label for="input_content_@{{$index}}" class="col-sm-3 control-label">Answer<em class="text-danger">*</em></label>
                        <div class="col-sm-6">
                          <textarea class="editors" id="editor_@{{$index}}" name="translations[@{{$index}}][txtEditor]" data-index="@{{$index}}"></textarea>
                          <textarea class="contents " id="content_@{{$index}}" name="translations[@{{$index}}][description]" hidden="true">@{{translation.description}}</textarea>
                         
                          <span class="text-danger ">@{{ errors['translations.'+$index+'.description'][0] }}</span>
                        </div>
                      </div>

                      <legend ng-if="$index+1 < translations.length"></legend>
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="row" ng-show="(translations | checkActiveTranslation: languages).length <  {{count($languages) - 1}}">
                      <div class="col-sm-12">
                        <button type="button" class="btn btn-info" ng-click="translations.push({locale:''});" >
                          <i class="fa fa-plus"></i> Add Translation
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                 <button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
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
<script type="text/javascript">
$("#txtEditor").Editor(); 
$('.Editor-editor').html($('#answer').val());
</script>
@endpush