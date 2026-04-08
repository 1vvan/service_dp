<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запис підтверджено</title>
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
        .badge {
            display: inline-block;
            margin-top: 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 4px 16px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
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
            padding: 10px 0;
            font-size: 14px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .info-table td:first-child {
            color: #666;
            width: 38%;
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
        .note {
            background: #fff8f0;
            border-left: 3px solid #f97316;
            padding: 12px 16px;
            border-radius: 0 4px 4px 0;
            font-size: 14px;
            color: #555;
            margin-top: 20px;
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
                <p>Ваш запис підтверджено</p>
                <span class="badge">Запис #{{ $booking->id }}</span>
            </div>
            <div class="body">
                <p>Вітаємо, <strong>{{ $booking->client->full_name ?? 'шановний клієнт' }}</strong>!</p>
                <p>Ваш запис до автосервісу підтверджено. Чекаємо на вас у призначений час.</p>

                <table class="info-table">
                    <tr>
                        <td>Дата та час:</td>
                        <td>{{ $booking->date instanceof \Carbon\Carbon ? $booking->date->format('d.m.Y H:i') : $booking->date }}</td>
                    </tr>
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
                    @if($booking->master)
                    <tr>
                        <td>Майстер:</td>
                        <td>{{ $booking->master->full_name }}</td>
                    </tr>
                    @endif
                    @if($booking->description)
                    <tr>
                        <td>Коментар:</td>
                        <td>{{ $booking->description }}</td>
                    </tr>
                    @endif
                </table>

                <p class="total">Орієнтовна вартість: {{ number_format($booking->total_price, 2, ',', ' ') }} грн</p>

                <div class="note">
                    Якщо у вас виникнуть запитання або потреба перенести запис — зв'яжіться з нами заздалегідь.
                </div>
            </div>
            <div class="footer">
                <p>Дякуємо за вибір AutoCare!</p>
                <p>Цей лист надіслано автоматично.</p>
            </div>
        </div>
    </div>
</body>
</html>
