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
                    <h4>{{ __('Current Contracts') }}</h4>
                    <div class="alert alert-warning">
                        De filters worden nog niet toegepast!
                    </div> 
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('current_segment') ? 'has-error' : '') }}">
                                {{ Form::label('current_segment', __('Segment')) }}
                                {{ Form::select('current_segment', $aCurrentSegments, old('current_segment'), ['id' => 'current_segment', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('current_in_a_group') ? 'has-error' : '') }}">
                                {{ Form::label('in_a_group', __('In a group')) }}
                                <br />@todo
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('current_under_an_agent') ? 'has-error' : '') }}">
                                {{ Form::label('under an agent', __('Under an agent')) }}
                                <br />@todo
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('current_profile_code') ? 'has-error' : '') }}">
                                {{ Form::label('current_profile_code', __('Profile Code')) }}
                                {{ Form::select('current_profile_code', $aCurrentProfileCodes, old('current_profile_codes'), ['id' => 'current_profile_codes', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('current_agreement') ? 'has-error' : '') }}">
                                {{ Form::label('current_agreement', __('Agreement')) }}
                                {{ Form::select('current_agreement', $aCurrentAgreements, old('current_agreement'), ['id' => 'current_agreement', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('current_expiration_date') ? 'has-error' : '') }}">
                                {{ Form::label('current_expiration_date', __('Expiration Date')) }}
                                {{ Form::select('current_expiration_date', $aCurrentExpirationDate, old('current_expiration_date'), ['id' => 'current_expiration_date', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                    </div>
                    <h4>{{ __('New Contract Offer') }}</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('new_agreement') ? 'has-error' : '') }}">
                                {{ Form::label('new_agreement', __('Agreement')) }}
                                {{ Form::select('new_agreement', $aNewAgreements, old('new_agreement'), ['id' => 'new_agreement', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('new_term_offer') ? 'has-error' : '') }}">
                                {{ Form::label('new_term_offer', __('Term Offer')) }}
                                {{ Form::select('new_term_offer', $aNewTermOffers, old('new_term_offer'), ['id' => 'new_term_offer', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ ($errors->first('new_percentage') ? 'has-error' : '') }}">
                                {{ Form::label('new_percentage', __('Prijs opslag percentage')) }}
                                {{ Form::select('new_percentage', $aNewPercentages, old('new_percentage'), ['id' => 'new_percentage', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                            </div>
                        </div>
                    </div>
                    <div style="height: 15px"></div>
                    {{ Form::submit(__('Add Campaign'), ['class' => 'btn btn-success']) }}
                {!! Form::close() !!}
            </div>
    </div>
</div>
@endsection