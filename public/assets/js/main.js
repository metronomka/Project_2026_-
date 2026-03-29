/**
 * Глобальные функции МЕТРОНОМ
 */

// Уведомления (toast)
window.showNotification = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 px-6 py-3 text-white z-[100] animate-bounce ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};

// Мобильное меню
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (btn && menu) {
        btn.onclick = () => menu.classList.toggle('hidden');
    }
});
