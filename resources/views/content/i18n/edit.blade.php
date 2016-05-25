@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li>
            <i class="fa fa-flag" style="margin-right: 5px"></i>
            <a href="/i18n">{{ __('I18n') }}</a>
        </li>
        <li class="active">{{ __('Edit I18n') }}</li>
    </ol>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">{{ __('Edit I18n') }}</h3>
            <a href="/i18n" class="btn btn-default pull-right">
                <i class="fa fa-chevron-left" style="margin-right: 5px"></i>
                {{ __('Back') }}
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    {{ Form::model($oI18n, ['route' => ['i18n.edit', $oI18n->id]]) }}
                        @include('errors.validation')
                        <div class="form-group {{ ($errors->first('source_language') ? 'has-error' : '') }}">
                            {{ Form::label('source_language', __('Source Language')) }}
                            {{ Form::select('source_language', $aLanguages, old('source_language'), ['id' => 'source_language', 'class' => 'form-control']) }}
                        </div>
                        <div class="form-group {{ ($errors->first('destination_language') ? 'has-error' : '') }}">
                            {{ Form::label('destination_language', __('Destination Language')) }}
                            {{ Form::select('destination_language', $aLanguages, old('destination_language'), ['id' => 'destination_language', 'class' => 'form-control']) }}
                        </div>
                        <div class="form-group {{ ($errors->first('source_string') ? 'has-error' : '') }}">
                            {{ Form::label('source_string', __('Source String')) }}
                            {{ Form::textarea('source_string', old('source_string'), ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                        <div class="form-group {{ ($errors->first('destination_string') ? 'has-error' : '') }}">
                            {{ Form::label('destination_string', __('Destination String')) }}
                            {{ Form::textarea('destination_string', old('destination_string'), ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::checkbox('website', 1, old('website'), ['id' => 'website', 'style' => 'margin-right: 10px']) }}
                            {{ Form::label('website', 'Website') }}
                        </div>
                        <div class="form-group">
                            {{ Form::checkbox('cms', 1, old('cms'), ['id' => 'cms', 'style' => 'margin-right: 10px']) }}
                            {{ Form::label('cms', 'CMS') }}
                        </div>
                        <div class="form-group">
                            {{ Form::checkbox('javascript', 1, old('javascript'), ['id' => 'javascript', 'style' => 'margin-right: 10px']) }}
                            {{ Form::label('javascript', 'JavaScript') }}
                        </div>
                        <div style="height: 15px"></div>
                        {{ Form::submit(__('Save'), ['class' => 'btn btn-success']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection