@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/campaigns/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/actions/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/edit.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/campaignDetails.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/campaignCsv.js') }}"></script>
@endsection

@section('content')
{!! csrf_field() !!}
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-refresh"></i>
            {{ __('Campaigns') }}
        </h3>
    </div>
    <div class="panel-body bg-concrete">
        <div class="pull-left">
            <h3>{{ __('Overview') }}</h3>
            <p>Contract verlengingen aangeboden via deze wizard</p>
        </div>
        <div class="pull-right">
            <a href="/campaigns/add" class="btn btn-default">
                <i class="fa fa-plus" style="margin-right: 5px"></i>
                {{ __('Add Campaign') }}
            </a>
        </div>
        <div class="clearfix"></div>
        @include('alerts.success')
        @include('alerts.delete')
        @include('errors.validation')
        <h4><strong>{{ __('Planned') }}</strong> ({{ $iCountPlanned }} {{ __('campaigns') }})</h4>
        <table id="table_campaigns_planned" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="name" data-formatter="campaignDetailsFormatter" data-sortable="true">{{ __('Name') }}</th>
                    <th data-field="current_segment" data-sortable="true">{{ __('Segment') }}</th>
                    <th data-field="current_profile_code" data-sortable="true">{{ __('Profile Code') }}</th>
                    <th data-field="current_agreement" data-sortable="true">{{ __('Agreement') }}</th>
                    <th data-field="current_expiration_date" data-align="center" data-sortable="true">{{ __('Expiration Date') }}</th>
                    <th data-field="count_customers" data-align="center" data-sortable="false">{{ __('Customers') }}</th>
                    <th data-field="planned_at" data-align="center" data-sortable="true">{{ __('Planned') }}</th>
                    <th data-field="csv" data-align="center" data-formatter="campaignCsvFormatter">CSV</th>
                    <th data-field="action_edit" data-sortable="false" data-formatter="editFormatter" data-align="center" class="col-md-1"></th>
                    <th data-field="action_delete" data-sortable="false" data-formatter="deleteFormatter" data-align="center" class="col-md-1"></th>
                </tr>
            </thead>
        </table>
        <h4><strong>{{ __('Sent') }}</strong> ({{ $iCountSent }} {{ __('campaigns') }})</h4>
        <table id="table_campaigns_sent" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="name" data-sortable="true">{{ __('Name') }}</th>
                    <th data-field="current_segment" data-sortable="false">{{ __('Segment') }}</th>
                    <th data-field="current_profile_code" data-sortable="false">{{ __('Profile Code') }}</th>
                    <th data-field="current_agreement" data-sortable="false">{{ __('Agreement') }}</th>
                    <th data-field="current_expiration_date" data-align="center" data-sortable="false">{{ __('Expiration Date') }}</th>
                    <th data-field="count_customers" data-align="center" data-sortable="false">{{ __('Customers') }}</th>
                    <th class="col-md-1"></th>
                    <th data-field="csv" data-align="center" data-formatter="campaignCsvFormatter">CSV</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection