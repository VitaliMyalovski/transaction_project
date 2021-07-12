<?php


namespace App\Service;


use App\Models\ExchangeRate;

class ExchangeRateService
{
    /**
     * @param array $exchangeRates массив с котировками к на день
     * пример массива:[
     *                  ["character":"RUB","quotation":0.013447,"on_date":"2021-07-11"],
     *                  ["character":"USD","quotation":1,"on_date":"2021-07-11"]
     *                ]
     */
    public function upload(array $exchangeRates): void
    {
        ExchangeRate::upsert($exchangeRates, ['character', 'on_date'], null);
    }

    /**
     * Процедура получения котировки валютый на текущий день
     * @param $character буквенный код валюты
     * @return float|null котировка
     */
    public function getСurrentQuotation($character): ?float
    {
        return ExchangeRate::where('character', $character)
                ->whereDate('on_date', date("Y-m-d"))->first()->quotation ?? null;
    }
}
