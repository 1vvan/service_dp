<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Замовлення завершено</title>
    <style>
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            background-color: #f4f4f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            background: #f97316;
            color: #fff;
            padding: 24px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .body {
            padding: 28px 32px;
        }
        .body p {
            margin: 0 0 14px;
            line-height: 1.6;
            font-size: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 20px;
        }
        .info-table td {
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            color: #666;
            width: 40%;
        }
        .info-table td:last-child {
            font-weight: 600;
        }
        .total {
            font-size: 18px;
            font-weight: 700;
            color: #f97316;
            text-align: right;
            margin-top: 8px;
        }
        .footer {
            text-align: center;
            padding: 20px 32px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>AutoCare</h1>
                <p>Ваше замовлення завершено</p>
            </div>
            <div class="body">
                <p>Вітаємо, {{ $booking->client->full_name ?? 'шановний клієнт' }}!</p>
                <p>Ваше замовлення <strong>#{{ $booking->id }}</strong> успішно завершено. Чек у форматі PDF додано до цього листа.</p>

                <table class="info-table">
                    <tr>
                        <td>Автомобіль:</td>
                        <td>{{ $booking->car->full_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Держ. номер:</td>
                        <td>{{ $booking->car->license_plate ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Послуги:</td>
                        <td>{{ $booking->services->pluck('name')->join(', ') }}</td>
                    </tr>
                    <tr>
                        <td>Дата:</td>
                        <td>{{ $booking->date->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>

                <p class="total">Загалом: {{ number_format($booking->total_price, 2, ',', ' ') }} грн</p>
            </div>
            <div class="footer">
                <p>Дякуємо за вибір AutoCare!</p>
                <p>Цей лист надіслано автоматично.</p>
            </div>
        </div>
    </div>
</body>
</html>
