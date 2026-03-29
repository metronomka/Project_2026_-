/**
 * Каталог услуг — выбор фотографа и доп. услуг
 */

const Catalog = {
    selectedPhotographerPrice: 0,
    additionalServicesTotal: 0,
    constructor: null,
    totalPriceEl: null,

    init() {
        this.constructor = document.getElementById('constructor-section');
        this.totalPriceEl = document.getElementById('total-price');
        
        if (!this.constructor) return;
        
        this.bindEvents();
    },

    bindEvents() {
        // Выбор фотографа
        document.querySelectorAll('.photographer-card').forEach(card => {
            card.addEventListener('click', () => this.selectPhotographer(card));
        });

        // Выбор доп. услуг
        document.querySelectorAll('.service-item').forEach(item => {
            item.addEventListener('click', () => this.toggleAddon(item));
        });
    },

    selectPhotographer(card) {
        // Сброс активного состояния
        document.querySelectorAll('.photographer-card').forEach(c => {
            c.classList.remove('selected-ring');
            const indicator = c.querySelector('.select-indicator');
            if (indicator) {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }
        });

        // Активация выбранного
        card.classList.add('selected-ring');
        const indicator = card.querySelector('.select-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
            indicator.classList.add('flex');
        }

        this.selectedPhotographerPrice = parseInt(card.dataset.price);

        // Показ конструктора
        this.constructor.classList.remove('hidden');
        setTimeout(() => this.constructor.classList.add('opacity-100'), 50);

        this.updateTotal();
    },

    toggleAddon(item) {
        const checkBox = item.querySelector('.check-box');
        const checkIcon = item.querySelector('[data-lucide="plus"]');
        const price = parseInt(item.dataset.price);

        if (item.classList.contains('service-active')) {
            item.classList.remove('service-active');
            checkBox.classList.remove('check-box-active');
            if (checkIcon) checkIcon.classList.add('hidden');
            this.additionalServicesTotal -= price;
        } else {
            item.classList.add('service-active');
            checkBox.classList.add('check-box-active');
            if (checkIcon) checkIcon.classList.remove('hidden');
            this.additionalServicesTotal += price;
        }

        this.updateTotal();
    },

    updateTotal() {
        const total = this.selectedPhotographerPrice + this.additionalServicesTotal;
        this.totalPriceEl.innerText = total.toLocaleString() + ' ₽';

        // Анимация изменения цены (пульсация)
        this.totalPriceEl.style.transform = 'scale(1.1)';
        setTimeout(() => this.totalPriceEl.style.transform = 'scale(1)', 200);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('photographers-grid')) {
        Catalog.init();
    }
});
