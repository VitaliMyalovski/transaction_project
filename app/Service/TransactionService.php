<?php

namespace App\Service;

use App\Models\Transaction;
use DB;

class TransactionService
{
    private UserWalletService $userWalletService;
    private ExchangeRateService $exchangeRateService;
    private CurrencyService $currencyService;

    public function __construct(
        UserWalletService $userWalletService,
        ExchangeRateService $exchangeRateService,
        CurrencyService $currencyService
    ) {
        $this->userWalletService = $userWalletService;
        $this->exchangeRateService = $exchangeRateService;
        $this->currencyService = $currencyService;
    }

    /**
     * Процедура пополнения кошелька
     * @param int $userId ид пользователя
     * @param float $value кол-во денег
     */
    public function refill(int $userId, float $value): void
    {
        $characterUserWallet = $this->userWalletService->getUserCharacter($userId);

        $userQuotation = $this->exchangeRateService->getСurrentQuotation($characterUserWallet);

        $userWallet_id = $this->userWalletService->getUserWalletId($userId);

        DB::beginTransaction();
        $transactionId = $this->addTransaction($userId,
            $userId,
            $userWallet_id,
            $userWallet_id,
            $characterUserWallet,
            $characterUserWallet,
            $value,
            $value,
            10,
            $userQuotation,
            $userQuotation);

        $this->transactionProcessing($transactionId);

        DB::commit();
    }

    /**
     * Процедура регистрации транзакции
     * @param int $fromUserId ид отправителя
     * @param int $toUserId ид получателя
     * @param int $fromUserWalletId ид кошелька отправителя
     * @param int $toUserWalletId ид кошелька получателя
     * @param string $fromCharacter валюта отправителя
     * @param string $toCharacter валюта получателя
     * @param float $fromValue кол-во денег отправителя
     * @param float $toValue кол-во денег получателя
     * @param int $typeOper тип операции 10-пополнение 20-переод
     * @param float $fromQuotation котировка отправителя
     * @param float $toQuotation котировка получателя
     * @return int ид транзакции
     */
    public function addTransaction(
        int $fromUserId,
        int $toUserId,
        int $fromUserWalletId,
        int $toUserWalletId,
        string $fromCharacter,
        string $toCharacter,
        float $fromValue,
        float $toValue,
        int $typeOper,
        float $fromQuotation,
        float $toQuotation
    ): int {
        return Transaction::create([
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'from_user_wallet_id' => $fromUserWalletId,
            'to_user_wallet_id' => $toUserWalletId,
            'from_character' => $fromCharacter,
            'to_character' => $toCharacter,
            'from_value' => $fromValue,
            'to_value' => $toValue,
            'type_oper' => $typeOper,
            'from_quotation' => $fromQuotation,
            'to_quotation' => $toQuotation
        ])->id;
    }

    /**
     * Процедура перевода
     * @param int $fromUserId ид отправителя
     * @param int $toUserId ид получателя
     * @param string $characterFromUserWallet валюта отправителя
     * @param string $characterToUserWallet валюта получателя
     * @param float $fromUserQuotation котировка отправителя
     * @param float $toUserQuotation котировка получателя
     * @param float $value кол-во денег
     * @param string $whose_currency в чьей валюте будет перевод "from"-отправителя "to"-получателя
     * @return string результат обработки перевода
     */
    public function transfer(
        int $fromUserId,
        int $toUserId,
        string $characterFromUserWallet,
        string $characterToUserWallet,
        float $fromUserQuotation,
        float $toUserQuotation,
        float $value,
        string $whose_currency
    ): string {
        if ($whose_currency === 'from') {
            $fromValue = -$value;
            $toValue = $this->currencyService->convertCurrency($value, $fromUserQuotation, $toUserQuotation);
        } elseif ($whose_currency === 'to') {
            $fromValue = -$this->currencyService->convertCurrency($value, $toUserQuotation, $fromUserQuotation,);
            $toValue = $value;
        }

        $fromUserWallet_id = $this->userWalletService->getUserWalletId($fromUserId);
        $toUserWallet_id = $this->userWalletService->getUserWalletId($toUserId);

        //регистрация транзакции
        $transactionId = $this->addTransaction($fromUserId,
            $toUserId,
            $fromUserWallet_id,
            $toUserWallet_id,
            $characterFromUserWallet,
            $characterToUserWallet,
            $fromValue,
            $toValue,
            20,
            $fromUserQuotation,
            $toUserQuotation);

        $resultMessage = $this->transactionProcessing($transactionId);

        return $resultMessage;
    }

    /**
     * Процедура обработки транзакции
     * @param int $transactionId ид транзакции
     * @return string результат обработки транзакции
     */
    public function transactionProcessing(int $transactionId): string
    {
        $transactionData = Transaction::find($transactionId);

        if ($transactionData->type_oper === 10) {
            $this->userWalletService->updBalance($transactionData->to_user_id, $transactionData->to_value);
            $transactionData->is_done = 1;
        } elseif ($transactionData->type_oper === 20
            && $this->userWalletService->checkBalance($transactionData->from_user_id, $transactionData->from_value)) {
            $this->userWalletService->updBalance($transactionData->to_user_id, $transactionData->to_value);
            $this->userWalletService->updBalance($transactionData->from_user_id, $transactionData->from_value);
            $transactionData->is_done = 1;
        } else {
            $transactionData->is_done = -1;
            $transactionData->save();
            return 'Insufficient funds in the account';
        }

        $transactionData->save();
        return 'Transaction completed';
    }

    /**
     * @param int $user ид пользователя
     * @param string|null $dateFrom дата первой операции
     * @param string|null $dateTo дата последней операции
     * @param int|null $take ограничение по кол-ву записей
     * @return object возвращает коллекцию со списком транзакций
     */
    public function getAlltransactionForUser(?int $userId, string $dateFrom = null, string $dateTo = null, int $take=null): object
    {
        $data = DB::table('users as u')
            ->join('transactions as t', function ($join) {
                $join->whereColumn('u.user_id', 't.from_user_id');
                $join->orWhereColumn('u.user_id', 't.to_user_id');
            })
            ->where('u.user_id', '=', $userId)
            ->where('t.is_done', 1)
            ->orderBy('t.created_at')
            ->select(['t.created_at'])
            ->selectSub('case when u.user_id=t.from_user_id then t.from_value else t.to_value end', 'value')
            ->selectSub('case when type_oper=10 then \'Пополнение\' when type_oper=20 then \'Перевод\' end',
                'type_oper')
            ->selectSub('case when u.user_id=t.from_user_id then t.from_quotation else t.to_quotation end',
                'quotation');

        if (isset($dateFrom)) {
            $data = $data->whereDate('t.created_at', '>=', $dateFrom);
        }
        if (isset($dateTo)) {
            $data = $data->whereDate('t.created_at', '<=', $dateTo);
        }
        if (isset($take)) {
            $data = $data->take($take);
        }

        $data = $data->get();
        return $data;
    }

}
