﻿Проект разрабатывался на следующих версиях ПО:
Laravel Framework 8.49.2
PHP 7.4.9
Composer 2.0.13
Node js 14.17.3
MySQL 5.7.31

Команды требуемые для развертки проекта:
composer i
php artisan migrate
php artisan serve

Настройки БД, которые использовались в проекте:
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=transaction_project
DB_USERNAME=vitalikdb
DB_PASSWORD=12345678

Фреймворк Laravel и PHP были выбраны, т.к. являются более знакомыми для разработки и способны справится с задачей. 
Была выбрана бесплатная БД MySQL, которая поставляется с большинством ПО настройке локальных серверов 
и она проста в развертке.  
Текущая БД должна справится с поставленной нагрузкой, т.к. в последних версиях БД были сняты 
ограничения по объему данных. Так же в процессе работы проекта используется малое кол-во таблиц.  

Описание назначения таблиц БД:
nss_currencies-справочник возможных валют, с их названиями и кодами
exchange_rates-таблица для хранения котировок валют на каждый день
users-таблица с пользователями (уникальность пользователя по имени, стране и городу регистрации)
user_wallets-таблица с кошельком пользователей
transactions-таблица с всеми транзакциями(type_oper:10-пополнение,20-перевод)(таблица избыточна
 для построения максимально быстрых отчетов)
Все выборки происходят по PK/FK/index

Структура основных файлов проекта:
app/Http/Controllers/Api-контроллеры, которые отвечают за обработку вызовов API
app/Http/Controllers/Reports-контроллер отвечающий за отображение отчета 
app/Http/Requests-правила валидации запроса
app/Service-бизнес логика проекта

Описание API (Все поля в API являются обязательными для заполнения):
    1. Регистрация пользователя и его кошелька
        api/user/register
        user-массив с идентифицирующими пользователя параметрами
        name-имя пользователя
        country-страна пользователя
        city_of_registration-город регистрации
        character-буквеный код валюты кошелька(список доступных валют есть в nss_currencies)
        Пример тела запроса:
        {
            "user": {
                "name": "Иван Лыньков",
                "country": "Россия",
                "city_of_registration": "Санкт-Питербург",
                "character":"RUB"
            }
        }
    
    2. Загрузка котировки валюты к USD на дату
        api/quotation/upload
        exchange_rates-массив котировок
        character-буквеный код валюты
        quotation-котировка валюты к USD
        on_date-на какую дату вносится котировка
        Пример тела запроса:
        {
            "exchange_rates": 
                [
                    {"character":"RUB","quotation":0.013447,"on_date":"2021-07-12"},
                    {"character":"USD","quotation":1,"on_date":"2021-07-12"},
                    {"character":"EUR","quotation":1.187625,"on_date":"2021-07-12"}
                ]
        }
    
    3. Пополнение кошелька
        api/transaction/refill
        user-массив с идентифицирующими пользователя параметрами
        name-имя пользователя
        country-страна пользователя
        city_of_registration-город регистрации
        value-количество денег вносимое на кошелек
        Пример тела запроса:
        {
            "user": {
                "name": "Иван Лыньков",
                "country": "Россия",
                "city_of_registration": "Санкт-Питербург",
                "character":"RUB"
            },
            "value": 1000
        }
        
    4. Перевод денег с кошелька одного пользователя на другой
       api/transaction/transfer
       from_user/to_user-массивы с идентифицирующие пользователей отправителя/получателя перевода
       name-имя пользователя
       country-страна пользователя
       city_of_registration-город регистрации
       value-количество денег вносимое на кошелек
       whose_currency-выбор валюты при переводе, может принимать два значения from-валюта отправителя/to-валюта получателя
       Пример тела запроса:
       {
           "from_user": {
               "name": "Иван Лыньков",
               "country": "Россия",
               "city_of_registration": "Санкт-Питербург",
               "character":"RUB"
           },
        "to_user": {
               "name": "Алексей Шлюпкин",
               "country": "Казахстан",
               "city_of_registration": "Нурсултан"
           },
           "value": 10,
           "whose_currency":"from"
       }
   
Отчет:
http://127.0.0.1:8000/reports/transaction
Работа отчета ограничена 100000 транзакциями(не знаю хорошего метода отрисовки, кроме использования сторонних библиотек)
Загрузка происходит за 871 ms при 12582915 транзакциях у пользователя, при выборке без учета даты транзакции
Реализована выгрузка данных в CSV 

    


