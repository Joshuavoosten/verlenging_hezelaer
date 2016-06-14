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
            <li class="right">
                <span><b>Klantgegevens</b></span>
                <span>{{ $oCampaignCustomer->client_name }}</span>
                <span>Klantcode #{{ $oCampaignCustomer->client_code }}</span>
            </li>
            <li class="right">
                <span><b>Persoonlijk contact opnemen</b></span>
                <span>{{ $oCampaignCustomer->accountmanager }}</span>
                <span>T. +31 76 30 30 720</span>
            </li>
        </ul>
    </div>
    <div id="content" class="clearfix">
        <div class="succesMelding clearfix">
            <h3>Gefeliciteerd met uw nieuwe contract</h3>
            <h4>U heeft alles per e-mail ontvangen</h4>
            <b>Contractverlenging</b>
            <span>Wij hebben de bevestiging en de PDF van uw contractverlenging gemaild.</span>
        </div>
    </div>
    <div id="footer">
        <ul class="clearfix">
            <li><span>Copyrights &copy; {{ date('Y') }} Hezelaer Energy B.V.</span></li>
        </ul>
    </div>
</div>
@endsection