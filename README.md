# 🚀 Task Manager — Управление задачами с Kanban-доской

> Простая система управления задачами с поддержкой тегов, фильтрации, пагинации.

---

## 🧰 Технологии

- **Backend**: Laravel 12.x
- **Frontend**: Vanilla JavaScript + Tailwind CSS (без фреймворков)
- **База данных**: MySQL
- **Контейнеризация**: Docker + Laravel Sail
- **Кэширование**: Redis (опционально)
- **API**: OpenAPI 3.0 (Swagger) \ Postman \ Scribe

---

## ⚙️ Системные требования

- Docker Desktop (или Docker Engine + Docker Compose)
- PHP 8.1+ (в контейнере)
- Node.js 18+ (для сборки JS/CSS)
- Composer 2.5+

---

## 🚀 Быстрый старт (Sail)

### 1. Клонируйте репозиторий
```bash
git clone https://github.com/yourname/task-manager.git
cd task-manager
```

# Установите PHP-зависимости
```bash
composer install
```

# Установите JS-зависимости
```bash
npm install
```
# Настройте окружение
```bash
cp .env.example .env
```

# Запустите контейнеры через Sail
```bash
./vendor/bin/sail up -d
```
