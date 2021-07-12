<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangeRateRequest;
use App\Service\ExchangeRateService;

class ExchangeRateController extends Controller
{
    private ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function upload(ExchangeRateRequest $request)
    {
        $exchangeRates = $request->validated()['exchange_rates'];

        $this->exchangeRateService->upload($exchangeRates);

        return response()->json(['message' => 'Loading is complete'], 201);
    }
}
