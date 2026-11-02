document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach((alert) => {
        window.setTimeout(() => {
            alert.remove();
        }, 5000);
    });
});