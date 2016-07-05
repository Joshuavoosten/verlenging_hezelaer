@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/deals/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/boolean.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/dealDetail.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/rowstyle.js') }}"></script>
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
            <i class="fa fa-paper-plane"></i>
            {{ __('Deals') }}
        </h3>
    </div>
    <div class="panel-body">
        <div class="pull-right">
            <a href="/deals/csv" class="btn btn-default">
                <i class="fa fa-file-text-o" style="margin-right: 5px"></i>
                {{ __('Export CSV') }}
            </a>
        </div>
        <div class="clearfix"></div>
        <table id="table_deals" data-row-style="rowstyleFormatter" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="false" data-show-refresh="true" data-show-columns="true" data-detail-view="true" data-detail-formatter="dealDetailFormatter">
            <thead>
                <tr>
                    <th data-field="campaign" data-sortable="true">{{ __('Campaign') }}</th>
                    <th data-field="client_name" data-sortable="true">{{ __('Client Name') }}</th>
                    <th data-field="client_code" data-sortable="true">{{ __('Client Number') }}</th>
                    <th data-field="email_commercieel" data-sortable="true">{{ __('Mail') }} ({{ __('commercial') }})</th>
                    <th data-field="telnr_commercieel" data-sortable="true">{{ __('Phone') }} ({{ __('commercial') }})</th>
                    <th data-field="aanhef_commercieel" data-sortable="true">{{ __('Salutation') }} ({{ __('commercial') }})</th>
                    <th data-field="fadr_street" data-sortable="true">{{ __('Street') }}</th>
                    <th data-field="fadr_nr" data-sortable="true">{{ __('Address Number') }}</th>
                    <th data-field="fadr_nr_conn" data-sortable="true">{{ __('Address Addition') }}</th>
                    <th data-field="fadr_zip" data-sortable="true">{{ __('Zipcode') }}</th>
                    <th data-field="fadr_city" data-sortable="true">{{ __('City') }}</th>
                    <th data-field="auto_renewal" data-formatter="booleanFormatter" data-align="center" data-sortable="true">{{ __('Auto Renewal') }}</th>
                    <th data-field="accountmanager" data-sortable="true">{{ __('Account Manager') }}</th>
                    <th data-field="klantsegment" data-sortable="true">{{ __('Customer Segment') }}</th>
                    <th data-field="category1" data-sortable="true">{{ __('Category') }} 1</th>
                    <th data-field="category2" data-sortable="true">{{ __('Category') }} 2</th>
                    <th data-field="category3" data-sortable="true">{{ __('Category') }} 3</th>
                    <th data-field="consument" data-formatter="booleanFormatter" data-align="center" data-sortable="true">{{ __('Consumer') }}</th>
                    <th data-field="estimate_total_price_1_year" data-sortable="true" data-align="right">{{ __('Total Estimate Price') }} (1 {{ __('year') }})</th>
                    <th data-field="estimate_total_saving_1_year" data-sortable="true" data-align="right">{{ __('Total Estimate Saving') }} (1 {{ __('year') }})</th>
                    <th data-field="estimate_total_price_2_year" data-sortable="true" data-align="right">{{ __('Total Estimate Price') }} (2 {{ __('year') }})</th>
                    <th data-field="estimate_total_saving_2_year" data-sortable="true" data-align="right">{{ __('Total Estimate Saving') }} (2 {{ __('year') }})</th>
                    <th data-field="estimate_total_price_3_year" data-sortable="true" data-align="right">{{ __('Total Estimate Price') }} (3 {{ __('year') }})</th>
                    <th data-field="estimate_total_saving_3_year" data-sortable="true" data-align="right">{{ __('Total Estimate Saving') }} (3 {{ __('year') }})</th>
                    <th data-field="status_format" data-sortable="true" data-align="right">{{ __('Status') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection