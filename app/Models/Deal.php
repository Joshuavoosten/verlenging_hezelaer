<?php

namespace App\Models;

use App\Models\Price as ModelPrice;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $table = 'deals';

    /**
     * @param int $iCalc
     * @param int $iYears
     * @param int $iMonths
     * @param float $syu_normal
     * @param float $syu_low
     * @param float $vastrecht
     * @param float $price_normal
     * @param float $price_low
     * @return float $fCosts
     */
    public static function calculateCosts($iCalculation,$aPrices,$iYears,$iMonths,$syu_normal,$syu_low,$vastrecht,$price_normal,$price_low) {
        $fCosts = 0;

        switch ($iCalculation) {

            // Gas met een tarief normaal

            case ModelPrice::CALCULATION_GAS_1_RATE:
                $new_price_gas = $aPrices['normal'];

                // Kosten = ((syu_gas * new_price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)
                $fCosts = (($syu_normal * $new_price_gas) * $iYears) + ($vastrecht * $iMonths);

                return $fCosts;

            // Energie met 2 tarieven (hoog en laag)

            case ModelPrice::CALCULATION_ENERGY_2_RATES:
                $new_price_low = $aPrices['low'];
                $new_price_high = $aPrices['normal'];

                // Kosten = (((syu_low * new_price_low) + (syu_high * new_price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)
                $fCosts = ((($syu_low * $new_price_low) + ($syu_normal * $new_price_high)) * $iYears) + ($vastrecht * $iMonths);

                return $fCosts;

            // Energie met 3 tarieven (hoog, laag en enkel)

            case ModelPrice::CALCULATION_ENERGY_3_RATES:
                $new_price_enkel = $aPrices['enkel'];

                // Kosten = ((syu_normal * new_price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)
                $fCosts = (($syu_normal * $new_price_enkel) * $iYears) + ($vastrecht * $iMonths);

                return $fCosts;
        }
    }

    /**
     * @param int $iCalc
     * @param int $iYears
     * @param int $iMonths
     * @param float $syu_normal
     * @param float $syu_low
     * @param float $vastrecht
     * @param float $price_normal
     * @param float $price_low
     * @return float $fSaving
     */
    public static function calculateSaving($iCalculation,$aPrices,$iYears,$iMonths,$syu_normal,$syu_low,$vastrecht,$price_normal,$price_low) {
        $fSaving = 0;

        switch ($iCalculation) {

            // Gas met een tarief normaal

            case ModelPrice::CALCULATION_GAS_1_RATE:
                $new_price_gas = $aPrices['normal'];

                // Besparing = (((syu_gas * new_price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                //           - (((syu_gas * price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))

                $fCostsOld = ((($syu_normal * $new_price_gas) * $iYears) + ($vastrecht * $iMonths));
                $fCostsNew = ((($syu_normal * $price_normal) * $iYears) + ($vastrecht * $iMonths));
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                return $fSaving;

            // Energie met 2 tarieven (hoog en laag)

            case ModelPrice::CALCULATION_ENERGY_2_RATES:
                $new_price_low = $aPrices['low'];
                $new_price_high = $aPrices['normal'];

                // Besparing = ((((syu_low * price_low) + (syu_high * price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                // - ((((syu_low * new_price_low) + (syu_high * new_price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))

                $fCostsOld = (((($syu_low * $price_low) + ($syu_normal * $price_normal)) * $iYears) + ($vastrecht * $iMonths));
                $fCostsNew = ((($syu_low * $new_price_low) + ($syu_normal * $new_price_high) * $iYears) + ($vastrecht * $iMonths));
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                return $fSaving;

            // Energie met 3 tarieven (hoog, laag en enkel)

            case ModelPrice::CALCULATION_ENERGY_3_RATES:
                $new_price_enkel = $aPrices['enkel'];

                // Besparing = (((syu_normal * new_price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                //           - (((syu_normal * price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))

                $fCostsOld = ((($syu_normal * $new_price_enkel) * $iYears) + ($vastrecht * $iMonths));
                $fCostsNew = ((($syu_normal * $price_normal) * $iYears) + ($vastrecht * $iMonths));
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                return $fSaving;
        }
    }

}
