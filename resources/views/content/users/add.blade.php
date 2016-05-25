@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li>
            <i class="fa fa-tv" style="margin-right: 5px"></i>
            <a href="/users">{{ __('Users') }}</a>
        </li>
        <li class="active">{{ __('Add User') }}</li>
    </ol>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">{{ __('Add User') }}</h3>
            <a href="/users" class="btn btn-default pull-right">
                <i class="fa fa-chevron-left" style="margin-right: 5px"></i>
                {{ __('Back') }}
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    {!! Form::open(['url' => '/users/add', 'method' => 'post']) !!}
                        @include('errors.validation')
                        <div class="form-group {{ ($errors->first('name') ? 'has-error' : '') }}">
                            {{ Form::label('name', __('Name')) }}
                            {{ Form::text('name', old('name'), ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group {{ ($errors->first('email') ? 'has-error' : '') }}">
                            {{ Form::label('email', __('Email')) }}
                            {{ Form::text('email', old('email'), ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group {{ ($errors->first('password') ? 'has-error' : '') }}">
                            {{ Form::label('password', __('Password')) }}
                            {{ Form::password('password', ['class' => 'form-control']) }}
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