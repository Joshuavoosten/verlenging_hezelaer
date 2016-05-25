@if ($errors->has())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $sError)
            {{ $sError }}<br />
        @endforeach
    </div>
@endif