<?php


namespace App\Service;


class CurrencyService
{
    /**
     * Процедура конвертации из одной валюты в другую
     * @param float $value кол-во денег
     * @param float $fromQuotation котировка отправителя
     * @param float $toQuotation котировка получателя
     * @return float конвертированное кол-во денег
     */
    public function convertCurrency(
        float $value,
        float $fromQuotation,
        float $toQuotation
    ): float {
        $usdValue = $this->convertToUsd($value, $fromQuotation);

        return round($usdValue / $toQuotation, 2);
    }

    /**
     * Процедура конвертации из любой валюты в доллары
     * @param float $value кол-во денег
     * @param float $fromQuotation котировка отправителя
     * @return float конвертированное кол-во денег
     */
    public function convertToUsd(float $value, float $fromQuotation): float
    {
        return $value * $fromQuotation;
    }
}
