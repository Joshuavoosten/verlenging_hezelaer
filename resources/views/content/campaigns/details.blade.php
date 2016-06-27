@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/campaigns/details.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/campaignCustomerActive.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/campaignPrice.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/dealEndAgreement.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/profileCodes.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/rowstyle.js') }}"></script>
@endsection

@section('content')
{{ Form::hidden('campaign_id', $oCampaign->id) }}
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
                <h4>{{ __('Current Contract(s)') }}</h4>
                <div class="row">
                    <div class="col-md-6">{{ __('Label') }}</div>
                    <div class="col-md-6">{{ $oCampaign->current_label }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Auto renewal') }}</div>
                    <div class="col-md-6">{{ $oCampaign->currentAutoRenewalFormatter() }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Holding') }}</div>
                    <div class="col-md-6">{{ $oCampaign->currentHoldingFormatter() }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Segment') }}</div>
                    <div class="col-md-6">{{ $oCampaign->currentSegementFormatter() }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Under an agent') }}</div>
                    <div class="col-md-6">
                        {{ $oCampaign->currentUnderAnAgentFormatter() }}
                        @if($oCampaign->current_under_an_agent)
                            <div style="height: 5px"></div>
                            @foreach(explode(',', $oCampaign->current_under_an_agent) as $sAgent)
                                <span class="label label-custom">{{ $sAgent }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('In a group') }}</div>
                    <div class="col-md-6">{{ $oCampaign->currentCurrentInAGroupFormatter() }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Agreement') }}</div>
                    <div class="col-md-6">{{ $oCampaign->current_agreement }}</div>
                </div>
                @if($oCampaign->current_agreement == 'Vast contract')
                    <div class="row">
                        <div class="col-md-6">{{ __('Expiration Date') }}</div>
                        <div class="col-md-6">t/m {{ date(Auth::user()->date_format, strtotime($oCampaign->current_expiration_date)) }}</div>
                    </div>
                @endif
                @if($oCampaign->status == \App\Models\Campaign::STATUS_PLANNED)
                    <div style="height: 15px"></div>
                    <a href="/campaigns/edit/{{ $oCampaign->id }}" class="btn btn-default">
                        {{ __('Edit Campaign') }}
                    </a>
                @endif
            </div>
            <div class="col-md-6">
                <h4>{{ __('Offer Contract Extension') }}</h4>
                <div class="row">
                    <div class="col-md-6">{{ __('Agreement') }}</div>
                    <div class="col-md-6">{{ $oCampaign->new_agreement }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Term Offer') }}</div>
                    <div class="col-md-6">{{ \App\Models\Campaign::newTermOfferFormatter($oCampaign->new_term_offer) }}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">{{ __('Prijs opslag percentage') }}</div>
                    <div class="col-md-6">{{ $oCampaign->new_percentage }} %</div>
                </div>
                <div style="height: 15px"></div>
                <div class"row">
                    <table id="table_campaign_prices" class="table table-hover table-striped sortable" data-row-style="rowstyleFormatter" data-side-pagination="server" data-pagination="true" data-page-size="3" data-page-list="[3, 25, 100]" data-search="false">
                        <thead>
                            <tr>
                                <th data-field="date_start" data-sortable="true" class="col-md-1">{{ __('Date Start') }}</th>
                                <th data-field="date_end" data-sortable="true" class="col-md-1">{{ __('Date End') }}</th>
                                <th data-field="rate" data-sortable="true" class="col-md-1">{{ __('Rate') }}</th>
                                <th data-field="code" data-sortable="true">{{ __('Profile Code') }}</th>
                                <th data-field="price_normal" data-formatter="campaignPriceFormatter" data-sortable="true" class="col-md-1">{{ __('Normal') }}</th>
                                <th data-field="price_low" data-formatter="campaignPriceFormatter" data-sortable="true" class="col-md-1">{{ __('Low') }}</th>
                                <th data-field="price_enkel" data-formatter="campaignPriceFormatter" data-sortable="true" class="col-md-1">{{ __('Single') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ __('Customer List') }} ({{ $oCampaign->countCustomers() }} {{ __('customers') }})</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4>{{ __('Customers without saving') }}</h4>
                <div style="height: 10px"></div>
                @if($oCampaign->status == \App\Models\Campaign::STATUS_PLANNED)
                    <button type="button" class="button-toggle btn btn-default" data-campaign-id="{{ $oCampaign->id }}" data-has-saving="0" data-active="1">
                        <span class="glyphicon glyphicon glyphicon-ok" aria-hidden="true"></span>
                        {{ __('Enable') }}
                    </button>
                    <button type="button" class="button-toggle btn btn-default" data-campaign-id="{{ $oCampaign->id }}" data-has-saving="0" data-active="0" style="margin-left: 10px">
                        <span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span>
                        {{ __('Disable') }}
                    </button>
                @endif
                <table id="table_customers_without_saving" class="table table-hover table-striped sortable" data-row-style="rowstyleFormatter" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                    <thead>
                        <tr>
                            <th data-field="active" data-formatter="campaignCustomerActiveFormatter" data-align="center" data-sortable="true" class="col-md-1"></th>
                            <th data-field="client_name" data-sortable="true">{{ __('Name') }}</th>
                            <th data-field="client_code" data-sortable="true" class="col-md-1">{{ __('Number') }}</th>
                            <th data-field="codes" data-formatter="profileCodesFormatter" data-sortable="false" class="col-md-1">{{ __('Profile Code(s)') }}</th>
                            <th data-field="end_agreement" data-formatter="dealEndAgreementFormatter" data-align="center" data-sortable="false" class="col-md-1">{{ __('Expiration Date') }}</th>
                            <th data-field="aanhef_commercieel" data-sortable="true" class="col-md-2">{{ __('Salutation') }}</th>
                            <th data-field="status_format" data-align="center" data-sortable="true" class="col-md-1">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                </table>
                <div style="height: 10px"></div>
                <h4>{{ __('Customers with savings') }}</h4>
                @if($oCampaign->status == \App\Models\Campaign::STATUS_PLANNED)
                    <button type="button" class="button-toggle btn btn-default" data-campaign-id="{{ $oCampaign->id }}" data-has-saving="1" data-active="1">
                        <span class="glyphicon glyphicon glyphicon-ok" aria-hidden="true"></span>
                        {{ __('Enable') }}
                    </button>
                    <button type="button" class="button-toggle btn btn-default" data-campaign-id="{{ $oCampaign->id }}" data-has-saving="1" data-active="0" style="margin-left: 10px">
                        <span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span>
                        {{ __('Disable') }}
                    </button>
                @endif
                <table id="table_customers_with_savings" class="table table-hover table-striped sortable" data-row-style="rowstyleFormatter" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                    <thead>
                        <tr>
                            <th data-field="active" data-formatter="campaignCustomerActiveFormatter" data-align="center" data-sortable="true" class="col-md-1"></th>
                            <th data-field="client_name" data-sortable="true">{{ __('Name') }}</th>
                            <th data-field="client_code" data-sortable="true" class="col-md-1">{{ __('Number') }}</th>
                            <th data-field="codes" data-formatter="profileCodesFormatter" data-sortable="false" class="col-md-1">{{ __('Profile Code(s)') }}</th>
                            <th data-field="end_agreement" data-formatter="dealEndAgreementFormatter" data-align="center" data-sortable="false" class="col-md-1">{{ __('Expiration Date') }}</th>
                            <th data-field="aanhef_commercieel" data-sortable="true" class="col-md-2">{{ __('Salutation') }}</th>
                            <th data-field="status_format" data-align="center" data-sortable="true" class="col-md-1">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                </table>
                <div style="height: 10px"></div>
                <h4>{{ __('Customers with a current offer') }}</h4>
                <table id="table_customers_with_current_offer" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                    <thead>
                        <tr>
                            <th class="col-md-1"></th>
                            <th data-field="client_name" data-sortable="true">{{ __('Name') }}</th>
                            <th data-field="client_code" data-sortable="true" class="col-md-1">{{ __('Number') }}</th>
                            <th data-field="codes" data-formatter="profileCodesFormatter" data-sortable="false" class="col-md-1">{{ __('Profile Code(s)') }}</th>
                            <th data-field="end_agreement" data-formatter="dealEndAgreementFormatter" data-align="center" data-sortable="false" class="col-md-1">{{ __('Expiration Date') }}</th>
                            <th data-field="aanhef_commercieel" data-sortable="true" class="col-md-2">{{ __('Salutation') }}</th>
                            <th class="col-md-1"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@if($oCampaign->status == \App\Models\Campaign::STATUS_PLANNED)
    {{ Form::open(['url' => '/campaigns/details/'.$oCampaign->id, 'method' => 'post']) }}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Schedule Campaign') }}</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-1 text-center">
                        {{ Form::radio('schedule', 'now', (!$oCampaign->scheduled)) }}
                    </div>
                    <div class="col-md-11">
                        {{ Form::label('schedule', __('Send the campaign immediately')) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1 text-center">
                        {{ Form::radio('schedule', 'planned', ($oCampaign->scheduled)) }}
                    </div>
                    <div class="col-md-11">
                        {{ Form::label('schedule', __('Schedule the campaign at ...')) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-1">{{ __('Day') }}</div>
                            <div class="col-md-2">{{ __('Month') }}</div>
                            <div class="col-md-1">{{ __('Year') }}</div>
                            <div class="col-md-1">{{ __('Hour') }}</div>
                            <div class="col-md-1">{{ __('Minute') }}</div>
                        </div>
                        <div style="height: 10px"></div>
                        <div class="row">
                            <div class="col-md-1">
                                {{ Form::selectRange('day', 1, 31, $iScheduleDay) }}
                            </div>
                            <div class="col-md-2">
                                {{ Form::selectMonth('month', $iScheduleMonth) }}
                            </div>
                            <div class="col-md-1">
                                {{ Form::selectYear('year', date('Y'), date('Y') + 1), $iScheduleYear }}
                            </div>
                            <div class="col-md-1">
                                {{ Form::select('hour', $aHours, $sScheduleHour) }}
                            </div>
                            <div class="col-md-1">
                                {{ Form::select('minute', $aMinutes, $sScheduleMinute) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="height: 10px"></div>
        {{ Form::submit(__('Save'), ['class' => 'btn btn-custom']) }}
    {!! Form::close() !!}
@else
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Campaign Sent') }}</h3>
        </div>
        <div class="panel-body bg-concrete">
            <div class="row">
                <div class="col-md-1">
                    <i class="fa fa-calendar" style="margin-right: 5px"></i>
                    {{ __('Date') }}
                </div>
                <div class="col-md-11">
                    {{ date(Auth::user()->date_format.' H:i', strtotime($oCampaign->scheduled_at)) }}
                </div>
            </div>
        </div>
    </div>
    <div style="height: 10px"></div>
@endif
@endsection