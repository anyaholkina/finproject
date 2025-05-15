@extends('layouts.app')

@section('title', 'Smart Money - Главная')

@section('content')
<section class="hero-section">
    <div class="container">
        <h1 class="mb-4">Приложение для управления личными финансами</h1>
        <h4 class="mb-5">Сервис для учета расходов и семейного бюджета</h4>
        <a href="{{ route('register') }}" class="btn-4"><span>Начать</span></a>
    </div>
</section>

<section class="cards-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="feature-title">Анализ</h2>
                        <ul class="list-unstyled">
                            <li class="feature-item">График и отчеты</li>
                            <li class="feature-item">Категории расходов</li>
                            <li class="feature-item">Динамика доходов</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="feature-title">Семья</h2>
                        <ul class="list-unstyled">
                            <li class="feature-item">Общий бюджет</li>
                            <li class="feature-item">Совместные цели</li>
                            <li class="feature-item">Контроль расходов</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="feature-title">Уведомления</h2>
                        <ul class="list-unstyled">
                            <li class="feature-item">Контроль лимитов</li>
                            <li class="feature-item">Напоминания</li>
                            <li class="feature-item">Важные события</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 