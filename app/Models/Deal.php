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

    protected $table = 'deals';

    /**
     * Calculate Costs
     *
     * @param array $aCampaignPrices
     * @return float $fCosts
     */
    public function calculateCosts($aCampaignPrices) {
        $fCosts = 0;

        $iYears = $aCampaignPrices['years'];
        $iMonths = $aCampaignPrices['years'] * 12;
        $iCalculation = $aCampaignPrices['calculation'];
        $iPercentage = $aCampaignPrices['percentage'];

        switch ($iCalculation) {

            // Gas met een tarief normaal

            case CampaignPrice::CALCULATION_GAS_1_RATE:
                $new_price_gas = $aCampaignPrices['normal'] / 100;

                // Kosten = ((syu_gas * new_price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd) + Prijs opslag percentage
                $fCosts = (($this->syu_normal * $new_price_gas) * $iYears) + ($this->new_vastrecht * $iMonths);
                $fCosts += ($fCosts / 100) * $iPercentage;

                break;

            // Energie met 2 tarieven (hoog en laag)

            case CampaignPrice::CALCULATION_ENERGY_2_RATES:
                $new_price_low = $aCampaignPrices['low'] / 100;
                $new_price_high = $aCampaignPrices['normal'] / 100;

                // Kosten = (((syu_low * new_price_low) + (syu_high * new_price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd) + Prijs opslag percentage
                $fCosts = ((($this->syu_low * $new_price_low) + ($this->syu_normal * $new_price_high)) * $iYears) + ($this->new_vastrecht * $iMonths);
                $fCosts += ($fCosts / 100) * $iPercentage;

                break;

            // Energie met 3 tarieven (hoog, laag en enkel)

            case CampaignPrice::CALCULATION_ENERGY_3_RATES:
                $new_price_enkel = $aCampaignPrices['enkel'] / 100;

                // Kosten = ((syu_normal * new_price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd) + Prijs opslag percentage
                $fCosts = (($this->syu_normal * $new_price_enkel) * $iYears) + ($this->new_vastrecht * $iMonths);
                $fCosts += ($fCosts / 100) * $iPercentage;

                break;
        }

        return $fCosts;
    }

    /**
     * Calculate Saving
     *
     * @param array $aCampaignPrices
     * @return float $fSaving
     */
    public function calculateSaving($aCampaignPrices) {
        $fSaving = 0;

        $iYears = $aCampaignPrices['years'];
        $iMonths = $aCampaignPrices['years'] * 12;
        $iCalculation = $aCampaignPrices['calculation'];
        $iPercentage = $aCampaignPrices['percentage'];

        switch ($iCalculation) {

            // Gas met een tarief normaal

            case CampaignPrice::CALCULATION_GAS_1_RATE:
                $new_price_gas = $aCampaignPrices['normal'] / 100;

                // Besparing = (((syu_gas * new_price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                //           - (((syu_gas * price_gas) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)) + Prijs opslag percentage

                $fCostsOld = ((($this->syu_normal * $this->price_normal) * $iYears) + ($this->vastrecht * $iMonths));
                $fCostsNew = ((($this->syu_normal * $new_price_gas) * $iYears) + ($this->new_vastrecht * $iMonths));
                $fCostsNew += ($fCostsNew / 100) * $iPercentage;
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                break;

            // Energie met 2 tarieven (hoog en laag)

            case CampaignPrice::CALCULATION_ENERGY_2_RATES:
                $new_price_low = $aCampaignPrices['low'] / 100;
                $new_price_high = $aCampaignPrices['normal'] / 100;

                // Besparing = ((((syu_low * price_low) + (syu_high * price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                // - ((((syu_low * new_price_low) + (syu_high * new_price_high)) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)) + Prijs opslag percentage

                $fCostsOld = (((($this->syu_low * $this->price_low) + ($this->syu_normal * $this->price_normal)) * $iYears) + ($this->vastrecht * $iMonths));
                $fCostsNew = ((($this->syu_low * $new_price_low) + ($this->syu_normal * $new_price_high) * $iYears) + ($this->new_vastrecht * $iMonths));
                $fCostsNew += ($fCostsNew / 100) * $iPercentage;
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                break;

            // Energie met 3 tarieven (hoog, laag en enkel)

            case CampaignPrice::CALCULATION_ENERGY_3_RATES:
                $new_price_enkel = $aCampaignPrices['enkel'] / 100;

                // Besparing = (((syu_normal * new_price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd))
                //           - (((syu_normal * price_enkel) * aantal jaren in looptijd) + (vastrecht * aantal maanden in looptijd)) + Prijs opslag percentage

                $fCostsOld = ((($this->syu_normal * $this->price_normal) * $iYears) + ($this->vastrecht * $iMonths));
                $fCostsNew = ((($this->syu_normal * $new_price_enkel) * $iYears) + ($this->new_vastrecht * $iMonths));
                $fCostsNew += ($fCostsNew / 100) * $iPercentage;
                $fSaving = $fCostsOld - $fCostsNew;

                if ($fSaving < 0) {
                    $fSaving = 0;
                }

                break;
        }

        return $fSaving;
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
