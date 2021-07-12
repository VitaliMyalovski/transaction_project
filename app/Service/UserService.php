<?php


namespace App\Service;


use App\Models\User;

class UserService
{
    /**
     * Метод выполняющий регистрацию пользователя
     * @param string $name ФИО
     * @param string $country Страна
     * @param string $cityOfRegistration Город проживания
     * @return int идентификатор пользователя
     */
    public function registerUser(string $name, string $country, string $cityOfRegistration): int
    {
        $userId = User::create([
            'name' => $name,
            'country' => $country,
            'city_of_registration' => $cityOfRegistration
        ])->user_id;

        return $userId;
    }

    /**
     * Метод проверяющий существование пользователя
     * @param string $name ФИО
     * @param string $country Страна
     * @param string $cityOfRegistration Город проживания
     * @return int
     */
    public function getUserId(string $name, string $country, string $cityOfRegistration): ?int
    {
        $checkBool = User::where([
                ['name', $name],
                ['country', $country],
                ['city_of_registration', $cityOfRegistration]
            ])->first()->user_id ?? null;

        return $checkBool;
    }
}
