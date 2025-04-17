<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Добавить импорт Hash
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\User; // Добавить импорт модели User
use LdapRecord\Auth\BindException; // Для отлова ошибок аутентификации LDAP
use LdapRecord\ConnectionException; // Для отлова ошибок соединения с LDAP

class LoginForm extends Form
{
    // Используем 'email' для ясности, т.к. поле ввода - email
    #[Validate('required|string|email')]
    public string $email = ''; // Переименовано с username

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials using LDAP or Database.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // --- Попытка 1: Аутентификация через LDAP ---
        $ldapAuthenticated = false;
        try {
            // Используем стандартный Auth::attempt(). Он использует гвард 'web',
            // который по умолчанию использует провайдер 'users' (наш LDAP провайдер).
            // LdapRecord попытается найти пользователя по 'mail' атрибуту в LDAP.
            if (Auth::attempt(['mail' => $this->email, 'password' => $this->password], $this->remember)) {
                $ldapAuthenticated = true;
                // Успешная аутентификация через LDAP.
                // LdapRecord автоматически синхронизирует пользователя в БД (если настроено).
            }
        } catch (BindException $e) {
            // Ошибка привязки LDAP (например, неверные учетные данные).
            // Это ожидаемая ошибка, если пользователь пытается войти с локальным паролем.
            // Просто продолжаем, чтобы проверить локальную БД.
            info("LDAP bind failed for user {$this->email}. Trying database fallback.");
        } catch (ConnectionException $e) {
            // Ошибка соединения с сервером LDAP.
            // В этом случае fallback на локальную БД может быть нежелателен.
            // Лучше сообщить пользователю о проблеме с LDAP.
            RateLimiter::hit($this->throttleKey());
            info("LDAP connection failed: " . $e->getMessage());
            throw ValidationException::withMessages([
                'form.email' => __('auth.ldap_error', ['message' => $e->getMessage()]), // Создайте этот ключ в файлах перевода
                 // Или более общее сообщение: 'form.email' => __('auth.failed'),
            ]);
        } // Можно добавить другие catch для специфических ошибок LDAP

        if ($ldapAuthenticated) {
            // Успех через LDAP
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // --- Попытка 2: Аутентификация через Локальную Базу Данных ---
        // Ищем пользователя в локальной БД по email
        $user = User::where('email', $this->email)->first();

        // Проверяем, найден ли пользователь и совпадает ли хеш пароля
        // Важно: Убедитесь, что у локальных пользователей поле password не пустое и содержит хеш.
        if ($user && $user->password && Hash::check($this->password, $user->password)) {
            // Успешная аутентификация через локальную БД.
            // Вручную логиним пользователя.
            Auth::login($user, $this->remember);
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // --- Если обе попытки не удались ---
        RateLimiter::hit($this->throttleKey());
        info("Authentication failed for {$this->email} via both LDAP and Database."); // Логируем неудачу
        throw ValidationException::withMessages([
            // Используем 'form.email', так как поле ввода связано с $this->email
            'form.email' => __('auth.failed'),
        ]);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            // Используем 'form.email'
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        // Используем email для ключа
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}
