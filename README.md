# API Task Management
API для управления задачами с CRUD функционалом.

# Стек:
1. Laravel 12
2. PHP 8.4
3. MySQL
4. Redis (для кэширования и сессий)
5. Docker (через Laravel SAIL)

# Установка и запуск

1. Клонирование репозитория
``` bash
git clone <repository-url>
cd <project-directory>
```
2. Установка зависимостей
``` bash
# Используя Laravel SAIL
sail composer install
```
``` bash
# Или если Docker не используется
composer install
```

2. Запуск

``` bash
# если Docker не используется
php artisan key:generate
php artisan migrate
```

``` bash
# Используя Laravel SAIL
./vendor/bin/sail up # добавить -d если нужно запустить фоном

# Или если Docker не используется
php artisan serve
```

