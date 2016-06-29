<div class="list-group">
    <a href="/campaigns" class="list-group-item odd @if ($segment_1 == 'campaigns') active @endif ">
        <i class="fa fa-refresh" style="margin-right: 6px"></i>
        {{ __('Campaigns') }}
    </a>
    <a href="/deals" class="list-group-item even @if ($segment_1 == 'deals') active @endif ">
        <i class="fa fa-paper-plane" style="margin-right: 6px"></i>
        {{ __('Deals') }}
    </a>
    <a href="/prices" class="list-group-item even @if ($segment_1 == 'prices') active @endif ">
        <i class="fa fa-euro" style="margin-right: 6px"></i>
        {{ __('Prices') }}
    </a>
    <a href="/contracts" class="list-group-item even @if ($segment_1 == 'contracts') active @endif ">
        <i class="fa fa-list" style="margin-right: 6px"></i>
        {{ __('Contracts') }}
    </a>
    <a href="/i18n" class="list-group-item even @if ($segment_1 == 'i18n') active @endif ">
        <i class="fa fa-flag" style="margin-right: 6px"></i>
        {{ __('Translations') }}
    </a>
    <a href="/users" class="list-group-item even @if ($segment_1 == 'users') active @endif ">
        <i class="fa fa-users" style="margin-right: 6px"></i>
        {{ __('Users') }}
    </a>
</div>
