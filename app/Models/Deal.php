<?php

namespace App\Models;

use App\Models\Price as ModelPrice;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    const TYPE_GAS = 1;
    const TYPE_ELEKTRICITY = 2;

    const CALCULATION_GAS_1_RATE = 1;
    const CALCULATION_ENERGY_2_RATES = 2;
    const CALCULATION_ENERGY_3_RATES = 3;

    const NEW_VASTRECHT = 4.95;

    // If the estimate saving is larger then this amount, the deal is saved with has_saving = 1.
    const HAS_SAVING_PRICE = 50;

    protected $table = 'deals';

    /**
     * Calculate Costs
     *
     * @param int $iPriceCalculation
     * @param int $iYears
     * @param int $iMonths
     * @return float $fCosts
     */
    public function calculateCosts($aPrices,$iYears,$iMonths) {
        $fCosts = 0;

        switch ($this->calculation) {

            // Gas met een tarief normaal

            case CampaignPrice::CALCULATION_GAS_1_RATE:
                $new_price_gas = $aPrices['normal'];

                // Kosten = ((syu_gas * new_price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)
                $fCosts = (($this->syu_normal * $new_price_gas) * $iYears) + ($this->vastrecht * $iMonths);

                return $fCosts;

            // Energie met 2 tarieven (hoog en laag)

            case CampaignPrice::CALCULATION_ENERGY_2_RATES:
                $new_price_low = $aPrices['low'];
                $new_price_high = $aPrices['normal'];

                // Kosten = (((syu_low * new_price_low) + (syu_high * new_price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)
                $fCosts = ((($this->syu_low * $new_price_low) + ($this->syu_normal * $new_price_high)) * $iYears) + ($this->vastrecht * $iMonths);

                return $fCosts;

            // Energie met 3 tarieven (hoog, laag en enkel)

            case CampaignPrice::CALCULATION_ENERGY_3_RATES:
                $new_price_enkel = $aPrices['enkel'];

                // Kosten = ((syu_normal * new_price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)
                $fCosts = (($this->syu_normal * $new_price_enkel) * $iYears) + ($this->vastrecht * $iMonths);

                return $fCosts;
        }
    }

    /**
     * Calculate Saving
     *
     * @param int $iPriceCalculation
     * @param int $iYears
     * @param int $iMonths
     * @return float $fSaving
     */
    public function calculateSaving($aPrices,$iYears,$iMonths) {
        $fSaving = 0;

        switch ($this->calculation) {

            // Gas met een tarief normaal

            case CampaignPrice::CALCULATION_GAS_1_RATE:
                $new_price_gas = $aPrices['normal'];

                // Besparing = (((syu_gas * new_price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                //           - (((syu_gas * price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))

                $fCostsOld = ((($this->syu_normal * $new_price_gas) * $iYears) + ($this->vastrecht * $iMonths));
                $fCostsNew = ((($this->syu_normal * $this->price_normal) * $iYears) + ($this->vastrecht * $iMonths));
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                return $fSaving;

            // Energie met 2 tarieven (hoog en laag)

            case CampaignPrice::CALCULATION_ENERGY_2_RATES:
                $new_price_low = $aPrices['low'];
                $new_price_high = $aPrices['normal'];

                // Besparing = ((((syu_low * price_low) + (syu_high * price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                // - ((((syu_low * new_price_low) + (syu_high * new_price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))

                $fCostsOld = (((($this->syu_low * $this->price_low) + ($this->syu_normal * $this->price_normal)) * $iYears) + ($this->vastrecht * $iMonths));
                $fCostsNew = ((($this->syu_low * $new_price_low) + ($this->syu_normal * $new_price_high) * $iYears) + ($this->vastrecht * $iMonths));
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                return $fSaving;

            // Energie met 3 tarieven (hoog, laag en enkel)

            case CampaignPrice::CALCULATION_ENERGY_3_RATES:
                $new_price_enkel = $aPrices['enkel'];

                // Besparing = (((syu_normal * new_price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                //           - (((syu_normal * price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))

                $fCostsOld = ((($this->syu_normal * $new_price_enkel) * $iYears) + ($this->vastrecht * $iMonths));
                $fCostsNew = ((($this->syu_normal * $this->price_normal) * $iYears) + ($this->vastrecht * $iMonths));
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                return $fSaving;
        }
    }

    /**
     * @return bool true | false
     */
    public function isGas() {
        return ($this->type == self::TYPE_GAS ? true : false);
    }

    /**
     * @return bool true | false
     */
    public function isElektricity() {
        return ($this->type == self::TYPE_ELEKTRICITY ? true : false);
    }

    /**
     * Total Annual Consumption
     *
     * @return float
     */
    public function totalAnnualConsumption() {
        return $this->syu_normal + $this->syu_low;
    }

}
