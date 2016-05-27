@extends('layouts.master')

@section('content')
<ol class="breadcrumb">
    <li>
        <i class="fa fa-user" style="margin-right: 5px"></i>
        {{ Auth::user()->name }}
    </li>
    <li class="active">{{ __('Change Password') }}</li>
</ol>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ __('Change Password') }}</h3>
    </div>
    <div class="panel-body">
        @if (isset($success))
            <p class="bg-success" style="padding: 10px">{{ $success }}</p>
        @endif
        <div class="row">
            <div class="col-md-6">
                {!! Form::open(['url' => '/change-password', 'method' => 'post']) !!}
                    @include('errors.validation')
                    <div class="form-group {{ ($errors->first('current_password') ? 'has-error' : '') }}">
                        {{ Form::label('current_password', __('Current Password')) }}
                        {{ Form::text('current_password', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group {{ ($errors->first('new_password') ? 'has-error' : '') }}">
                        {{ Form::label('new_password', __('New Password')) }}
                        {{ Form::text('new_password', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group {{ ($errors->first('new_password_repeat') ? 'has-error' : '') }}">
                        {{ Form::label('new_password_repeat', __('New Password (repeat)')) }}
                        {{ Form::text('new_password_repeat', null, ['class' => 'form-control']) }}
                    </div>
                    <div style="height: 15px"></div>
                    {{ Form::submit(__('Save'), ['class' => 'btn btn-custom']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection