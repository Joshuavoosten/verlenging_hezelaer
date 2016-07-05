@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/campaigns/add.js') }}"></script>
@endsection

@section('stylesheets')
<style>
#content_current_under_an_agent {
    display: none;
}
</style>
@endsection

@section('content')
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
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('current_label') ? 'has-error' : '') }}">
                        {{ Form::label('current_label', __('Label')) }}
                        {{ Form::select('current_label', $aCurrentLabels, old('current_label'), ['id' => 'current_label', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('current_auto_renewal') ? 'has-error' : '') }}">
                        {{ Form::label('current_auto_renewal', __('Auto renewal')) }}
                        {{ Form::select('current_auto_renewal', $aCurrentAutoRenewals, old('current_auto_renewal'), ['id' => 'current_auto_renewal', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('current_holding') ? 'has-error' : '') }}">
                        {{ Form::label('current_holding', __('Holding')) }}
                        {{ Form::select('current_holding', $aCurrentHoldings, old('current_holding'), ['id' => 'current_holding', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('current_segment') ? 'has-error' : '') }}">
                        {{ Form::label('current_segment', __('Segment')) }}
                        {{ Form::select('current_segment', $aCurrentSegments, old('current_segment'), ['id' => 'current_segment', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('current_under_an_agent') ? 'has-error' : '') }}">
                        {{ Form::label('current_under_an_agent', __('Under an agent')) }}
                        {{ Form::select('current_under_an_agent', $aCurrentUnderAnAgent, old('current_under_an_agent'), ['id' => 'current_under_an_agent', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                        <div id="content_current_agents">
                            <div style="height: 10px"></div>
                            {{ Form::select('current_agents[]', $aCurrentAgents, $aData['current_agents'], ['id' => 'current_agents', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options'), 'multiple']) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('current_in_a_group') ? 'has-error' : '') }}">
                        {{ Form::label('current_in_a_group', __('In a group')) }}
                        {{ Form::select('current_in_a_group', $aCurrentInAGroup, old('current_in_a_group'), ['id' => 'current_in_a_group', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
            </div>
            <div class="row">
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
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('new_percentage') ? 'has-error' : '') }}">
                        {{ Form::label('new_percentage', __('Prijs opslag percentage')) }}
                        {{ Form::select('new_percentage', $aNewPercentages, old('new_percentage'), ['id' => 'new_percentage', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group {{ ($errors->first('new_percentage_after_term_offer') ? 'has-error' : '') }}">
                        {{ Form::label('new_percentage_after_term_offer', __('Prijs opslag percentage na looptijd aanbieding')) }}
                        {{ Form::select('new_percentage_after_term_offer', $aNewPercentages, old('new_percentage_after_term_offer'), ['id' => 'new_percentage_after_term_offer', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
            </div>
            <div style="height: 15px"></div>
            {{ Form::submit(__('Add Campaign'), ['class' => 'btn btn-custom']) }}
        {!! Form::close() !!}
    </div>
</div>
@endsection