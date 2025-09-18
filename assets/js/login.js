document.addEventListener('DOMContentLoaded', function() {
    const userTypeRadios = document.querySelectorAll('input[name="userType"]');
    const usernameLabel = document.getElementById('usernameLabel');
    const userTypeInput = document.getElementById('userTypeInput');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Handle user type changes
    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const userType = this.value;
            userTypeInput.value = userType;
            
            // Update username label based on user type
            switch(userType) {
                case 'student':
                    usernameLabel.textContent = 'PRN Number';
                    break;
                case 'coordinator':
                    usernameLabel.textContent = 'Coordinator ID';
                    break;
                case 'hod':
                    usernameLabel.textContent = 'HoD ID';
                    break;
                case 'admin':
                    usernameLabel.textContent = 'Admin ID';
                    break;
            }
        });
    });

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Form submission with validation
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        
        if (!username || !password) {
            e.preventDefault();
            showNotification('Please fill in all fields', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
        submitBtn.disabled = true;
        
        // Reset button after 3 seconds if form doesn't redirect
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});

// Utility function for notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${getNotificationIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}