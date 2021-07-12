<?php


namespace App\Service;


use App\Models\UserWallet;

class UserWalletService
{
    public function registerWallet(int $userId, string $character): void
    {
        UserWallet::create([
            'user_id' => $userId,
            'character' => $character,
        ]);
    }

    public function getUserCharacter($userId): ?string
    {
        return UserWallet::where('user_id', $userId)->first()->character ?? null;
    }

    public function getUserWalletId($userId): int
    {
        return UserWallet::where('user_id', $userId)->first()->wallet_id;
    }

    public function updBalance(int $userId, float $value): void
    {
        $userWallet = UserWallet::where('user_id', $userId)->first();
        $userWallet->balance += $value;
        $userWallet->save();
    }

    public function checkBalance(int $userId, float $value): bool
    {
        $userWallet = UserWallet::where('user_id', $userId)->first();
        if ($userWallet->balance + $value >= 0) {
            return true;
        } else {
            return false;
        }
    }


}
