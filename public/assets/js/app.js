document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach((alert) => {
        window.setTimeout(() => {
            alert.remove();
        }, 5000);
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm;

            if (!message || !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Global Print / PDF Export Handler
    const printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', () => {
            window.print();
        });
    }
});
