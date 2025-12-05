document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menu_tougle');
    const navLinks = document.getElementById('navLinks');
    if (menuBtn) {
        menuBtn.addEventListener('click', function() {
            if (navLinks.classList.contains('hidden')) {
                navLinks.classList.remove('hidden');
                navLinks.classList.add('block', 'absolute', 'top-16', 'left-0', 'right-0', 'bg-blue-600', 'p-4', 'z-50');
            } else {
                navLinks.classList.add('hidden');
                navLinks.classList.remove('block', 'absolute', 'top-16', 'left-0', 'right-0', 'bg-blue-600', 'p-4', 'z-50');
             }
        });
    }

    //URLSearchParams lire la partie de l'URL apres ? ex: url = page.php?message=success&error=none  => message = success et error = none
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const error = urlParams.get('error');
    
    if (message) {
        showNotification(getMessageText(message), 'success');
    }
    
    if (error) {
        showNotification(getErrorText(error), 'error');
    }
    
    if (message || error) {
        //window.history.replaceState() => permet de modifier URL qui affiche sans recharger la page
        //window.location.pathname => c'est URL sans les parametres GET
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

function showNotification(text, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = text;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function getMessageText(message) {
    const messages = {
        'income_added': 'Income added successfully!',
        'income_updated': 'Income updated successfully!',
        'income_deleted': 'Income deleted successfully!',
        'expense_added': 'Expense added successfully!',
        'expense_updated': 'Expense updated successfully!',
        'expense_deleted': 'Expense deleted successfully!'
    };
    return messages[message] || 'Operation completed successfully!';
}

function getErrorText(error) {
    const errors = {
        'missing_fields': 'Please fill in all required fields!',
        'no_id': 'No record ID provided!',
        'not_found': 'Record not found!',
        'insert_failed': 'Failed to add record!',
        'update_failed': 'Failed to update record!',
        'delete_failed': 'Failed to delete record!'
    };
    return errors[error] || 'An error occurred!';
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredIn = form.querySelectorAll('[required]');
    let isValide = true;
    
    requiredIn.forEach(str => {
        if (!str.value.trim()) {
            str.classList.add('border-red-500');
            isValide = false;
        } else {
            str.classList.remove('border-red-500');
        }
    });
    
    return isValide;
}

//comment afficher les amount (montants => money)
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function confirmDelete(actionText) {
    return confirm(`Are you sure you want to ${actionText}? This action cannot be undone.`);
}