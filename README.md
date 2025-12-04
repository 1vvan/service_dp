# Service DP

Проект на Laravel с Vue.js, Vue Router и Vuex.

## Стек технологий

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3
- **Роутинг**: Vue Router 4
- **Управление состоянием**: Vuex 4
- **Сборщик**: Vite
- **Стили**: Tailwind CSS

## Установка и запуск

### Требования

- PHP >= 8.2
- Composer
- Node.js >= 20.19.0 (рекомендуется)
- npm или yarn

### Шаги установки

1. Установите зависимости PHP:
```bash
composer install
```

2. Установите зависимости Node.js:
```bash
npm install
```

3. Скопируйте файл окружения:
```bash
cp .env.example .env
```

4. Сгенерируйте ключ приложения:
```bash
php artisan key:generate
```

5. Настройте базу данных в файле `.env` (опционально)

6. Запустите миграции (если используете базу данных):
```bash
php artisan migrate
```

### Запуск в режиме разработки

1. Запустите Laravel сервер разработки:
```bash
php artisan serve
```

2. В другом терминале запустите Vite для сборки фронтенда:
```bash
npm run dev
```

Приложение будет доступно по адресу: `http://localhost:8000`

### Сборка для продакшена

```bash
npm run build
```