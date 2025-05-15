@props(['notifications'])

<div class="notifications-container">
    @foreach($notifications as $notification)
        <div class="notification alert {{ $notification['type'] === 'budget_exceeded' ? 'alert-danger' : 'alert-warning' }} alert-dismissible fade show" role="alert">
            <i class="bi {{ $notification['type'] === 'budget_exceeded' ? 'bi-exclamation-triangle' : 'bi-info-circle' }} me-2"></i>
            {{ $notification['message'] }}
            @if(isset($notification['amount']))
                <strong>Превышение: {{ number_format($notification['amount'], 2) }} ₽</strong>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
</div>

<style>
.notifications-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    max-width: 350px;
}

.notification {
    margin-bottom: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.notification .btn-close {
    position: absolute;
    right: 10px;
    top: 10px;
}

@media (max-width: 768px) {
    .notifications-container {
        left: 20px;
        right: 20px;
        max-width: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }, 5000);
    });
});
</script> 