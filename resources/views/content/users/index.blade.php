@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/users/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/actions/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/edit.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/email.js') }}"></script>
@endsection

@section('content')
{!! csrf_field() !!}
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-users"></i>
            {{ __('Users') }}
        </h3>
    </div>
    <div class="panel-body">
        <div class="pull-left">
            <h3>{{ __('Overview') }}</h3>
        </div>
        <div class="pull-right">
            <a href="/users/add" class="btn btn-default">
                <i class="fa fa-plus" style="margin-right: 5px"></i>
                {{ __('Add User') }}
            </a>
        </div>
        <div class="clearfix"></div>
        @include('alerts.success')
        @include('alerts.delete')
        <table id="table_users" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="name" data-sortable="true">{{ __('Name') }}</th>
                    <th data-field="email" data-formatter="emailFormatter" data-sortable="true">{{ __('Email') }}</th>
                    <th data-field="created_at" data-sortable="true" data-align="center">{{ __('Created') }}</th>
                    <th data-field="updated_at" data-sortable="true" data-align="center">{{ __('Updated') }}</th>
                    <th data-field="action_edit" data-sortable="false" data-formatter="editFormatter" data-align="center" class="col-md-1"></th>
                    <th data-field="action_delete" data-sortable="false" data-formatter="deleteFormatter" data-align="center" class="col-md-1"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection