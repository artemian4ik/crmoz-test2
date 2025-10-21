<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoho CRM - Успішна інтеграція</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .success-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .success-title {
            color: #333;
            margin-bottom: 15px;
        }
        .success-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1 class="success-title">Інтеграція успішна!</h1>
        <p class="success-message">{{ $message }}</p>
        <a href="{{ route('zoho.integration.status') }}" class="btn">Переглянути статус</a>
    </div>
</body>
</html>
