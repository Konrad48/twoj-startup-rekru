# Zadanie rekrutacyjne Twój Startup

## Wymagania

- PHP 8.2+
- Composer

## Instalacja i konfiguracja
1. Sklonuj repozytorium:
```bash
git clone [adres-twojego-repozytorium]
cd [katalog-projektu]
```

2. Zainstaluj zależności:
```bash
composer install
```

3. Skonfiguruj plik `.env` na podstawie `.env.example`.
Dla uproszczenia, użyłem bazy danych SQLite, ale można użyć dowolnej innej

Aby możliwa była wysyłka maili należy wprowadzić dane serwera SMTP:
```bash
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

lub pozostawić domyślne ustawienia, które pozwolą na wysyłanie maili do konsoli. Będą one zapisywane wtedy w pliku `storage/logs/laravel.log`.

5. Uruchom migracje:
```bash
php artisan migrate
```

6. Uruchom serwer:
```bash
php artisan serve
```

## Autentykacja
Wykorzystałem Laravel Sanctum do autentykacji. Token autentykacji należy dołączyć w nagłówku Authorization:
`Authorization: Bearer {twoj-token}`

## Dokumentacja API
Projekt wykorzystuje Scramble do dokumentacji API. Po uruchomieniu projektu, dokumentacja jest dostępna pod adresem: http://127.0.0.1:8000/docs/api#

Umożliwia ona przeglądanie dostępnych endpointów i testowanie API bezpośrednio z przeglądarki bez konieczności używania narzędzi takich jak Postman.

## Testy

Testy jednostkowe zostały napisane z wykorzystaniem PHPUnit dla obydwu kontrolerów. Uruchomienie testów:
```bash
php artisan test
```
