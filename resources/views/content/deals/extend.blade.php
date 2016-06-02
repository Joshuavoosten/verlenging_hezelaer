@extends('layouts.external')

@section('stylesheets')
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/deals/extend/normalize.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/deals/extend/main.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/css/content/deals/extend/shifft_template.css') }}">
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
                <span>{{ $oDeal->client_name }}</span>
                <span>Klantcode #{{ $oDeal->client_code }}</span>
            </li>
            <li class="right">
                <span><b>Persoonlijk contact opnemen</b></span>
                <span>{{ $oDeal->accountmanager }}</span>
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
                @if($oDeal->has_saving)
                    <a href="#">Uw jaarbesparing <b>&euro; <span class="estimate_saving">{{ number_format($oDeal->estimate_saving_3_year,2,',','.') }}</span></b></a>
                @endif
            </div>
        </div>
        <table style="font-size: 13px; width: 958px" class="currentAgreement table-content noBorder hezGrijs" cellpadding="0" cellspacing="0">
            <tr style="border: 1px solid #999899">
                <td colspan="7" class="morePad">
                    <b class="tableTitle">Uw huidige leveringsovereenkomst Elektriciteit & gas / Elek. / Gas</b>
                    Kenmerk V72-00-4673-05
                </td>
            </tr>
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
            <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899; font-size: 11px">
                <td>Elektriciteit</td>
                <td>8716879400254125</td>
                <td>41.627 kWh</td>
                <td>-</td>
                <td>3,42 &euro; ct/kWh</td>
                <td>2,89 &euro; ct/kWh</td>
                <td>1-1-2017</td>
            </tr>
            <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899; font-size: 11px">
                <td>Gas</td>
                <td>8716879400236523</td>
                <td>41.627 m3</td>
                <td>2,95 &euro; ct/m3</td>
                <td>-</td>
                <td>-</td>
                <td>1-1-2017</td>
            </tr>
            <tr style="border-left: 1px solid #999899; border-right: 1px solid #999899; font-size: 11px">
                <td colspan="7">&nbsp;
            </td>
            <tr class="hezOranje" style="border: 1px solid #f29421;">
                <td class="morePad" colspan="7">
                    <span class="newPrices"><b>Nieuwe tarieven</b></span>
                    <span class="newPrices"><b>Gas</b> 2,35 &euro; ct/m3</span>
                    <span class="newPrices"><b>Normaal</b> 2,55 &euro; ct/kWh</span>
                    <span class="newPrices"><b>Laag</b> 2,46 &euro; ct/kWh</span>
                    <span class="newPrices"><b>Enkel</b> 2,82 &euro; ct/kWh</span>
                    <span class="newPrices"><b>Jaarbesparing</b> &euro; 44,42</span>
                </td>
            </tr>
            <tr class="hezBlauw" style="border: 1px solid #76999b; border-top: none">
                <td class="infoCell1 morePad">&nbsp;</td>
                <td class="morePad" colspan="6">
                    - Nog een extra regel met informatie die je hier makkelijk kwijt kan.<br /><br />
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
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>Verleng u contract(en) tot 1-1-2018</span>
                    </label>
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>Verleng u contract(en) tot 1-1-2019</span>
                    </label>
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>Verleng u contract(en) tot 1-1-2020</span>
                    </label>
                </li>
                <li class="clearfix right">
                    <h4>Selecteer uw hernieuwbare bron</h4>
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>100% opgewekt door Nederlandse windmolens <i class="hezGrijs">(+0.30 ct/kWh)</i></span>
                    </label>
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>100% opgewekt door windmolens</span>
                    </label>
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>niet hernieuwbaar <i class="hezGrijs">(-0.05 ct/kWh)</i></span>
                    </label>
                </li>
            </ul>
            <h4>Communicatie</h4>
            <span class="subHead">Geef hieronder aan op welke email adressen we u het beste kunnen bereiken</span>
            <label class="commLabel clearfix">
                <span class="preText1">E-mail adres voor facturatie</span>
                <input class="rounded" type="text" placeholder="naam@domein.nl" />
            </label>
            <label class="commLabel clearfix">
                <span class="preText1">E-mail adres voor contractverlenging</span>
                <input class="rounded" type="text" placeholder="naam@domein.nl" />
            </label>
            <label class="commLabel lastCom clearfix">
                <span class="preText1">E-mail adres voor opgave meterstanden</span>
                <input class="rounded redBorder" type="text" placeholder="naam@domein.nl" />
                <span class="errorSpan">Dit is een verplicht veld</span>
            </label>
            <h4>Betalingsgegevens</h4>
            <ul class="betalingsGegevens clearfix">
                <li class="clearfix">
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>Ja, ik machtig Hezelaer voor automatische incasso</span>
                        <a class="infoHolder" href="#">
                            <div class="holderText rounded clearfix" style="display: none">
                                Integer accumsan, purus at eleifend rhoncus, leo velit auctor neque, vitae bibendum leo lorem quis tortor. 
                                Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. 
                                Nam feugiat leo lacus, viverra rutrum velit laoreet id. 
                                In hac habitasse platea dictumst.<br /><br />
                                <i class="infoRow">
                                    <b>Hezelaer Energy BV</b><br />
                                    Ulvenhoutselaan 12<br />
                                    4835 MC BREDA
                                </i>
                                <i class="infoRow">
                                    <b>Incassant ID</b><br />
                                    1234567890 ABC<br />
                                </i>
                            </div>
                        </a>
                    </label>
                </li>
                <li class="clearfix">
                    <label class="clearfix">
                        <input type="radio" name="" value="" />
                        <span>Nee, ik betaal per factuur</span>
                        <a class="infoHolder" href="#">
                            <div class="holderText rounded clearfix" style="display: block">
                                Integer accumsan, purus at eleifend rhoncus, leo velit auctor neque, vitae bibendum leo lorem quis tortor. 
                                Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. 
                                Nam feugiat leo lacus, viverra rutrum velit laoreet id. 
                                In hac habitasse platea dictumst.<br /><br />
                                <i class="infoRow">
                                    <b>Hezelaer Energy BV</b><br />
                                    Ulvenhoutselaan 12<br />
                                    4835 MC BREDA
                                </i>
                                <i class="infoRow">
                                    <b>Incassant ID</b><br />
                                    1234567890 ABC<br />
                                </i>
                            </div>
                        </a>
                    </label>
                </li>
            </ul>
            <div class="adresField clearfix">
                <label class="straatnaamLabel clearfix">
                    <span class="preText2">Factuuradres</span>
                    <input class="rounded" type="text" placeholder="Straatnaam" />
                </label>
                <label class="huisnrLabel clearfix">
                    <input class="rounded" type="text" placeholder="00" />
                </label>
                <label class="toevoegingLabel clearfix">
                    <input class="rounded" type="text" placeholder="A" />
                </label>
                <label class="postcodeLabel clearfix">
                    <input class="rounded" type="text" placeholder="1234 AB" />
                </label>
                <label class="stadLabel clearfix">
                    <input class="rounded" type="text" placeholder="Stad" />
                </label>
                <label class="ibanLabel clearfix">
                    <span class="preText2">IBAN</span>
                    <input class="rounded redBorder" type="text" placeholder="NL00 INGB 0000 0000 00" />
                    <span class="errorSpan">Dit is een verplicht veld</span>
                    <i class="hezGrijs">Vergeet niet uw bankrekeningnummer door te geven voor eventuele terug betalingen</i>
                </label>
            </div>
            <h4>Algemene voorwaarden</h4>
            <label class="algLabel clearfix">
                <input type="checkbox" name="" value="" />
                <a class="hezBlauw" href="#">Model aansluit- en transportovereenkomst (ATO)</a>
            </label>
            <label class="algLabel clearfix">
                <input type="checkbox" name="" value="" />
                <a class="hezBlauw" href="#">Algemene voorwaarden kleinverbruikaansluitingen</a>
            </label>
            <label class="algLabel lastAlg clearfix">
                <input type="checkbox" name="" value="" />
                <a class="hezBlauw" href="#">Leveringsvoorwaarden Zeker & Vast contract voor kleinverbruikaansluitingen</a>
                <span class="errorSpan">Dit is een verplicht veld</span>
            </label>
            <h4>Ondertekenen</h4>
            <label class="signLabel clearfix">
                <span class="preText3">Naam</span>
                <input class="rounded redBorder" type="text" placeholder="Voorletters + Achternaam" />
                <span class="errorSpan">Dit is een verplicht veld</span>
            </label>
            <label class="signLabel clearfix">
                <span class="preText3">Namens</span>
                <input class="rounded" type="text" placeholder="Bedrijfsnaam" />
            </label>
            <label class="signLabel clearfix">
                <span class="preText3">Functie</span>
                <input class="rounded" type="text" placeholder="Functie" />
            </label>
            <span class="ingevuldeInfo hezBlauw">
                voorletters + achternaam<br />
                functie bij bedrijfsnaam<br />
                <?= date('d-m-Y') ?>
            </span>
            <label class="submitLabel clearfix">
                <input type="checkbox" value="" name="" />
                <span class="hezBlauw">Hierbij verklaar ik mijn toestemming voor bovenstaande contractverlenging(en)</span>
            </label>
            {{ Form::submit('Contractverlengen ondertekenen en bevestigen', ['class' => 'submitForm rounded']) }}
        {!! Form::close() !!}
        <div class="succesMelding clearfix" style="display: none">
            <h3>Gefeliciteerd met uw nieuwe contract</h3>
            <h4>U heeft alles per e-mail ontvangen</h4>
            <b>Contractverlenging</b>
            <span>Wij hebben de bevestiging en de PDF van uw contractverlenging gemaild.</span>
            <a class="hezBlauw" href="#">Zeker&Vast-SC-20160415-v8t.pdf</a>
            <a class="hezBlauw" href="#">Download</a>
            <a class="hezBlauw" href="#">Print</a>
        </div>
    </div>
    <div id="footer">
        <ul class="clearfix">
            <li><span>Copyrights &copy; <?= date('Y') ?> Hezelaer Energy B.V. - Contractverlenging Mevr. A. B. Achternaam</span></li>
        </ul>
    </div>
</div>
@endsection