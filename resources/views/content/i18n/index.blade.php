@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/i18n/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/actions/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/delete.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/edit.js') }}"></script>
@endsection

@section('content')
{!! csrf_field() !!}
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-flag"></i>
            {{ __('I18n') }}
        </h3>
    </div>
    <div class="panel-body">
        <a href="/i18n/add" class="btn btn-default pull-right">
            <i class="fa fa-plus" style="margin-right: 5px"></i>
            {{ __('Add I18n') }}
        </a>
        <div class="clearfix"></div>
        <div style="height: 15px"></div>
        @include('alerts.success')
        @include('alerts.delete')
        <div class="row">
            {!! Form::open() !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('source_language', __('Source Language')) }}
                        {{ Form::select('source_language', $aLanguages, 1, ['id' => 'source_language', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{ Form::label('destination_language', __('Destination Language')) }}
                        {{ Form::select('destination_language', $aLanguages, 2, ['id' => 'destination_language', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class="clearfix"></div>
            {!! Form::close() !!}
        </div>
        <div class="clearfix"></div>
        <table id="table_i18n" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="source_string" data-sortable="true">{{ __('Source String') }}</th>
                    <th data-field="destination_string" data-sortable="true">{{ __('Destination String') }}</th>
                    <th data-field="action_edit" data-sortable="false" data-formatter="editFormatter" data-align="center" class="col-md-1"></th>
                    <th data-field="action_delete" data-sortable="false" data-formatter="deleteFormatter" data-align="center" class="col-md-1"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection