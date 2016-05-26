@extends('layouts.master')

@section('content')
<ol class="breadcrumb">
    <li>
        <i class="fa fa-refresh" style="margin-right: 5px"></i>
        <a href="/campaigns">{{ __('Campaigns') }}</a>
    </li>
    <li class="active">{{ __('Edit Campaign') }}</li>
</ol>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title pull-left">{{ __('Edit Campaign') }}</h3>
        <a href="/campaigns" class="btn btn-default pull-right">
            <i class="fa fa-chevron-left" style="margin-right: 5px"></i>
            {{ __('Back') }}
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body bg-concrete">
        <div class="row">
            <div class="col-md-6">
                {{ Form::model($oCampaign, ['route' => ['campaigns.edit', $oCampaign->id]]) }}
                    @include('errors.validation')
                    <div class="form-group {{ ($errors->first('name') ? 'has-error' : '') }}">
                        {{ Form::label('name', __('Campaign Name')) }}
                        {{ Form::text('name', old('name'), ['class' => 'form-control']) }}
                    </div>
                    <div style="height: 15px"></div>
                    {{ Form::submit(__('Edit Campaign'), ['class' => 'btn btn-custom']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection