@extends('layouts.app')

@section('title', 'Управление бюджетом')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/budget.css') }}">
@endsection

@section('content')
<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Управление бюджетом</h1>

            <!-- Добавление бюджета -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Добавить бюджет</h5>
                    <form id="budgetForm" class="row g-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Категория</label>
                            <select class="form-select" id="category" name="category_id" required>
                                <!-- Категории будут загружены через JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Сумма</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="period" class="form-label">Период</label>
                            <select class="form-select" id="period" name="period" required>
                                <option value="month">Месяц</option>
                                <option value="week">Неделя</option>
                                <option value="year">Год</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Добавить бюджет</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Текущие бюджеты -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Текущие бюджеты</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Категория</th>
                                    <th>Сумма</th>
                                    <th>Период</th>
                                    <th>Использовано</th>
                                    <th>Остаток</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody id="budgetsList">
                                <!-- Бюджеты будут добавлены через JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- График использования бюджета -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Использование бюджета</h5>
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования бюджета -->
<div class="modal fade" id="editBudgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать бюджет</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editBudgetForm">
                    <input type="hidden" id="editBudgetId">
                    <div class="mb-3">
                        <label for="editAmount" class="form-label">Сумма</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="editAmount" name="amount" min="0" step="0.01" required>
                            <span class="input-group-text">₽</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="updateBudget()">Сохранить</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let budgetChart;

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadBudgets();

    // Обработка формы добавления бюджета
    document.getElementById('budgetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addBudget();
    });
});

function loadCategories() {
    fetch('/api/categories')
        .then(response => response.json())
        .then(categories => {
            const select = document.getElementById('category');
            select.innerHTML = '<option value="">Выберите категорию</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        });
}

function loadBudgets() {
    fetch('/api/budgets')
        .then(response => response.json())
        .then(budgets => {
            const tbody = document.getElementById('budgetsList');
            tbody.innerHTML = '';
            
            budgets.forEach(budget => {
                const used = budget.expenses ? budget.expenses.reduce((sum, exp) => sum + exp.amount, 0) : 0;
                const remaining = budget.amount - used;
                const percentage = (used / budget.amount * 100).toFixed(1);
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${budget.category.name}</td>
                    <td>${budget.amount.toFixed(2)} ₽</td>
                    <td>${budget.period}</td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar ${percentage > 100 ? 'bg-danger' : 'bg-success'}" 
                                 role="progressbar" 
                                 style="width: ${Math.min(percentage, 100)}%">
                                ${percentage}%
                            </div>
                        </div>
                    </td>
                    <td>${remaining.toFixed(2)} ₽</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editBudget(${budget.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBudget(${budget.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            updateBudgetChart(budgets);
        });
}

function updateBudgetChart(budgets) {
    const ctx = document.getElementById('budgetChart').getContext('2d');
    
    if (budgetChart) {
        budgetChart.destroy();
    }

    const data = budgets.map(budget => {
        const used = budget.expenses ? budget.expenses.reduce((sum, exp) => sum + exp.amount, 0) : 0;
        return {
            category: budget.category.name,
            budget: budget.amount,
            used: used
        };
    });

    budgetChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.category),
            datasets: [
                {
                    label: 'Бюджет',
                    data: data.map(item => item.budget),
                    backgroundColor: '#36A2EB'
                },
                {
                    label: 'Использовано',
                    data: data.map(item => item.used),
                    backgroundColor: '#FF6384'
                }
            ]
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

function addBudget() {
    const formData = new FormData(document.getElementById('budgetForm'));
    
    fetch('/api/budgets', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('budgetForm').reset();
            loadBudgets();
        }
    });
}

function editBudget(id) {
    fetch(`/api/budgets/${id}`)
        .then(response => response.json())
        .then(budget => {
            document.getElementById('editBudgetId').value = budget.id;
            document.getElementById('editAmount').value = budget.amount;
            new bootstrap.Modal(document.getElementById('editBudgetModal')).show();
        });
}

function updateBudget() {
    const id = document.getElementById('editBudgetId').value;
    const amount = document.getElementById('editAmount').value;
    
    fetch(`/api/budgets/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ amount })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editBudgetModal')).hide();
            loadBudgets();
        }
    });
}

function deleteBudget(id) {
    if (confirm('Вы уверены, что хотите удалить этот бюджет?')) {
        fetch(`/api/budgets/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadBudgets();
            }
        });
    }
}
</script>
@endsection 