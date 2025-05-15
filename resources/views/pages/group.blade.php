@extends('layouts.app')

@section('title', 'Управление группой')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/group.css') }}">
@endsection

@section('content')
<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Управление группой</h1>

            <!-- Информация о группе -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Информация о группе</h5>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editGroupModal">
                            <i class="bi bi-pencil"></i> Редактировать
                        </button>
                    </div>
                    <div id="groupInfo">
                        <!-- Информация будет загружена через JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Управление бюджетом -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Общий бюджет</h5>
                    <form id="budgetForm" class="row g-3">
                        <div class="col-md-6">
                            <label for="budget" class="form-label">Сумма бюджета</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="budget" name="budget" min="0" step="0.01" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Сохранить бюджет</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Участники группы -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Участники группы</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
                            <i class="bi bi-person-plus"></i> Пригласить
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody id="membersList">
                                <!-- Участники будут добавлены через JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Статистика группы -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Статистика группы</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="groupChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Участник</th>
                                            <th>Расходы</th>
                                            <th>% от бюджета</th>
                                        </tr>
                                    </thead>
                                    <tbody id="statisticsTable">
                                        <!-- Статистика будет добавлена через JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно приглашения -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Пригласить участника</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="inviteForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email участника</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="inviteUser()">Пригласить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования группы -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать группу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editGroupForm">
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Название группы</label>
                        <input type="text" class="form-control" id="groupName" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="updateGroup()">Сохранить</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let groupChart;

document.addEventListener('DOMContentLoaded', function() {
    loadGroupInfo();
    loadMembers();
    loadStatistics();

    // Обработка формы бюджета
    document.getElementById('budgetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateBudget();
    });
});

function loadGroupInfo() {
    fetch('/api/group')
        .then(response => response.json())
        .then(data => {
            document.getElementById('groupInfo').innerHTML = `
                <p><strong>Название:</strong> ${data.name}</p>
                <p><strong>Создатель:</strong> ${data.owner.name}</p>
                <p><strong>Дата создания:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
            `;
            document.getElementById('budget').value = data.budget || '';
            document.getElementById('groupName').value = data.name;
        });
}

function loadMembers() {
    fetch('/api/group')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('membersList');
            tbody.innerHTML = '';
            
            data.users.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>
                        ${user.role !== 'admin' ? `
                            <button class="btn btn-sm btn-outline-danger" onclick="removeUser(${user.id})">
                                <i class="bi bi-person-x"></i>
                            </button>
                        ` : ''}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function loadStatistics() {
    fetch('/api/group/statistics')
        .then(response => response.json())
        .then(data => {
            // Обновление графика
            const ctx = document.getElementById('groupChart').getContext('2d');
            
            if (groupChart) {
                groupChart.destroy();
            }

            groupChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.members.map(member => member.name),
                    datasets: [{
                        data: data.members.map(member => member.total_expenses),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
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

            // Обновление таблицы
            const tbody = document.getElementById('statisticsTable');
            tbody.innerHTML = '';
            
            data.members.forEach(member => {
                const percentage = ((member.total_expenses / data.total_expenses) * 100).toFixed(1);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${member.name}</td>
                    <td>${member.total_expenses.toFixed(2)} ₽</td>
                    <td>${percentage}%</td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function inviteUser() {
    const email = document.getElementById('email').value;
    
    fetch('/api/group/invite', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('inviteModal')).hide();
            loadMembers();
        }
    });
}

function removeUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого участника из группы?')) {
        fetch('/api/group/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMembers();
                loadStatistics();
            }
        });
    }
}

function updateBudget() {
    const budget = document.getElementById('budget').value;
    
    fetch('/api/group/budget', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ budget })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadGroupInfo();
            loadStatistics();
        }
    });
}

function updateGroup() {
    const name = document.getElementById('groupName').value;
    
    fetch('/api/group', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editGroupModal')).hide();
            loadGroupInfo();
        }
    });
}
</script>
@endsection 