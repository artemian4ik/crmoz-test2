<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoho CRM - Помилка інтеграції</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .error-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .error-icon {
            color: #f44336;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .error-title {
            color: #333;
            margin-bottom: 15px;
        }
        .error-message {
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
            margin: 0 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">✗</div>
        <h1 class="error-title">Помилка інтеграції</h1>
        <p class="error-message">{{ $error }}</p>
        <div>
            <a href="{{ route('zoho.integration.index') }}" class="btn">Спробувати знову</a>
            <a href="{{ route('zoho.integration.status') }}" class="btn btn-secondary">Переглянути статус</a>
        </div>
    </div>
</body>
</html>
