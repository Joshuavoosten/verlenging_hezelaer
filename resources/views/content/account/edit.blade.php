@extends('layouts.master')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ __('My Account') }}</h3>
    </div>
    <div class="panel-body">
        @include('alerts.success')
        <div class="row">
            <div class="col-md-6">
                {{ Form::model($oUser, ['route' => ['account.edit', $oUser->id]]) }}
                    @include('errors.validation')
                    <div class="form-group {{ ($errors->first('name') ? 'has-error' : '') }}">
                        {{ Form::label('name', __('Name')) }}
                        {{ Form::text('name', old('name'), ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group {{ ($errors->first('gender') ? 'has-error' : '') }}">
                        {{ Form::label('gender', __('Gender')) }}
                        {{ Form::select('gender', $aGenders, old('gender'), ['id' => 'gender', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                    <div class="form-group {{ ($errors->first('email') ? 'has-error' : '') }}">
                        {{ Form::label('email', __('Email')) }}
                        {{ Form::text('email', old('email'), ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group {{ ($errors->first('password') ? 'has-error' : '') }}">
                        {{ Form::label('password', __('Password')) }}
                        {{ Form::password('password', ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group {{ ($errors->first('date_format') ? 'has-error' : '') }}">
                        {{ Form::label('date_format', __('Date Format')) }}
                        {{ Form::select('date_format', $aDateFormats, old('date_format'), ['id' => 'date_format', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                    <div class="form-group {{ ($errors->first('language') ? 'has-error' : '') }}">
                        {{ Form::label('language_id', __('Language')) }}
                        {{ Form::select('language_id', $aLanguages, old('language_id'), ['id' => 'language_id', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                    <div style="height: 15px"></div>
                    {{ Form::submit(__('Save'), ['class' => 'btn btn-custom']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection