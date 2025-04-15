# Вимоги до сервера та встановлення Laravel-проєкту

## Системні вимоги

### Операційна система

- Ubuntu 22.04 / Debian 12 / CentOS 9 / будь-який сумісний Linux-дистрибутив

### Необхідні компоненти

- **PHP 8.2+** з розширеннями:
  - `bcmath`
  - `ctype`
  - `fileinfo`
  - `json`
  - `mbstring`
  - `openssl`
  - `pdo`
  - `tokenizer`
  - `xml`
  - `zip`
  - `ldap`
- **Composer** (остання версія)
- **MySQL 8.0+** / **PostgreSQL 14+**
- **Redis** (якщо використовується кешування)
- **Node.js 18+** і **npm 9+** (для фронтенду)
- **Nginx** або **Apache** (веб-сервер)
- **Python 3.10+** із залежностями:
  - `tabula-py` (вимагає встановленого **Java 8+ JDK**)
  - `pandas`
  - `numpy`
  - `dbf`
  - `json`
  - `re`
  - `datetime`

## Встановлення

### 1. Клонування проєкту

```sh
git clone https://github.com/olerksandr-c/deposit-statement.git deposit-statement
cd deposit-statement
```

### 2. Встановлення залежностей

```sh
composer install
npm install && npm run dev
```

### 3. Встановлення Python-залежностей

```sh

pip install datetime tabula-py pandas numpy json jpype1 pyPDF2 dbf
```

### 4. Налаштування оточення

```sh
cp .env.example .env
php artisan key:generate
```

Налаштувати `.env`, вказавши параметри бази даних, кешу та черг.

### 5. Налаштування бази даних

```sh
php artisan migrate --seed
```

### 6. Налаштування прав

```sh
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Встановлення Java (JDK) для tabula-py

```sh
sudo apt install default-jdk  # Ubuntu/Debian
```

### 8. Налаштування веб-сервера

#### Nginx

```nginx
server {
    listen 80;
    server_name example.com;
    root /path/to/deposit-statement/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_index index.php;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Перезапуск Nginx:

```sh
systemctl restart nginx
```

### 10. Завершення встановлення

```sh
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Тепер проєкт доступний за вказаним доменом.

