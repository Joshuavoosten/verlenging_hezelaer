@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li>
            <i class="fa fa-refresh" style="margin-right: 5px"></i>
            <a href="/campaigns">{{ __('Campaigns') }}</a>
        </li>
        <li class="active">{{ $oCampaign->name }}</li>
    </ol>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">{{ $oCampaign->name }}</h3>
            <a href="/campaigns" class="btn btn-default pull-right">
                <i class="fa fa-chevron-left" style="margin-right: 5px"></i>
                {{ __('Back') }}
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>{{ __('Current Contract(s)') }}</strong>
                    <div class="row">
                        <div class="col-md-6">{{ __('Segment') }}</div>
                        <div class="col-md-6">{{ $oCampaign->current_segment }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">{{ __('Profile Code') }}</div>
                        <div class="col-md-6">{{ $oCampaign->current_profile_code }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">{{ __('Agreement') }}</div>
                        <div class="col-md-6">{{ $oCampaign->current_agreement }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">{{ __('Expiration Date') }}</div>
                        <div class="col-md-6">t/m {{ date('d-m-Y', strtotime($oCampaign->current_expiration_date)) }}</div>
                    </div>
                    <div style="height: 15px"></div>
                    <a href="/campaigns/edit/{{ $oCampaign->id }}" class="btn btn-default">
                        {{ __('Edit Campaign') }}
                    </a>
                </div>
                <div class="col-md-6">
                    <strong>{{ __('Offer Contract Extension') }}</strong>
                    <div class="row">
                        <div class="col-md-6">{{ __('Agreement') }}</div>
                        <div class="col-md-6">{{ $oCampaign->new_agreement }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">{{ __('Term Offer') }}</div>
                        <div class="col-md-6">{{ $oCampaign->new_term_offer }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{ __('Electricity prices') }}<br />
                            <small>(&euro;ct/per kWh)</small>
                        </div>
                        <div class="col-md-6">@todo</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{ __('Gas price') }}<br />
                            <small>(&euro;ct/per m3)</small>
                        </div>
                        <div class="col-md-6">@todo</div>
                    </div>
                </div>
            </div>
            <div style="height: 15px"></div>
            <h4>{{ __('Customer List') }} ({{ $iCountCustomers }} klanten)</h4>
            <table id="table_customers" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                <thead>
                    <tr>
                        <th data-field="name" data-sortable="true">{{ __('Name') }}</th>
                        <th data-field="number" data-sortable="true">{{ __('Number') }}</th>
                        <th data-field="code" data-sortable="true">{{ __('Profile Code') }}</th>
                        <th data-field="date" data-sortable="true">{{ __('Expiration Date') }}</th>
                        <th data-field="contact" data-sortable="true">{{ __('Contact') }}</th>
                    </tr>
                </thead>
            </table>
            <div style="height: 15px"></div>
            <h4>{{ __('Schedule Campaign') }}</h4>
            <div class="row">
                <div class="col-md-1">
                    {{ Form::radio('schedule', 'now') }}
                </div>
                <div class="col-md-11">
                    {{ Form::label('schedule', __('Send the campaign immediately')) }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    {{ Form::radio('schedule', 'planned') }}
                </div>
                <div class="col-md-11">
                    {{ Form::label('schedule', __('Schedule the campaign at ...')) }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-11">
                    @todo
                </div>
            </div>
            <hr />
            {{ Form::submit(__('Campaign confirm and finalize'), ['class' => 'btn btn-success']) }}
        </div>
    </div>
</div>
@endsection