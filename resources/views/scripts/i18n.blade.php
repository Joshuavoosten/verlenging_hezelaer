var i18n = {
@foreach ($i18n as $k => $v)
    '{{ $k }}': '{{ $v }}',
@endforeach
}