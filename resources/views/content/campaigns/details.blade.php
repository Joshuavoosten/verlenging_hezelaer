@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/campaigns/details.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/dealActive.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/dealEndAgreement.js') }}"></script>
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
                    <div class="col-md-6">t/m {{ date('j-n-Y', strtotime($oCampaign->current_expiration_date)) }}</div>
                </div>
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
                @if($oCampaign->isElektricity())
                    <div class="row">
                        <div class="col-md-6">
                            {{ __('Electricity prices') }}<br />
                            <small style="color: #888">(&euro;ct/per kWh)</small>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    {{ __('Normal') }} &euro; {{ number_format($oCampaign->price_normal / 100,4,'.',',') }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    {{ __('Low') }} &euro; {{ number_format($oCampaign->price_low / 100,4,'.',',') }}
                                </div>
                            </div>
                            @if($oCampaign->price_enkel>0)
                                <div class="row">
                                    <div class="col-md-12">
                                        {{ __('Single') }} &euro; {{ number_format($oCampaign->price_enkel / 100,4,'.',',') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if($oCampaign->isGas())
                    <div class="row">
                        <div class="col-md-6">
                            {{ __('Gas price') }}<br />
                            <small style="color: #888">(&euro;ct/per m3)</small>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    {{ __('Normal') }} &euro; {{ number_format($oCampaign->price_normal / 100,4,'.',',') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
                <table id="table_customers_without_saving" class="table table-hover table-striped sortable" data-row-style="rowstyleFormatter" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                    <thead>
                        <tr>
                            <th data-field="active" data-formatter="dealActiveFormatter" data-align="center" data-sortable="true" class="col-md-1"></th>
                            <th data-field="client_name" data-sortable="true">{{ __('Name') }}</th>
                            <th data-field="client_code" data-sortable="true">{{ __('Number') }}</th>
                            <th data-field="code" data-sortable="true" class="col-md-1">{{ __('Profile Code') }}</th>
                            <th data-field="end_agreement" data-formatter="dealEndAgreementFormatter" data-align="center" data-sortable="true" class="col-md-2">{{ __('Expiration Date') }}</th>
                            <th data-field="aanhef_commercieel" data-sortable="true">{{ __('Salutation') }}</th>
                            <th data-field="status_format" data-align="center" data-sortable="true">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                </table>
                <div style="height: 10px"></div>
                <h4>{{ __('Customers with savings') }}</h4>
                <table id="table_customers_with_savings" class="table table-hover table-striped sortable" data-row-style="rowstyleFormatter" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                    <thead>
                        <tr>
                            <th data-field="active" data-formatter="dealActiveFormatter" data-align="center" data-sortable="true" class="col-md-1"></th>
                            <th data-field="client_name" data-sortable="true">{{ __('Name') }}</th>
                            <th data-field="client_code" data-sortable="true">{{ __('Number') }}</th>
                            <th data-field="code" data-sortable="true" class="col-md-1">{{ __('Profile Code') }}</th>
                            <th data-field="end_agreement" data-formatter="dealEndAgreementFormatter" data-align="center" data-sortable="true" class="col-md-2">{{ __('Expiration Date') }}</th>
                            <th data-field="aanhef_commercieel" data-sortable="true">{{ __('Salutation') }}</th>
                            <th data-field="status_format" data-align="center" data-sortable="true">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                </table>
                <div style="height: 10px"></div>
                <h4>{{ __('Customers with a current offer') }}</h4>
                <table id="table_customers_with_current_offer" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
                    <thead>
                        <tr>
                            <th data-field="client_name" data-sortable="true">{{ __('Name') }}</th>
                            <th data-field="client_code" data-sortable="true">{{ __('Number') }}</th>
                            <th data-field="code" data-sortable="true" class="col-md-1">{{ __('Profile Code') }}</th>
                            <th data-field="end_agreement" data-formatter="dealEndAgreementFormatter" data-align="center" data-sortable="true" class="col-md-2">{{ __('Expiration Date') }}</th>
                            <th data-field="aanhef_commercieel" data-sortable="true">{{ __('Salutation') }}</th>
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
                    {{ date('d-m-Y H:i', strtotime($oCampaign->scheduled_at)) }}
                </div>
            </div>
        </div>
    </div>
    <div style="height: 10px"></div>
@endif
@endsection