<div class="list-group">
    <a href="/campaigns" class="list-group-item odd @if ($segment_1 == 'campaigns') active @endif ">
        <i class="fa fa-refresh" style="margin-right: 6px"></i>
        {{ __('Campaigns') }}
    </a>
    <a href="/users" class="list-group-item even @if ($segment_1 == 'users') active @endif ">
        <i class="fa fa-users" style="margin-right: 6px"></i>
        {{ __('Users') }}
    </a>
    <!--
    <a href="/i18n" class="list-group-item odd @if ($segment_1 == 'i18n') active @endif ">
        <i class="fa fa-flag" style="margin-right: 6px"></i>
        I18n
    </a>
    -->
</div>
