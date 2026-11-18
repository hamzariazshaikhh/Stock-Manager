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


// This script is external, so it is ALLOWED by your CSP ('script-src 'self')
document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('click', (event) => {
        if (event.target && event.target.id === 'printButton') {
            event.preventDefault();
            window.print();
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle logic
    const toggleButton = document.getElementById('themeToggle');
    
    // Apply saved theme on load to <html>
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});


