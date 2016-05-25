function number_format_to_decimal(value){
    var COMMA = ',';
    var DOT   = '.';
    var decimal = '0.00';
    var count_comma_seperators = 0;
    var count_dot_seperators   = 0;
    // Controleren of de komma seperator voorkomt
    if (value.indexOf(COMMA)!= '-1'){
        count_comma_seperators += value.split(COMMA).length - 1;
    }
    // Controleren of de punt seperator voorkomt
    if (value.indexOf(DOT)!= '-1'){
        count_dot_seperators += value.split(DOT).length - 1;
    }
    // Indien er ÈÈn seperator is, en het is een komma, dan vervangen we de komma door een punt
    if (count_comma_seperators == 1 && count_dot_seperators == 0){
        decimal = value.replace(COMMA, DOT);
    }
    // Indien er ÈÈn seperator is, en het is een punt, dan doen we niets
    else if (count_comma_seperators == 0 && count_dot_seperators == 1){
        decimal = value;
    }
    // Indien er twee verschillende seperators zijn
    else if (count_comma_seperators == 1 && count_dot_seperators == 1){
        decimal = value.replace(COMMA, DOT);
        decimal = decimal.replace(/[.](?![^.]*$)/, '');
    }
    // Indien er meererde punt seperators zijn maar geen komma seperators
    else if (count_comma_seperators == 0 && count_dot_seperators > 1){
        decimal = value.replace(/\./g, '');
    }
    // Indien er meererde komma seperators zijn maar geen punt seperators
    else if (count_comma_seperators > 1 && count_dot_seperators == 0){
        decimal = value.replace(/\,/g, '');
    }
    // Indien er meerdere punt seperators zijn en ÈÈn komma seperator
    else if (count_comma_seperators == 1 && count_dot_seperators > 1){
        decimal = value.replace(/\./g, '');
        decimal = decimal.replace(/\,/g, DOT);
    }
    // Indien er meerdere komma seperators zijn en ÈÈn punt seperator
    else if (count_comma_seperators > 1 && count_dot_seperators == 1){
        decimal = value.replace(/\,/g, '');
    }
    else {
        decimal = value;
    }
    return decimal;
}