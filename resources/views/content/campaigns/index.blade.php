@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/campaigns/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/actions/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/edit.js') }}"></script>
@endsection

@section('content')
{!! csrf_field() !!}
<div class="panel panel-primary">
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
            <a href="/campaigns/add" class="btn btn-success">
                <i class="fa fa-plus" style="margin-right: 5px"></i>
                {{ __('Add Campaign') }}
            </a>
        </div>
        <div class="clearfix"></div>
        <div style="height: 15px"></div>
        @include('alerts.success')
        @include('alerts.delete')
        <h4><strong>{{ __('Planned') }}</strong> ({{ $iCountPlannedCampaigns }} {{ __('campaigns') }})</h4>
        <table id="table_campaigns" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="name" data-sortable="true">{{ __('Name') }}</th>
                    <th data-field="segment">{{ __('Segment') }}</th>
                    <th data-field="agreement">{{ __('Agreement') }}</th>
                    <th data-field="term_offer">{{ __('Term Offer') }}</th>
                    <th data-field="csv">CSV</th>
                    <th data-field="action_edit" data-sortable="false" data-formatter="editFormatter" data-align="center" class="col-md-1"></th>
                    <th data-field="action_delete" data-sortable="false" data-formatter="deleteFormatter" data-align="center" class="col-md-1"></th>
                </tr>
            </thead>
        </table>
        <h4><strong>{{ __('Sent') }}</strong> ({{ $iCountSentCampaigns }} {{ __('campaigns') }})</h4>
        <table id="table_campaigns" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="name" data-sortable="true">{{ __('Name') }}</th>
                    <th data-field="segment">{{ __('Segment') }}</th>
                    <th data-field="agreement">{{ __('Agreement') }}</th>
                    <th data-field="term_offer">{{ __('Term Offer') }}</th>
                    <th data-field="csv">CSV</th>
                    <th class="col-md-1"></th>
                    <th class="col-md-1"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection