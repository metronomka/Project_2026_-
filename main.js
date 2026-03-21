/**
 * Глобальный конфигуратор сайта МЕТРОНОМ
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Инициализируем общие компоненты
    injectHeader();
    injectFooter();
    
    // 2. Подсвечиваем активную страницу в меню
    highlightActiveLink();

    // 3. Инициализируем иконки Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
function initMap() {
    // Проверяем, есть ли на странице блок для карты
    const mapContainer = document.getElementById('map');
    if (!mapContainer) return;

    ymaps.ready(() => {
        const myMap = new ymaps.Map("map", {
            center: [53.288361, 83.567863], // Координаты Москвы (ул. Примерная)
            zoom: 15,
            controls: ['zoomControl', 'fullscreenControl']
        });

        // Создаем стилизованную метку в цветах "Метронома"
        const myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
            balloonContent: '<strong>Фотостудия МЕТРОНОМ</strong><br/>ул. Спортивная, 1В',
            hintContent: 'Мы здесь!'
        }, {
            // Желтый цвет в стиле бренда
            preset: 'islands#yellowDotIconWithCaption'
        });

        myMap.geoObjects.add(myPlacemark);
        
        // Отключаем скролл мышкой, чтобы не мешать прокрутке страницы
        myMap.behaviors.disable('scrollZoom');
    });
}

// Обновите слушатель событий DOMContentLoaded в main.js
document.addEventListener('DOMContentLoaded', () => {
    injectHeader();
    injectFooter();
    highlightActiveLink();
    
    // Если подключен скрипт Яндекса, запускаем карту
    if (typeof ymaps !== 'undefined') {
        initMap();
    }
});
// --- Функции вставки компонентов ---

function injectHeader() {
    
    const headerHTML = `
    <header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="index.html" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-accent flex items-center justify-center transition-transform group-hover:rotate-90">
                        <div class="w-1 h-6 bg-white"></div>
                    </div>
                    <span class="text-xl font-bold tracking-tighter uppercase">Метроном</span>
                </a>
                <nav class="hidden md:flex items-center space-x-8" id="main-nav">
                    <a href="catalog.html" class="nav-link text-sm font-medium hover:text-accent transition-colors">Услуги</a>
                    <a href="about.html" class="nav-link text-sm font-medium hover:text-accent transition-colors">О нас</a>
                    <a href="reviews.html" class="nav-link text-sm font-medium hover:text-accent transition-colors">Отзывы</a>
                    <a href="booking.html" class="text-sm font-medium text-accent border border-accent px-4 py-2 hover:bg-accent hover:text-white transition-all">Забронировать</a>
                </nav>
                <div class="flex items-center space-x-5">
                    <div class="flex items-center space-x-5">
    <a href="login.html" class="hidden sm:block text-sm font-semibold hover:text-accent transition-colors">
        Войти
    </a>
    <a href="profile.html" class="hover:text-accent transition-colors" title="Личный кабинет">
        <i data-lucide="user" class="w-6 h-6"></i>
    </a>
    <button class="md:hidden" id="mobile-menu-btn">
        <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
</div>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100 absolute w-full left-0 px-4 py-6 space-y-4 shadow-xl">
            <a href="catalog.html" class="block text-lg font-semibold">Услуги</a>
            <a href="about.html" class="block text-lg font-semibold">О нас</a>
            <a href="reviews.html" class="block text-lg font-semibold">Отзывы</a>
            <a href="booking.html" class="block bg-accent text-white text-center py-3 font-bold">Забронировать</a>
        </div>
    </header>
    <div class="h-20"></div>`;

    document.body.insertAdjacentHTML('afterbegin', headerHTML);

    // Логика мобильного меню
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (btn) {
        btn.onclick = () => menu.classList.toggle('hidden');
    }
}

function injectFooter() {
    const footerHTML = `
    <footer class="bg-gray-50 border-t border-gray-200 pt-16 pb-8 mt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-6 h-6 bg-accent flex items-center justify-center"><div class="w-0.5 h-4 bg-white"></div></div>
                        <span class="text-lg font-bold uppercase">Метроном</span>
                    </div>
                    <p class="text-sm text-gray-500">Эстетика в каждом кадре.</p>
                </div>
                </div>
            <div class="border-t border-gray-200 pt-8 text-center text-[10px] text-gray-400 uppercase tracking-widest">
                © 2026 МЕТРОНОМ. Все права защищены.
            </div>
        </div>
    </footer>`;

    document.body.insertAdjacentHTML('beforeend', footerHTML);
}

// Автоматическое определение активной страницы
function highlightActiveLink() {
    const currentPath = window.location.pathname.split("/").pop() || 'index.html';
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('text-accent', 'font-bold');
        }
    });
}

// 4. Глобальная логика уведомлений (Toast)
window.showNotification = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 px-6 py-3 text-white z-[100] animate-bounce ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};
function injectModal() {
    const modalHTML = `
    <div id="success-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <div class="relative bg-white p-8 md:p-12 max-w-sm w-full text-center shadow-2xl border-t-4 border-accent animate-in zoom-in duration-300">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="check" class="w-10 h-10"></i>
            </div>
            <h2 class="text-2xl font-bold mb-4 uppercase tracking-tight">Заявка принята!</h2>
            <p class="text-gray-500 mb-8">Мы прислали подтверждение на вашу почту. Наш менеджер свяжется с вами в течение 15 минут.</p>
            <button onclick="closeModal()" class="w-full bg-black text-white py-4 font-bold hover:bg-accent transition-all">
                ОТЛИЧНО
            </button>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// Функции управления модальным окном
window.showSuccessModal = function() {
    const modal = document.getElementById('success-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Блокируем скролл страницы
};

window.closeModal = function() {
    const modal = document.getElementById('success-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Возвращаем скролл
};

// Не забудьте вызвать injectModal() при загрузке
document.addEventListener('DOMContentLoaded', () => {
    // ... ваши предыдущие вызовы (injectHeader и т.д.)
    injectModal();
});
window.handleBooking = function(event) {
    event.preventDefault(); // Чтобы страница не перезагружалась
    
    // Здесь можно добавить проверку заполнения полей
    const btn = event.target;
    const originalText = btn.innerText;
    
    // Имитация загрузки
    btn.innerText = "ОТПРАВКА...";
    btn.disabled = true;
    
    setTimeout(() => {
        showSuccessModal();
        btn.innerText = originalText;
        btn.disabled = false;
    }, 1500); // Задержка 1.5 секунды для реалистичности
};