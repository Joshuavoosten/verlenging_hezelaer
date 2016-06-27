<div class="list-group">
    <a href="/campaigns" class="list-group-item odd @if ($segment_1 == 'campaigns') active @endif ">
        <i class="fa fa-refresh" style="margin-right: 6px"></i>
        {{ __('Campaigns') }}
    </a>
    <a href="/deals" class="list-group-item even @if ($segment_1 == 'deals') active @endif ">
        <i class="fa fa-paper-plane" style="margin-right: 6px"></i>
        {{ __('Deals') }}
    </a>
    <a href="/users" class="list-group-item even @if ($segment_1 == 'users') active @endif ">
        <i class="fa fa-users" style="margin-right: 6px"></i>
        {{ __('Users') }}
    </a>
</div>
