@extends('layouts.master')

@section('content')
<ol class="breadcrumb">
    <li>
        <i class="fa fa-euro" style="margin-right: 5px"></i>
        <a href="/prices">{{ __('Prices') }}</a>
    </li>
    <li class="active">{{ __('Import') }}</li>
</ol>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title pull-left">{{ __('Import') }}</h3>
        <a href="/prices" class="btn btn-default pull-right">
            <i class="fa fa-chevron-left" style="margin-right: 5px"></i>
            {{ __('Back') }}
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        {!! Form::open(['url' => '/prices/import', 'method' => 'post']) !!}
            @include('errors.validation')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ ($errors->first('name') ? 'has-error' : '') }}">
                        {{ Form::label('file', __('File')) }}
                        {{ Form::select('file', $aFiles, old('file'), ['id' => 'file', 'class' => 'form-control', 'data-placeholder' => __('Select Some Options')]) }}
                    </div>
                </div>
            </div>
            <div style="height: 15px"></div>
            {{ Form::submit(__('Submit'), ['class' => 'btn btn-custom']) }}
        {!! Form::close() !!}
    </div>
</div>
@endsection