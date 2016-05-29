@extends('layouts.master')

@section('scripts')
<script src="{{ URL::asset('assets/js/content/prices/index.js') }}"></script>
<script src="{{ URL::asset('assets/js/formatters/profileCodes.js') }}"></script>
@endsection

@section('content')
{!! csrf_field() !!}
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-eur"></i>
            {{ __('Prices') }}
        </h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                {{ Form::label('rate', __('Rate')) }}
                {{ Form::select('code', $aRates, old('rate'), ['id' => 'rate', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
            </div>
            <div class="col-md-3">
                {{ Form::label('code', __('Profile Code')) }}
                {{ Form::select('code', $aProfileCodes, old('code'), ['id' => 'code', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
            </div>
        </div>
        <table id="table_prices" class="table table-hover table-striped sortable" data-side-pagination="server" data-pagination="true" data-page-size="25" data-page-list="[25, 50, 100]" data-search="true">
            <thead>
                <tr>
                    <th data-field="date_start" data-align="center" data-sortable="true" class="col-md-1">{{ __('Date Start') }}</th>
                    <th data-field="date_end" data-align="center" data-sortable="true" class="col-md-1">{{ __('Date End') }}</th>
                    <th data-field="rate" data-sortable="true">{{ __('Rate') }}</th>
                    <th data-field="codes" data-formatter="profileCodesFormatter" data-sortable="false">{{ __('Profile Codes') }}</th>
                    <th data-field="price" data-align="right" data-sortable="true" class="col-md-2">{{ __('Price') }}</th>
                    <th data-field="created_at" data-align="center" data-sortable="true" class="col-md-2">{{ __('Created') }}</th>
                    <th data-field="updated_at" data-align="center" data-sortable="true" class="col-md-2">{{ __('Updated') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection