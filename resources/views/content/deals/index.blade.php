@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/deals/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/rowstyle.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/syu.js') }}"></script>
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
        <table id="table_deals" data-row-style="rowstyleFormatter" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="false" data-show-refresh="true" data-show-columns="true">
            <thead>
                <tr>
                    <th data-field="campaign" data-sortable="true" data-visible="false">{{ __('Campaign') }}</th>
                    <th data-field="client_name" data-sortable="true">client_name</th>
                    <th data-field="client_code" data-sortable="true">client_code</th>
                    <th data-field="ean" data-sortable="true">ean</th>
                    <th data-field="code" data-sortable="true">code</th>
                    <th data-field="super_contract_number" data-sortable="true">super_contract_number</th>
                    <th data-field="syu_normal" data-formatter="syuFormatter" data-sortable="true">syu_normal</th>
                    <th data-field="syu_low" data-formatter="syuFormatter" data-sortable="true">syu_low</th>
                    <th data-field="end_agreement" data-sortable="true" data-align="center">end_agreement</th>
                    <th data-field="email_commercieel" data-sortable="true">email_commercieel</th>
                    <th data-field="telnr_commercieel" data-sortable="true">telnr_commercieel</th>
                    <th data-field="aanhef_commercieel" data-sortable="true">aanhef_commercieel</th>
                    <th data-field="fadr_street" data-sortable="true">fadr_street</th>
                    <th data-field="fadr_nr" data-sortable="true">fadr_nr</th>
                    <th data-field="fadr_nr_conn" data-sortable="true">fadr_nr_conn</th>
                    <th data-field="fadr_zip" data-sortable="true">fadr_zip</th>
                    <th data-field="fadr_city" data-sortable="true">fadr_city</th>
                    <th data-field="cadr_street" data-sortable="true">cadr_street</th>
                    <th data-field="cadr_nr" data-sortable="true">cadr_nr</th>
                    <th data-field="cadr_nr_conn" data-sortable="true">cadr_nr_conn</th>
                    <th data-field="cadr_zip" data-sortable="true">cadr_zip</th>
                    <th data-field="cadr_city" data-sortable="true">cadr_city</th>
                    <th data-field="vastrecht" data-sortable="true" data-align="right">vastrecht</th>
                    <th data-field="new_vastrecht" data-sortable="true" data-align="right" data-visible="false">new_vastrecht</th>
                    <th data-field="auto_renewal" data-sortable="true">auto_renewal</th>
                    <th data-field="accountmanager" data-sortable="true">accountmanager</th>
                    <th data-field="klantsegment" data-sortable="true">klantsegment</th>
                    <th data-field="category1" data-sortable="true">category1</th>
                    <th data-field="category2" data-sortable="true">category2</th>
                    <th data-field="category3" data-sortable="true">category3</th>
                    <th data-field="consument" data-sortable="true">consument</th>
                    <th data-field="price_normal" data-sortable="true" data-align="right">price_normal</th>
                    <th data-field="price_low" data-sortable="true" data-align="right">price_low</th>
                    <th data-field="estimate_price_1_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Price (1 year)') }}</th>
                    <th data-field="estimate_saving_1_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Saving (1 year)') }}</th>
                    <th data-field="estimate_price_2_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Price (2 years)') }}</th>
                    <th data-field="estimate_saving_2_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Saving (2 years)') }}</th>
                    <th data-field="estimate_price_3_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Price (3 years)') }}</th>
                    <th data-field="estimate_saving_3_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Saving (3 years)') }}</th>
                    <th data-field="estimate_total_price_1_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Total Price (1 year)') }}</th>
                    <th data-field="estimate_total_saving_1_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Total Saving (1 year)') }}</th>
                    <th data-field="estimate_total_price_2_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Total Price (2 years)') }}</th>
                    <th data-field="estimate_total_saving_2_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Total Saving (2 years)') }}</th>
                    <th data-field="estimate_total_price_3_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Total Price (3 years)') }}</th>
                    <th data-field="estimate_total_saving_3_year" data-sortable="true" data-align="right" data-visible="false">{{ __('Estimate Total Saving (3 years)') }}</th>
                    <th data-field="status_format" data-sortable="true" data-align="right" data-visible="false">{{ __('Status') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection