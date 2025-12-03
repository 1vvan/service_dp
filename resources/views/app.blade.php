<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AutoCare - Професійний Автосервіс</title>
    <meta name="description" content="Професійне обслуговування та ремонт автомобілів. Записуйтесь на прийом, відстежуйте історію обслуговування та підтримуйте свій автомобіль у відмінному стані." />


    <!-- Styles -->
    @vite(['resources/scss/main.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>

