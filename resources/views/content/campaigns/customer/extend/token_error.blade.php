@extends('layouts.external')

@section('stylesheets')
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/campaigns/customer/extend/normalize.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/campaigns/customer/extend/main.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/campaigns/customer/extend/shifft_template.css') }}">
@endsection

@section('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
@endsection

@section('content')
<div id="wrapper">
    <div id="header">
        <ul class="clearfix">
            <li class="left"></li>
        </ul>
    </div>
    <div id="content" class="clearfix">
        <div class="succesMelding clearfix">
            <b>Fout</b>
            <span>De token is onjuist.</span>
        </div>
    </div>
    <div id="footer">
        <ul class="clearfix">
            <li><span>Copyrights &copy; {{ date('Y') }} Hezelaer Energy B.V.</span></li>
        </ul>
    </div>
</div>
@endsection