<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Service\ExchangeRateService;
use App\Service\TransactionService;
use App\Service\UserService;
use App\Service\UserWalletService;

class TransactionController extends Controller
{
    private UserService $userService;
    private UserWalletService $userWalletService;
    private ExchangeRateService $exchangeRateService;
    private TransactionService $transactionService;

    public function __construct(
        UserService $userService,
        UserWalletService $userWalletService,
        ExchangeRateService $exchangeRateService,
        TransactionService $transactionService
    ) {
        $this->userService = $userService;
        $this->userWalletService = $userWalletService;
        $this->exchangeRateService = $exchangeRateService;
        $this->transactionService = $transactionService;
    }

    //процедура обрабатывающая пополнение счета
    public function refill(TransactionRequest $request)
    {
        $requestArray = $request->validated();

        $userId = $this->userService->getUserId($requestArray['user']['name'], $requestArray['user']['country'],
            $requestArray['user']['city_of_registration']);

        //Проверка на существование пользователя
        if (!$userId) {
            return response()->json([
                'message' => 'Unprocessable Entity',
                'errors' => 'User does not exist'
            ], 422);
        }

        $characterUserWallet = $this->userWalletService->getUserCharacter($userId);
        $UserQuotation = $this->exchangeRateService->getСurrentQuotation($characterUserWallet);
//      Проверка на наличие котировок
        if (!$UserQuotation) {
            return response()->json([
                'message' => 'Unprocessable Entity',
                'errors' => 'One of the currency quotes is currently missing'
            ], 422);
        }

        $this->transactionService->refill($userId, $requestArray['value']);

        return response()->json(['message' => 'Wallet replenished'], 200);
    }

    //процедура обрабатывающая переводы
    public function transfer(TransactionRequest $request)
    {
        $requestArray = $request->validated();

        $fromUserId = $this->userService->getUserId($requestArray['from_user']['name'],
            $requestArray['from_user']['country'],
            $requestArray['from_user']['city_of_registration']);
        $toUserId = $this->userService->getUserId($requestArray['to_user']['name'],
            $requestArray['to_user']['country'],
            $requestArray['to_user']['city_of_registration']);

        //Проверка на существование пользователя
        if (!$fromUserId || !$toUserId) {
            return response()->json([
                'message' => 'Unprocessable Entity',
                'errors' => 'User does not exist'
            ], 422);
        }

        //Проверка на одного пользователя
        if ($fromUserId == $toUserId) {
            return response()->json([
                'message' => 'Unprocessable Entity',
                'errors' => 'Users must be different'
            ], 422);
        }

        $characterFromUserWallet = $this->userWalletService->getUserCharacter($fromUserId);
        $characterToUserWallet = $this->userWalletService->getUserCharacter($toUserId);
        $fromUserQuotation = $this->exchangeRateService->getСurrentQuotation($characterFromUserWallet);
        if ($characterFromUserWallet === $characterToUserWallet) {
            $toUserQuotation = $fromUserQuotation;
        } else {
            $toUserQuotation = $this->exchangeRateService->getСurrentQuotation($characterToUserWallet);
        }
//      Проверка на наличие котировок
        if (!$fromUserQuotation || !$toUserQuotation) {
            return response()->json([
                'message' => 'Unprocessable Entity',
                'errors' => 'One of the currency quotes is currently missing'
            ], 422);
        }

        $resultMessage = $this->transactionService->transfer($fromUserId, $toUserId, $characterFromUserWallet,
            $characterToUserWallet,
            $fromUserQuotation, $toUserQuotation, $requestArray['value'], $requestArray['whose_currency']);

        return response()->json(['message' => $resultMessage], 200);
    }
}
