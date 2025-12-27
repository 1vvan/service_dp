# Настройка Stripe для оплаты букингов

## Шаги настройки

### 1. Создание аккаунта Stripe

1. Зарегистрируйтесь на [https://stripe.com](https://stripe.com)
2. Перейдите в Dashboard → Developers → API keys
3. Скопируйте **Test mode keys** (для разработки) или **Live mode keys** (для продакшена)

### 2. Настройка переменных окружения

Добавьте в файл `.env`:

```env
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_... # Для webhook (см. шаг 3)
APP_FRONTEND_URL=http://localhost:8000
```

**Для тестирования используйте тестовые ключи:**
- `sk_test_...` - Secret key (тестовый)
- `pk_test_...` - Publishable key (тестовый)

### 3. Настройка Webhook (для локального тестирования)

#### Вариант 1: Использование Stripe CLI (рекомендуется для разработки)

1. Установите Stripe CLI: [https://stripe.com/docs/stripe-cli](https://stripe.com/docs/stripe-cli)
2. Авторизуйтесь:
   ```bash
   stripe login
   ```
3. Запустите туннель для webhook:
   ```bash
   stripe listen --forward-to localhost:8000/api/payments/webhook/stripe
   ```
4. Скопируйте `whsec_...` ключ из вывода команды
5. Добавьте его в `.env` как `STRIPE_WEBHOOK_SECRET`

#### Вариант 2: Использование ngrok (альтернатива)

1. Установите ngrok: [https://ngrok.com](https://ngrok.com)
2. Запустите туннель:
   ```bash
   ngrok http 8000
   ```
3. В Stripe Dashboard → Developers → Webhooks добавьте endpoint:
   - URL: `https://your-ngrok-url.ngrok.io/api/payments/webhook/stripe`
   - События: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`
4. Скопируйте Signing secret из созданного webhook

### 4. Тестовые карты Stripe

Используйте следующие тестовые карты для проверки:

**Успешная оплата:**
- Номер: `4242 4242 4242 4242`
- Дата: любая будущая (например, 12/25)
- CVC: любые 3 цифры (например, 123)
- ZIP: любые 5 цифр

**Неуспешная оплата:**
- Номер: `4000 0000 0000 0002` (карта отклонена)

**Требует 3D Secure:**
- Номер: `4000 0025 0000 3155`

Полный список тестовых карт: [https://stripe.com/docs/testing](https://stripe.com/docs/testing)

### 5. Проверка работы

1. Запустите Laravel сервер:
   ```bash
   php artisan serve
   ```

2. Создайте букинг в системе

3. В таблице букингов нажмите "Оплатити" в дропдауне действий

4. Вас перенаправит на Stripe Checkout

5. Используйте тестовую карту `4242 4242 4242 4242`

6. После успешной оплаты вы вернетесь на страницу букингов

### 6. Проверка статуса оплаты

- После успешной оплаты webhook обновит статус транзакции в БД
- Кнопка "Оплатити" исчезнет для оплаченных букингов
- Проверить транзакции можно в таблице `payment_transactions`

### 7. Переход на продакшен

Когда будете готовы к продакшену:

1. Получите Live mode keys из Stripe Dashboard
2. Обновите `.env`:
   ```env
   STRIPE_SECRET_KEY=sk_live_...
   STRIPE_PUBLIC_KEY=pk_live_...
   ```
3. Настройте webhook в Stripe Dashboard на ваш продакшен домен
4. Обновите `APP_FRONTEND_URL` на продакшен URL

## API Endpoints

- `POST /api/bookings/{booking}/payment/create-session` - Создание сессии оплаты (требует авторизации)
- `POST /api/payments/webhook/stripe` - Webhook для обработки событий Stripe (без авторизации)

## Структура базы данных

Транзакции сохраняются в таблице `payment_transactions`:
- `booking_id` - ID букинга
- `amount` - Сумма оплаты
- `payment_method` - Метод оплаты (stripe)
- `payment_status` - Статус (pending, completed, failed)
- `transaction_id` - ID сессии Stripe

