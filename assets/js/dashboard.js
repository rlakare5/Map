document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            } else {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
            }
            
            // Save state in localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Restore sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
        if (sidebarCollapsed === 'true') {
            sidebar.classList.add('collapsed');
            const icon = sidebarToggle.querySelector('i');
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        }
    }
    
    // Mobile menu toggle
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // File drag and drop functionality
    const fileUploadAreas = document.querySelectorAll('.file-upload-area');
    fileUploadAreas.forEach(area => {
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });
        
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const fileInput = this.nextElementSibling;
                if (fileInput && fileInput.type === 'file') {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;
            
            // Reset button after 5 seconds if form doesn't redirect
            setTimeout(() => {
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 5000);
        });
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
    
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }
    
    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetTable = document.querySelector(this.dataset.target);
            
            if (targetTable) {
                const rows = targetTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });
});

// Notification system
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification ${type} show`;
    
    const icon = getNotificationIcon(type);
    notification.innerHTML = `
        <i class="${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }
    }, duration);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    return icons[type] || icons.info;
}

// Initialize charts
function initializeCharts() {
    // Progress donut chart
    const progressChart = document.getElementById('progressChart');
    if (progressChart) {
        const ctx = progressChart.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Remaining'],
                datasets: [{
                    data: [progressChart.dataset.completed || 0, progressChart.dataset.remaining || 100],
                    backgroundColor: ['#10b981', '#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });
    }
    
    // Category breakdown chart
    const categoryChart = document.getElementById('categoryChart');
    if (categoryChart) {
        const ctx = categoryChart.getContext('2d');
        const categories = JSON.parse(categoryChart.dataset.categories || '[]');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories.map(cat => `Category ${cat.id}`),
                datasets: [
                    {
                        label: 'Required Points',
                        data: categories.map(cat => cat.required),
                        backgroundColor: '#e5e7eb'
                    },
                    {
                        label: 'Earned Points',
                        data: categories.map(cat => cat.earned),
                        backgroundColor: '#2563eb'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Utility functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function validateFile(file, allowedTypes, maxSize) {
    if (!allowedTypes.includes(file.type)) {
        showNotification('Invalid file type. Please select a valid file.', 'error');
        return false;
    }
    
    if (file.size > maxSize) {
        showNotification(`File size exceeds ${formatFileSize(maxSize)}. Please select a smaller file.`, 'error');
        return false;
    }
    
    return true;
}

// Export utility functions for use in other scripts
window.dashboardUtils = {
    showNotification,
    formatDate,
    formatFileSize,
    validateFile
};