@extends('layouts.app')

@section('title', 'Управление категориями')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/categories.css') }}">
@endsection

@section('content')
<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Управление категориями</h1>
            
            <!-- Форма добавления категории -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Добавить новую категорию</h5>
                    <form id="categoryForm" class="mt-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Название категории</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Иконка</label>
                                    <select class="form-select" id="icon" name="icon">
                                        <option value="shopping-cart">🛒 Покупки</option>
                                        <option value="car">🚗 Транспорт</option>
                                        <option value="home">🏠 Жилье</option>
                                        <option value="food">🍽️ Еда</option>
                                        <option value="health">⚕️ Здоровье</option>
                                        <option value="entertainment">🎮 Развлечения</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить категорию</button>
                    </form>
                </div>
            </div>

            <!-- Список категорий -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Мои категории</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Иконка</th>
                                    <th>Название</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesList">
                                <!-- Категории будут добавлены через JavaScript -->
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Загрузка категорий
    loadCategories();

    // Обработка формы добавления категории
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/api/categories', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCategories();
                this.reset();
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

function loadCategories() {
    fetch('/api/categories')
        .then(response => response.json())
        .then(categories => {
            const tbody = document.getElementById('categoriesList');
            tbody.innerHTML = '';
            
            categories.forEach(category => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><i class="bi bi-${category.icon}"></i></td>
                    <td>${category.name}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editCategory(${category.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${category.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error:', error));
}

function editCategory(id) {
    // Реализация редактирования категории
}

function deleteCategory(id) {
    if (confirm('Вы уверены, что хотите удалить эту категорию?')) {
        fetch(`/api/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCategories();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
@endsection 