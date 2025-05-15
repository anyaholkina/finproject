@extends('layouts.app')

@section('title', 'Аналитика расходов')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
@endsection

@section('content')
<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Аналитика расходов</h1>

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label for="period" class="form-label">Период</label>
                            <select class="form-select" id="period" name="period">
                                <option value="day">День</option>
                                <option value="week">Неделя</option>
                                <option value="month" selected>Месяц</option>
                                <option value="year">Год</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="startDate" class="form-label">Начало периода</label>
                            <input type="date" class="form-control" id="startDate" name="startDate">
                        </div>
                        <div class="col-md-4">
                            <label for="endDate" class="form-label">Конец периода</label>
                            <input type="date" class="form-control" id="endDate" name="endDate">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Применить фильтры</button>
                            <button type="button" class="btn btn-outline-primary" onclick="exportAnalytics()">
                                <i class="bi bi-download"></i> Экспорт в PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Графики -->
            <div class="row">
                <!-- Круговая диаграмма -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Распределение по категориям</h5>
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Столбчатая диаграмма -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Динамика расходов</h5>
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица расходов -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Детализация расходов</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Категория</th>
                                    <th>Сумма</th>
                                    <th>% от общего</th>
                                </tr>
                            </thead>
                            <tbody id="expensesTable">
                                <!-- Данные будут добавлены через JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let categoryChart, trendChart;

document.addEventListener('DOMContentLoaded', function() {
   
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    document.getElementById('startDate').value = firstDayOfMonth.toISOString().split('T')[0];
    document.getElementById('endDate').value = today.toISOString().split('T')[0];

    
    loadAnalytics();

    
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadAnalytics();
    });
});

function loadAnalytics() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);

    fetch(`/api/analytics/by-category?${params}`)
        .then(response => response.json())
        .then(data => {
            updateCategoryChart(data);
            updateExpensesTable(data);
        });

    fetch(`/api/analytics/by-period?${params}`)
        .then(response => response.json())
        .then(data => {
            updateTrendChart(data);
        });
}

function updateCategoryChart(data) {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    if (categoryChart) {
        categoryChart.destroy();
    }

    categoryChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.category.name),
            datasets: [{
                data: data.map(item => item.total),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
}

function updateTrendChart(data) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    if (trendChart) {
        trendChart.destroy();
    }

    trendChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.date),
            datasets: [{
                label: 'Расходы',
                data: data.map(item => item.amount),
                backgroundColor: '#36A2EB'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateExpensesTable(data) {
    const tbody = document.getElementById('expensesTable');
    tbody.innerHTML = '';
    
    const total = data.reduce((sum, item) => sum + item.total, 0);
    
    data.forEach(item => {
        const percentage = ((item.total / total) * 100).toFixed(1);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.category.name}</td>
            <td>${item.total.toFixed(2)} ₽</td>
            <td>${percentage}%</td>
        `;
        tbody.appendChild(tr);
    });
}

function exportAnalytics() {
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);
    
    window.location.href = `/api/export/analytics?${params}`;
}
</script>
@endsection 