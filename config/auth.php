<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users', // Основной провайдер - LDAP с fallback для поиска
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    // 'providers' => [
    //     'users' => [
    //         'driver' => 'eloquent',
    //         'model' => env('AUTH_MODEL', App\Models\User::class),
    //     ],



    // 'users' => [
    //     'driver' => 'database',
    //     'table' => 'users',
    // ],

    // 'providers' => [
    //     // ...

    //     'users' => [
    //         'driver' => 'ldap',
    //         'model' => LdapRecord\Models\ActiveDirectory\User::class,
    //         'rules' => [],
    //         'scopes' => [],
    //         'database' => [
    //             'model' => App\Models\User::class,
    //             'sync_passwords' => false,
    //             'sync_attributes' => [
    //                 'name' => 'cn',
    //                 // 'username' => 'mail',
    //                 'email' => 'mail',
    //             ],

    //         ],
    //     ],
    // ],

    'providers' => [
        // --- Провайдер LDAP (основной) ---
        // Он будет использоваться по умолчанию гардом 'web'.
        // LdapRecord будет пытаться аутентифицировать через LDAP.
        // Настройки 'database' здесь важны для синхронизации и fallback *поиска* пользователя.
        'users' => [
            'driver' => 'ldap',
            'model' => LdapRecord\Models\ActiveDirectory\User::class, // Убедитесь, что это правильная модель для вашего LDAP (AD, OpenLDAP и т.д.)
            'rules' => [], // Добавьте правила LDAP-аутентификации здесь, если нужно
            'scopes' => [], // Добавьте LDAP-скоупы здесь, если нужно
            'database' => [
                'model' => App\Models\User::class, // Ваша Eloquent модель пользователя
                'sync_passwords' => false, // Никогда не синхронизируйте пароли из LDAP!
                'sync_attributes' => [
                    'name' => 'cn',          // Атрибут LDAP для имени пользователя в БД
                    'email' => 'mail',       // Атрибут LDAP для email пользователя в БД
                    // 'guid' => 'objectguid', // Рекомендуется для надежной связи LDAP и БД
                ],
                // Опционально: Настройка fallback для поиска пользователя в БД, если он
                // аутентифицировался по LDAP, но LdapRecord не нашел его стандартным поиском.
                // 'fallback' => [
                //     'username' => 'email', // Поле в БД для поиска по введенному логину
                //     'password' => 'password', // Поле пароля в БД (НЕ используется для fallback аутентификации)
                // ],
            ],
        ],

        // --- Провайдер Eloquent (для ручной проверки локальной БД) ---
        // Назовем его 'db_users', чтобы не было конфликта имен.
        'db_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    // 'passwords' => [
    //     'users' => [
    //         'provider' => 'users',
    //         'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
    //         'expire' => 60,
    //         'throttle' => 60,
    //     ],
    // ],

    'passwords' => [
        'users' => [
            // Важно: Провайдер для сброса пароля должен указывать на Eloquent модель,
            // так как сброс пароля обычно происходит для локальных учетных записей.
            // Если 'users' - это ваш LDAP провайдер, создайте отдельный брокер
            // или убедитесь, что 'users' провайдер правильно настроен для поиска
            // пользователя в БД для сброса. Проще использовать 'db_users'.
            'provider' => 'db_users', // Используем провайдер для локальной БД
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
