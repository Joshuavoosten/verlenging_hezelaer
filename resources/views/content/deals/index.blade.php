@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/deals/index.js') }}"></script>
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
        <table id="table_deals" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="client_name" data-sortable="true">{{ __('Client Name') }}</th>
                    <th data-field="email_commercieel" data-sortable="true">{{ __('Email') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection