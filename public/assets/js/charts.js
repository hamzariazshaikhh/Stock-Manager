document.addEventListener('DOMContentLoaded', function () {
    const canvasElement = document.getElementById('inventoryChart');
    if (!canvasElement) return;

    // Function to initialize chart once Chart.js is ready
    function initChart() {
        if (typeof Chart === 'undefined') {
            // Wait 50ms and check again if CDN is still downloading
            setTimeout(initChart, 50);
            return;
        }

        const summaryData = {
            products: parseInt(canvasElement.dataset.products || 0, 10),
            categories: parseInt(canvasElement.dataset.categories || 0, 10),
            suppliers: parseInt(canvasElement.dataset.suppliers || 0, 10),
            lowStock: parseInt(canvasElement.dataset.lowStock || 0, 10)
        };

        const ctx = canvasElement.getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Products', 'Categories', 'Suppliers', 'Low Stock'],
                datasets: [{
                    label: 'Inventory Count',
                    data: [
                        summaryData.products, 
                        summaryData.categories, 
                        summaryData.suppliers, 
                        summaryData.lowStock
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    initChart();
});
