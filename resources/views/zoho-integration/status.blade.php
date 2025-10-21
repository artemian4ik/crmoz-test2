<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoho CRM - Статус інтеграції</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .status-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .status-active {
            color: #4CAF50;
        }
        .status-inactive {
            color: #f44336;
        }
        .status-title {
            color: #333;
            margin-bottom: 10px;
        }
        .status-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 0 10px 10px 0;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #1e7e34;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="status-card">
        <div class="status-header">
            <div class="status-icon {{ $isActive ? 'status-active' : 'status-inactive' }}">
                {{ $isActive ? '✓' : '✗' }}
            </div>
            <h1 class="status-title">
                {{ $isActive ? 'Інтеграція активна' : 'Інтеграція неактивна' }}
            </h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($integration)
            <div class="status-info">
                <div class="info-row">
                    <span class="info-label">Створено:</span>
                    <span class="info-value">{{ $integration->created_at->format('d.m.Y H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Оновлено:</span>
                    <span class="info-value">{{ $integration->updated_at->format('d.m.Y H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Токен закінчується:</span>
                    <span class="info-value">{{ ($integration->updated_at->addSeconds($integration->expires_in))->format('d.m.Y H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Статус токена:</span>
                    <span class="info-value">{{ $integration->isTokenExpired() ? 'Застарів' : 'Активний' }}</span>
                </div>
            </div>
        @else
            <div class="status-info">
                <p>Інтеграція ще не налаштована.</p>
            </div>
        @endif

        <div style="text-align: center;">
            @if($isActive)
                <button type="submit" onclick="refreshTokens()" class="btn btn-warning">Оновити токени</button>
            @else
                <a href="{{ route('zoho.integration.index') }}" class="btn btn-success">
                    {{ $integration ? 'Переналаштувати інтеграцію' : 'Налаштувати інтеграцію' }}
                </a>
                <button onclick="refreshTokens()" class="btn btn-warning">
                    Refresh tokens
                </button>
            @endif
        </div>
    </div>
</body>
</html>

<script>
    function refreshTokens() {
        fetch('{{ route('zoho.integration.refresh') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json()).then(data => {
            location.reload();
        });
    }
</script>