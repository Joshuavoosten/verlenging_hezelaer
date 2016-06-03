<?php

if (!function_exists('__')) {
    function __($string)
    {
        $r = trans('messages.'.$string);

        return substr($r, 0, 9) === 'messages.' ? $string : $r;
    }

    /**
     * Validate IBAN
     *
     * @param string $iban
     * @return boolean true | false
     * @see http://nl.wikipedia.org/wiki/International_Bank_Account_Number
     */
    function validateIban($iban)
    {
        $controlegetal = null;

        # 1. valideer de samenstelling

        if (strlen($iban) > 34) {
            return false;
        }

        # 2. verplaats de eerste 4 karakters naar het einde

        $controlegetal = substr($iban, 4) . substr($iban, 0, 4);

        # 3. vervang elke letter door 2 cijfers, waarbij A = 10, B = 11, ..., Z = 35

        $find    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $replace = ['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36'];

        $controlegetal = str_replace($find, $replace, $controlegetal);

        # 4. bereken dan het getal modulo 97

        $restwaarde = bcmod($controlegetal, '97');

        # 5. als de restwaarde 1 is, dan klopt het nummer op basis van het controlecijfer en kan het IBAN valide zijn

        if ($restwaarde == 1) {
            return true;
        }

        return false;
    }

}
