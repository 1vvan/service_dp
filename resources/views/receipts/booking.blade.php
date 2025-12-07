<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чек запису #{{ $booking->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
            position: relative;
        }

        .header-logo {
            position: absolute;
            top: 13px;
            left: 20px;

            font-size: 1.25rem;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: #333;
        }
        
        .header-logo .highlight {
            color: #f97316;
        }
        
        .header h1 {
            color: #f97316;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .info-block {
            flex: 1;
        }
        
        .info-block h3 {
            color: #f97316;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-block p {
            margin: 5px 0;
            font-size: 12px;
        }
        
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .services-table thead {
            background-color: #f97316;
            color: white;
        }
        
        .services-table th,
        .services-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .services-table th {
            font-weight: bold;
            font-size: 12px;
        }
        
        .services-table td {
            font-size: 11px;
        }
        
        .services-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        
        .total-row.final {
            font-size: 16px;
            font-weight: bold;
            color: #f97316;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .calculation-details {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff9f0;
            border-left: 4px solid #f97316;
            font-size: 11px;
        }
        
        .calculation-details h4 {
            color: #f97316;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .calculation-details p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="header-logo">
                Auto<span class="highlight">Care</span>
            </div>

            <h1>ЧЕК ЗАПИСУ НА СЕРВІС</h1>
            <p>Номер запису: #{{ $booking->id }}</p>
            <p>Дата створення: {{ $booking->created_at->format('d.m.Y H:i') }}</p>
        </div>
        
        <div class="receipt-info">
            <div class="info-block">
                <h3>Інформація про клієнта</h3>
                <p><strong>ПІБ:</strong> {{ $booking->client->full_name ?? 'Не вказано' }}</p>
                <p><strong>Email:</strong> {{ $booking->client->email ?? 'Не вказано' }}</p>
                <p><strong>Телефон:</strong> {{ $booking->client->phone ?? 'Не вказано' }}</p>
            </div>
            
            <div class="info-block">
                <h3>Інформація про автомобіль</h3>
                <p><strong>Автомобіль:</strong> {{ $booking->car->full_name ?? 'Не вказано' }}</p>
                <p><strong>Рік випуску:</strong> {{ $booking->car->car_year ?? 'Не вказано' }}</p>
                <p><strong>Держ. номер:</strong> {{ $booking->car->license_plate ?? 'Не вказано' }}</p>
            </div>
        </div>
        
        <div class="info-block" style="margin-bottom: 20px;">
            <h3>Деталі запису</h3>
            <p><strong>Дата та час:</strong> {{ $booking->date->format('d.m.Y H:i') }}</p>
            <p><strong>Статус:</strong> {{ $booking->status_name }}</p>
            @if($booking->description)
            <p><strong>Коментар:</strong> {{ $booking->description }}</p>
            @endif
        </div>
        
        <h3 style="color: #f97316; margin-bottom: 10px; font-size: 14px;">Послуги</h3>
        <table class="services-table">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Назва послуги</th>
                    <th>Вартість</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($priceCalculation['services']) && count($priceCalculation['services']) > 0)
                    @foreach($priceCalculation['services'] as $index => $service)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $service['name'] }}</td>
                        <td><strong>{{ number_format($service['final_price'], 2, ',', ' ') }} грн</strong></td>
                    </tr>
                    @endforeach
                @else
                    @foreach($booking->services as $index => $service)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $service->name }}</td>
                        <td><strong>{{ number_format($service->base_price, 2, ',', ' ') }} грн</strong></td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        
        <div class="total-section">
            <div class="total-row final">
                <span>ЗАГАЛЬНА ВАРТІСТЬ:</span>
                <span>{{ number_format($booking->total_price, 2, ',', ' ') }} грн</span>
            </div>
        </div>
        
        <div class="footer">
            <p>Дякуємо за вибір нашого сервісу!</p>
            <p>Цей документ є підтвердженням запису на сервіс</p>
        </div>
    </div>
</body>
</html>

