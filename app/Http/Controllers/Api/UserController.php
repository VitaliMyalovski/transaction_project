<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\UserService;
use App\Service\UserWalletService;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    private UserService $userService;
    private UserWalletService $userWalletService;

    public function __construct(UserService $userService, UserWalletService $userWalletService)
    {
        $this->userService = $userService;
        $this->userWalletService = $userWalletService;
    }

    //процедура обрабатывающая регистрацию пользователя с кошельком
    public function register(UserRequest $request)
    {
        $userArray = $request->validated()['user'];

        $userId = $this->userService->getUserId($userArray['name'], $userArray['country'],
            $userArray['city_of_registration']);

        //Проверка на существование пользователя
        if ($userId) {
            return response()->json([
                'message' => 'Unprocessable Entity',
                'errors' => 'This user already exists'
            ], 422);
        }

        $userId = $this->userService->registerUser($userArray['name'], $userArray['country'],
            $userArray['city_of_registration']);

        $this->userWalletService->registerWallet($userId, $userArray['character']);

        return response()->json(['message' => 'User is registered'], 201);
    }
}
