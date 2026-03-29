/**
 * Личный кабинет — вкладки, отмена бронирования, отзывы
 */

const Profile = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        // Переключение вкладок
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const tabName = e.target.id.replace('tab-link-', '');
                this.switchTab(tabName);
            });
        });
    },

    switchTab(tabName) {
        // Скрыть весь контент
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Убрать активный класс с ссылок
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('active');
        });

        // Показать нужную вкладку
        document.getElementById('tab-' + tabName).classList.add('active');
        document.getElementById('tab-link-' + tabName).classList.add('active');
    },

    cancelBooking(bookingId) {
        if (confirm('Вы уверены, что хотите отменить бронирование?')) {
            fetch('../api/cancel-booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ booking_id: bookingId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Бронирование отменено');
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                alert('Ошибка сети: ' + error);
            });
        }
    },

    leaveReview(bookingId) {
        // Пока просто алерт, потом можно сделать модальное окно или редирект
        alert('Форма отзыва будет добавлена позже. ID бронирования: ' + bookingId);
    }
};

// Глобальные функции для доступа из HTML
window.switchTab = (tabName) => Profile.switchTab(tabName);
window.cancelBooking = (bookingId) => Profile.cancelBooking(bookingId);
window.leaveReview = (bookingId) => Profile.leaveReview(bookingId);

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.tab-link')) {
        Profile.init();
    }
});
