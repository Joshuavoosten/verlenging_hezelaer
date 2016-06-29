@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/contracts/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/boolean.js') }}"></script>
@endsection

@section('stylesheets')
<style>
.dropdown-menu { width: 380px; }
</style>
@endsection

@section('content')
{!! csrf_field() !!}
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-list"></i>
            {{ __('Contracts') }}
            <div class="pull-right">
                 <i class="fa fa-calendar"></i>
                 {{ __('Date Modified') }}:
                 <strong>{{ $sDateModified }}</strong>
            </div>
        </h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="pull-right">
                <a href="/contracts/import" class="btn btn-default" style="margin-right: 15px">
                    <i class="fa fa-file-text-o" style="margin-right: 5px"></i>
                    {{ __('Import CSV') }}
                </a>
            </div>
        </div>
        <div class="clearfix"></div>
        <div style="height: 15px"></div>
        @include('alerts.success')
        <table id="table_contracts" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="false" data-show-refresh="true" data-show-columns="true">
            <thead>
                <tr>
                    <th data-field="client_name" data-sortable="true" class="col-md-2">{{ __('Client Name') }}</th>
                    <th data-field="client_code" data-sortable="true">{{ __('Client Number') }}</th>
                    <th data-field="ean" data-sortable="true">EAN</th>
                    <th data-field="code" data-sortable="true">{{ __('Code') }}</th>
                    <th data-field="super_contract_number" data-sortable="true">{{ __('Super Contract Number') }}</th>
                    <th data-field="syu_normal" data-sortable="true">SYU {{ __('normal') }}</th>
                    <th data-field="syu_low" data-sortable="true">SYU {{ __('low') }}</th>
                    <th data-field="end_agreement" data-sortable="true" data-align="center">{{ __('End Agreement') }}</th>
                    <th data-field="email_commercieel" data-sortable="true">{{ __('Mail') }} ({{ __('commercial') }})</th>
                    <th data-field="telnr_commercieel" data-sortable="true">{{ __('Phone') }} ({{ __('commercial') }})</th>
                    <th data-field="aanhef_commercieel" data-sortable="true">{{ __('Salutation') }} ({{ __('commercial') }})</th>
                    <th data-field="fadr_street" data-sortable="true">{{ __('Street') }} (F)</th>
                    <th data-field="fadr_nr" data-sortable="true">{{ __('Address Number') }} (F)</th>
                    <th data-field="fadr_nr_conn" data-sortable="true">{{ __('Address Addition') }} (F)</th>
                    <th data-field="fadr_zip" data-sortable="true">{{ __('Zipcode') }} (F)</th>
                    <th data-field="fadr_city" data-sortable="true">{{ __('City') }} (F)</th>
                    <th data-field="cadr_street" data-sortable="true">{{ __('Street') }} (C)</th>
                    <th data-field="cadr_nr" data-sortable="true">{{ __('Address Number') }} (C)</th>
                    <th data-field="cadr_nr_conn" data-sortable="true">{{ __('Address Addition') }} (C)</th>
                    <th data-field="cadr_zip" data-sortable="true">{{ __('Zipcode') }} (C)</th>
                    <th data-field="cadr_city" data-sortable="true">{{ __('City') }} (C)</th>
                    <th data-field="vastrecht" data-sortable="true" data-align="right">{{ __('Standing Charge') }}</th>
                    <th data-field="auto_renewal" data-formatter="booleanFormatter" data-align="center" data-sortable="true">{{ __('Auto Renewal') }}</th>
                    <th data-field="accountmanager" data-sortable="true">{{ __('Account Manager') }}</th>
                    <th data-field="klantsegment" data-sortable="true">{{ __('Customer Segment') }}</th>
                    <th data-field="category1" data-sortable="true">{{ __('Category') }} 1</th>
                    <th data-field="category2" data-sortable="true">{{ __('Category') }} 2</th>
                    <th data-field="category3" data-sortable="true">{{ __('Category') }} 3</th>
                    <th data-field="consument" data-formatter="booleanFormatter" data-align="center" data-sortable="true">{{ __('Consumer') }}</th>
                    <th data-field="price_normal" data-sortable="true" data-align="right">{{ __('Price').' '.__('normal') }}</th>
                    <th data-field="price_low" data-sortable="true" data-align="right">{{ __('Price').' '.__('low') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection