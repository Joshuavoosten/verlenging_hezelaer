@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li>
            <i class="fa fa-refresh" style="margin-right: 5px"></i>
            <a href="/campaigns">{{ __('Campaigns') }}</a>
        </li>
        <li class="active">{{ __('Add Campaign') }}</li>
    </ol>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">{{ __('Add Campaign') }}</h3>
            <a href="/campaigns" class="btn btn-default pull-right">
                <i class="fa fa-chevron-left" style="margin-right: 5px"></i>
                {{ __('Back') }}
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
                {!! Form::open(['url' => '/campaigns/add', 'method' => 'post']) !!}
                    @include('errors.validation')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ ($errors->first('name') ? 'has-error' : '') }}">
                                {{ Form::label('name', __('Campaign Name')) }}
                                {{ Form::text('name', old('name'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                    <hr />
                    <h4>Huidige contract(en)</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('segment') ? 'has-error' : '') }}">
                                {{ Form::label('segment', __('Segment')) }}
                                {{ Form::select('segment', $aSegments, old('segment'), ['id' => 'segment', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('in_a_group') ? 'has-error' : '') }}">
                                {{ Form::label('in_a_group', __('In a group')) }}
                                <br />@todo
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('under an agent') ? 'has-error' : '') }}">
                                {{ Form::label('under an agent', __('Under an agent')) }}
                                <br />@todo
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('code') ? 'has-error' : '') }}">
                                {{ Form::label('code', __('Profile Code')) }}
                                <br />@todo
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('similarities') ? 'has-error' : '') }}">
                                {{ Form::label('code', __('Similarities')) }}
                                <br />@todo
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('expiry_date') ? 'has-error' : '') }}">
                                {{ Form::label('expiry_date', __('Expiry Date')) }}
                                <br />@todo
                            </div>
                        </div>
                    </div>
                    <h4>Nieuw contract aanbod</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('agreement') ? 'has-error' : '') }}">
                                {{ Form::label('agreement', __('Agreement')) }}
                                <br />@todo
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('term_offer') ? 'has-error' : '') }}">
                                {{ Form::label('term_offer', __('Term Offer')) }}
                                <br />@todo
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('percentage') ? 'has-error' : '') }}">
                                {{ Form::label('percentage', __('Prijs opslag percentage')) }}
                                <br />@todo
                            </div>
                        </div>
                    </div>
                    {{ Form::submit(__('Creating Customer List'), ['class' => 'btn btn-success']) }}
                {!! Form::close() !!}
            </div>
    </div>
</div>
@endsection