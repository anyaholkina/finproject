<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Аналитика расходов</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Аналитика расходов</h1>
        <p>Пользователь: {{ $user->name }}</p>
        @if($group)
            <p>Группа: {{ $group->name }}</p>
        @endif
        <p>Дата отчета: {{ now()->format('d.m.Y') }}</p>
    </div>

    <div class="section">
        <h2>Расходы по категориям</h2>
        <table>
            <thead>
                <tr>
                    <th>Категория</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $category => $items)
                    <tr>
                        <td>{{ $category }}</td>
                        <td>{{ number_format($items->sum('amount'), 2) }} ₽</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="total">Итого:</td>
                    <td class="total">{{ number_format($expenses->flatten()->sum('amount'), 2) }} ₽</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Бюджеты</h2>
        <table>
            <thead>
                <tr>
                    <th>Категория</th>
                    <th>Лимит</th>
                    <th>Период</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $budget)
                    <tr>
                        <td>{{ $budget->category->name }}</td>
                        <td>{{ number_format($budget->amount, 2) }} ₽</td>
                        <td>{{ $budget->start_date->format('d.m.Y') }} - {{ $budget->end_date->format('d.m.Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html> 