@extends('layouts.external')

@section('stylesheets')
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/campaigns/customer/extend/normalize.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/campaigns/customer/extend/main.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/campaigns/customer/extend/shifft_template.css') }}">
@endsection

@section('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script src="{{ URL::asset('assets/js/content/campaigns/customer/extend.js') }}"></script>
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
        <div class="topInfo clearfix">
            <div class="left">
                <h3>{{ sprintf('Uw nieuwe %s contract', $oCampaign->new_agreement) }}</h3>
                <h4>Snel en makkelijk online verlengen</h4>
            </div>
            <div class="right">
                @if($oCampaignCustomer->has_saving)
                    <a href="#">Uw jaarbesparing <b>&euro; <span class="estimate_saving">{{ number_format($oCampaignCustomer->estimate_saving_3_year,2,',','.') }}</span></b></a>
                @endif
            </div>
        </div>
        <table style="font-size: 13px; width: 958px;" class="currentAgreement table-content noBorder hezGrijs" cellpadding="0" cellspacing="0">
            <tr style="border: 1px solid #999899;">
                <td colspan="7" class="morePad">
                    <b class="tableTitle">Uw huidige leveringsovereenkomst(en)</b>
                    Kenmerk {{ $oCampaignCustomer->kenmerkFormatter() }}
                </td>
            </tr>
            {{--*/ $cadrChecksum = null /*--}}
            @foreach ($aDeals as $oDeal)
                @if($cadrChecksum != $oDeal->cadrChecksum())
                    <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899">
                        <td colspan="7" class="morePad"><b>Leveringsadres:</b> {{ $oDeal->cadrFormatter() }}</td>
                    </tr>
                    <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899"> 
                        <td width="90px"><b>Type</b></td>
                        <td width="140px"><b>EAN Code</b></td>
                        <td width="165px"><b>Totaal jaarverbruik</b></td>
                        <td width="165px"><b>Tarief (gas)</b></td>
                        <td width="165px"><b>Tarief (normaal)</b></td>
                        <td width="140px"><b>Tarief (laag)</b></td>
                        <td width="85px"><b>Einddatum</b></td>
                    </tr>
                @endif
                @if($oDeal->isElektricity())
                    <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899; font-size: 11px">
                        <td>Elektriciteit</td>
                        <td>{{ $oDeal->ean }}</td>
                        <td>{{ number_format($oDeal->totalAnnualConsumption(),0,',','.') }} kWh</td>
                        <td>-</td>
                        <td>
                            @if($oDeal->price_normal == 0)
                                -
                            @else
                                {{ number_format($oDeal->price_normal,6,',','.') }} &euro; ct/kWh
                            @endif
                        </td>
                        <td>
                            @if($oDeal->price_low == 0)
                                -
                            @else
                                {{ number_format($oDeal->price_low,6,',','.') }} &euro; ct/kWh
                            @endif
                        </td>
                        <td>
                            @if($oDeal->end_agreement == '3000-01-01')
                                -
                            @else
                                {{ date('d-m-Y', strtotime($oDeal->end_agreement)) }}
                            @endif
                        </td>
                    </tr>
                @endif
                @if($oDeal->isGas())
                    <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899; font-size: 11px">
                        <td>Gas</td>
                        <td>{{ $oDeal->ean }}</td>
                        <td>{{ number_format($oDeal->totalAnnualConsumption(),0,',','.') }} m3</td>
                        <td>
                            {{ number_format($oDeal->price_normal,6,',','.') }} &euro; ct/m3
                        </td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            @if($oDeal->end_agreement == '3000-01-01')
                                -
                            @else
                                {{ date('d-m-Y', strtotime($oDeal->end_agreement)) }}
                            @endif
                        </td>
                    </tr>
                @endif
                {{--*/ $cadrChecksum = $oDeal->cadrChecksum() /*--}}
            @endforeach
            <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899; font-size: 11px">
                <td colspan="7">&nbsp;
            </td>
            @foreach ($aCampaignPrices as $sCode => $o)
                <tr class="hezOranje nieuwe_tarieven" style="border: 1px solid #f29421">
                    @if($o[3]->type == \App\Models\CampaignPrice::TYPE_GAS)
                        <td class="morePad" colspan="7">
                            <span class="newPrices"><b>Nieuwe tarieven ({{ $sCode }})</b></span>
                            <span class="newPrices"><b>Gas</b> {{ number_format($o[3]->price_normal/100,6,',','.') }} &euro; ct/m3</span>
                        </td>
                    @endif
                    @if($o[3]->type == \App\Models\CampaignPrice::TYPE_ELEKTRICITY)
                        <td class="morePad" colspan="7">
                            <span class="newPrices"><b>Nieuwe tarieven ({{ $sCode }})</b></span>
                            <span class="newPrices"><b>Normaal</b> {{ number_format($o[3]->price_normal/100,6,',','.') }} &euro; ct/kWh</span>
                            <span class="newPrices"><b>Laag</b> {{ number_format($o[3]->price_low/100,6,',','.') }} &euro; ct/kWh</span>
                            @if($o[3]->price_enkel > 0)
                                <span class="newPrices"><b>Enkel</b> {{ number_format($o[3]->price_enkel/100,6,',','.') }} &euro; ct/kWh</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
            <tr class="hezBlauw" style="border: 1px solid #76999b; border-top: none;">
                <td class="infoCell1 morePad">&nbsp;</td>
                <td class="morePad" colspan="6">
                    - De huishoudelijke energie opwek wordt gesaldeerd tegen hetzelfde tarief.<br /><br />
                    - Alle leveringstarieven zijn excl. btw, de huidige energiebelasting en andere tot op heden bekende
                    overheidstoeslagen. Het leveringstarief voor gas is incl. regiotoeslag.<br /><br />
                    - De vaste leveringskosten bedraagt €4,95 excl. btw, per aansluiting elektriciteit of gas, per maand.
                </td>
            </tr>
        </table>
        {!! Form::open(['url' => '/verleng/'.$token, 'method' => 'post', 'class' => 'contractVerlenging clearfix']) !!}
            <ul class="selectLooptijdBron clearfix">
                <li class="clearfix left">
                    <h4>Selecteer de looptijd</h4>
                    @if($errors->has('form_end_agreement'))
                        <br />
                        <span class="errorSpan">Dit is een verplichte keuze</span>
                    @endif
                    <label class="clearfix">
                        {{ Form::radio('form_end_agreement', 1, ($aData['form_end_agreement'] == 1 ? true : false)) }}
                        <span>Verleng u contract(en) tot {{ date('d-m-Y', strtotime('+1 year', strtotime($oDeal->end_agreement))) }}</span>
                    </label>
                    <label class="clearfix">
                        {{ Form::radio('form_end_agreement', 2, ($aData['form_end_agreement'] == 2 ? true : false)) }}
                        <span>Verleng u contract(en) tot {{ date('d-m-Y', strtotime('+2 year', strtotime($oDeal->end_agreement))) }}</span>
                    </label>
                    <label class="clearfix">
                        {{ Form::radio('form_end_agreement', 3, ($aData['form_end_agreement'] == 3 ? true : false)) }}
                        <span>Verleng u contract(en) tot {{ date('d-m-Y', strtotime('+3 year', strtotime($oDeal->end_agreement))) }}</span>
                    </label>
                </li>
                @if($hasElektricity)
                    <li class="clearfix right">
                        <h4>Selecteer uw hernieuwbare bron</h4>
                        @if($errors->has('form_renewable_resource'))
                            <br />
                            <span class="errorSpan">Dit is een verplichte keuze</span>
                        @endif
                        <label class="clearfix">
                            {{ Form::radio('form_renewable_resource', 1, ($aData['form_renewable_resource'] == 1 ? true : false)) }}
                            <span>100% opgewekt door Nederlandse windmolens <i class="hezGrijs">(+0.30 ct/kWh)</i></span>
                        </label>
                        <label class="clearfix">
                            {{ Form::radio('form_renewable_resource', 2, ($aData['form_renewable_resource'] == 2 ? true : false)) }}
                            <span>100% opgewekt door windmolens</span>
                        </label>
                        <label class="clearfix">
                            {{ Form::radio('form_renewable_resource', 3, ($aData['form_renewable_resource'] == 3 ? true : false)) }}
                            <span>niet hernieuwbaar <i class="hezGrijs">(-0.05 ct/kWh)</i></span>
                        </label>
                    </li>
                @endif
            </ul>
            <h4>Communicatie</h4>
            <span class="subHead">Geef hieronder aan op welke email adressen we u het beste kunnen bereiken</span>
            <label class="commLabel clearfix">
                <span class="preText1">E-mail adres voor facturatie</span>
                {{ Form::text('form_email_billing', $aData['form_email_billing'], ['placeholder' => 'naam@domein.nl', 'class' => 'rounded '.($errors->has('form_email_billing') ? 'redBorder' : null)]) }}
                @if($errors->has('form_email_billing'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <label class="commLabel clearfix">
                <span class="preText1">E-mail adres voor contractverlenging</span>
                {{ Form::text('form_email_contract_extension', $aData['form_email_contract_extension'], ['placeholder' => 'naam@domein.nl', 'class' => 'rounded '.($errors->has('form_email_contract_extension') ? 'redBorder' : null)]) }}
                @if($errors->has('form_email_contract_extension'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <label class="commLabel lastCom clearfix">
                <span class="preText1">E-mail adres voor opgave meterstanden</span>
                {{ Form::text('form_email_meter_readings', $aData['form_email_meter_readings'], ['placeholder' => 'naam@domein.nl', 'class' => 'rounded '.($errors->has('form_email_meter_readings') ? 'redBorder' : null)]) }}
                @if($errors->has('form_email_meter_readings'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <h4>Betalingsgegevens</h4>
            <ul class="betalingsGegevens clearfix">
                <li class="clearfix">
                    <label class="clearfix">
                        {{ Form::radio('form_payment', \App\Models\Form::PAYMENT_AUTOMATIC_COLLECTION, ($aData['form_payment'] == \App\Models\Form::PAYMENT_AUTOMATIC_COLLECTION ? true : false)) }}
                        <span>Ja, ik machtig Hezelaer voor automatische incasso</span>
                        <a class="infoHolder tooltip-payment" href="#">
                            <div class="holderText rounded clearfix" style="display: none">
                                <strong>Doorlopende machtiging SEPA automatische incasso</strong><br />
                                Door ondertekening van dit formulier geeft u toestemming aan Hezelaer Energy B.V. (hierna Hezelaer), met incassant id NL38ZZZ171680950000 om doorlopend incasso-opdrachten te sturen naar uw bank om een bedrag van uw rekening af te schrijven wegens openstaande factu(u)r(e)n en uw bank om doorlopend een bedrag van uw rekening af te schrijven overeenkomstig de opdracht van Hezelaer. Als u het niet eens bent met deze afschrijving kunt u deze laten terugboeken. Neem hiervoor acht werken na afschrijving contact op met uw bank. Vraag uw bank naar de voorwaarden.<br />
                                <br />
                                <div style="font-style: italic"><strong>Let op:</strong> Er kan alleen ge&iuml;ncasseerd worden van uw rekening als u uw bank hiervoor toestemming geeft. Zie voor meer informatie de website van uw bank.</div>
                                <br />
                                <i class="infoRow">
                                    <b>Hezelaer Energy BV</b><br />
                                    Ulvenhoutselaan 12<br />
                                    4835 MC BREDA
                                </i>
                                <i class="infoRow">
                                    <b>Incassant ID</b><br />
                                    NL38ZZZ171680950000<br />
                                </i>
                            </div>
                        </a>
                    </label>
                </li>
                <li class="clearfix">
                    <label class="clearfix">
                        {{ Form::radio('form_payment', \App\Models\Form::PAYMENT_INVOICE, ($aData['form_payment'] == \App\Models\Form::PAYMENT_INVOICE ? true : false)) }}
                        <span>Nee, ik betaal per factuur</span>
                        <a class="infoHolder tooltip-payment" href="#">
                            <div class="holderText rounded clearfix" style="display: none">
                                <strong>Betaling per factuur</strong><br />
                                Kiest u nier voor automatische incasso dan betaal u &euro; 2,- exclusief btw administratiekosten per maand en dient u de factuur te voldoen binnen 7 dagen na ontvangt. Indien Hezelaer uw automatische incasso be&euml;indigd i.v.m. een stornering, dan worden deze kosten ook in rekening gebracht. Wilt u facturen per post ontvangen, dan brengt Hezelaer Energy hiervoor &euro; 2,- exclusief btw per verzonden factuur per post in rekening. Het verzenden van uw facturen per e-mail is gratis. Indien Leveranciers de overeenkomst later dan bovenstaande datum (schriftelijk) ontvangt, dan heeft Leverancier het recht de tarieven te wijzigen, alleen indien Leverancier hierdoor een financieel nadeel heeft. De getekende overeenkomst komt hiermee niet te vervallen.<br/>
                                <br />
                                <i class="infoRow">
                                    <b>Hezelaer Energy BV</b><br />
                                    Ulvenhoutselaan 12<br />
                                    4835 MC BREDA
                                </i>
                                <i class="infoRow">
                                    <b>Incassant ID</b><br />
                                    NL38ZZZ171680950000<br />
                                </i>
                            </div>
                        </a>
                    </label>
                </li>
            </ul>
            @if($errors->has('form_payment'))
                <span class="errorSpan">Dit is een verplichte keuze</span>
            @endif
            <div class="adresField clearfix">
                <label class="straatnaamLabel clearfix">
                    <span class="preText2">Factuuradres</span>
                    {{ Form::text('form_fadr_street', $aData['form_fadr_street'], ['placeholder' => 'Straatnaam', 'class' => 'rounded '.($errors->has('form_fadr_street') ? 'redBorder' : null)]) }}
                </label>
                <label class="huisnrLabel clearfix">
                    {{ Form::text('form_fadr_nr', $aData['form_fadr_nr'], ['placeholder' => '00', 'class' => 'rounded '.($errors->has('form_fadr_nr') ? 'redBorder' : null)]) }}
                </label>
                <label class="toevoegingLabel clearfix">
                   {{ Form::text('form_fadr_nr_conn', $aData['form_fadr_nr_conn'], ['placeholder' => 'A', 'class' => 'rounded '.($errors->has('form_fadr_nr_conn') ? 'redBorder' : null)]) }}
                </label>
                @if($errors->has('form_fadr_street') || $errors->has('form_fadr_nr'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
                <label class="postcodeLabel clearfix">
                    {{ Form::text('form_fadr_zip', $aData['form_fadr_zip'], ['placeholder' => '1234 AB', 'class' => 'rounded '.($errors->has('form_fadr_zip') ? 'redBorder' : null)]) }}
                </label>
                <label class="stadLabel clearfix">
                    {{ Form::text('form_fadr_city', $aData['form_fadr_city'], ['placeholder' => 'Stad', 'class' => 'rounded '.($errors->has('form_fadr_city') ? 'redBorder' : null)]) }}
                </label>
                @if($errors->has('form_fadr_zip') || $errors->has('form_fadr_city'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
                <label class="ibanLabel clearfix">
                    <span class="preText2">IBAN</span>
                    {{ Form::text('form_iban', $aData['form_iban'], ['placeholder' => 'NL00 INGB 0000 0000 00', 'class' => 'rounded '.($errors->has('form_iban') ? 'redBorder' : null)]) }}
                    @if($errors->has('form_iban'))
                        <span class="errorSpan">Dit is een verplicht veld</span>
                    @endif
                    <i class="hezGrijs">Vergeet niet uw bankrekeningnummer door te geven voor eventuele terug betalingen</i>
                </label>
            </div>
            <h4>Algemene voorwaarden</h4>
            <label class="algLabel clearfix">
                {{ Form::checkbox('form_terms_and_conditions_1', 1, ($aData['form_terms_and_conditions_1'] == 1 ? true : false)) }}
                <a class="hezBlauw" href="http://www.hezelaer.nl/voorwaarden/" target="index">Model aansluit- en transportovereenkomst (ATO)</a>
                @if($errors->has('form_terms_and_conditions_1'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <label class="algLabel clearfix">
                {{ Form::checkbox('form_terms_and_conditions_2', 1, ($aData['form_terms_and_conditions_2'] == 1 ? true : false)) }}
                <a class="hezBlauw" href="http://www.hezelaer.nl/voorwaarden/" target="index">Algemene voorwaarden kleinverbruikaansluitingen</a>
                @if($errors->has('form_terms_and_conditions_2'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <label class="algLabel lastAlg clearfix">
                {{ Form::checkbox('form_terms_and_conditions_3', 1, ($aData['form_terms_and_conditions_3'] == 1 ? true : false)) }}
                <a class="hezBlauw" href="http://www.hezelaer.nl/voorwaarden/" target="index">Leveringsvoorwaarden Zeker & Vast contract voor kleinverbruikaansluitingen</a>
                @if($errors->has('form_terms_and_conditions_3'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <h4>Ondertekenen</h4>
            <label class="signLabel clearfix">
                <span class="preText3">Naam</span>
                {{ Form::text('form_sign_name', $aData['form_sign_name'], ['placeholder' => 'Voorletters + Achternaam', 'class' => 'form_sign_name rounded '.($errors->has('form_sign_name') ? 'redBorder' : null)]) }}
                @if($errors->has('form_sign_name'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <label class="signLabel clearfix">
                <span class="preText3">Namens</span>
                {{ Form::text('form_sign_on_behalf_of', $aData['form_sign_on_behalf_of'], ['placeholder' => 'Bedrijfsnaam', 'class' => 'rounded '.($errors->has('form_sign_on_behalf_of') ? 'redBorder' : null)]) }}
                @if($errors->has('form_sign_on_behalf_of'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <label class="signLabel clearfix">
                <span class="preText3">Functie</span>
                {{ Form::text('form_sign_function', $aData['form_sign_function'], ['placeholder' => 'Functie', 'class' => 'form_sign_function rounded '.($errors->has('form_sign_function') ? 'redBorder' : null)]) }}
                @if($errors->has('form_sign_function'))
                    <span class="errorSpan">Dit is een verplicht veld</span>
                @endif
            </label>
            <span class="ingevuldeInfo hezBlauw">
                <div class="sign_name">{{ $aData['form_sign_name'] }}</div>
                <div class="sign_function">{{ $aData['form_sign_function'] }}</div>
                {{ date('d-m-Y') }}<br />
            </span>
            <label class="submitLabel clearfix">
                {{ Form::checkbox('form_permission', 1, ($aData['form_permission'] == 1 ? true : false)) }}
                <span class="hezBlauw">Hierbij verklaar ik mijn toestemming voor bovenstaande contractverlenging(en)</span>
            </label>
            <div class="clearfix"></div>
            @if($errors->has('form_permission'))
                <span class="errorSpan">Dit is een verplicht veld</span>
            @endif
            {{ Form::submit('Contractverlengen ondertekenen en bevestigen', ['class' => 'submitForm rounded']) }}
        {!! Form::close() !!}
    </div>
    <div id="footer">
        <ul class="clearfix">
            <li><span>Copyrights &copy; {{ date('Y') }} Hezelaer Energy B.V. - Contractverlenging {{ $oCampaignCustomer->client_name }}</span></li>
        </ul>
    </div>
</div>
@endsection