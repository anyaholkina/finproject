@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Дашборд</div>

                <div class="card-body">
                    <h4>Добро пожаловать, {{ Auth::user()->name }}!</h4>
                    <p>Вы успешно вошли в систему.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 