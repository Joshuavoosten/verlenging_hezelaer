<?php

return DB::table('i18n')
    ->join('languages AS l1', 'l1.id', '=', 'i18n.source_language')
    ->join('languages AS l2', 'l2.id', '=', 'i18n.destination_language')
    ->where('l1.locale', '=', 'en')
    ->where('l2.locale', '=', 'nl')
    ->orderBy('i18n.source_string')
    ->lists('i18n.destination_string', 'i18n.source_string')
;