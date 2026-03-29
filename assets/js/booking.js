/**
 * Пошаговая форма бронирования
 */

const BookingForm = {
    state: {
        step: 1,
        service: null,
        photographer: null,
        addons: [],
        date: '',
        time: null
    },

    data: {
        services: [],
        photographers: [],
        addons: [],
        timeSlots: ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00']
    },

    init(config = {}) {
        if (config.data) {
            this.data = { ...this.data, ...config.data };
        }
        if (config.state) {
            this.state = { ...this.state, ...config.state };
        }

        this.renderProgress();
        this.updateNavigation();
        this.initHiddenFields();
        this.bindEvents();
        
        // Автозаполнение данных пользователя
        if (config.user) {
            this.fillUserData(config.user);
        }

        if (this.state.service && this.state.photographer) {
            this.renderSummary();
        }
    },

    fillUserData(user) {
        const nameField = document.querySelector('input[name="name"]');
        const emailField = document.querySelector('input[name="email"]');
        const phoneField = document.querySelector('input[name="phone"]');
        
        if (nameField && user.name) nameField.value = user.name;
        if (emailField && user.email) emailField.value = user.email;
        if (phoneField && user.phone) phoneField.value = user.phone;
    },

    initHiddenFields() {
        const serviceField = document.getElementById('hidden-service-id');
        const photographerField = document.getElementById('hidden-photographer-id');
        const dateField = document.getElementById('hidden-date');
        const timeField = document.getElementById('hidden-time-slot');

        if (serviceField) serviceField.value = this.state.service;
        if (photographerField) photographerField.value = this.state.photographer;
        if (dateField) dateField.value = this.state.date;
        if (timeField) timeField.value = this.state.time;
    },

    selectService(id) {
        this.state.service = id;
        this.updateHiddenField('hidden-service-id', id);

        document.querySelectorAll('#services-grid input[name="service_id"]').forEach(input => {
            input.checked = (input.value == id);
        });

        document.querySelectorAll('#services-grid > div').forEach(div => {
            div.classList.remove('ring-2', 'ring-accent', 'border-accent');
        });

        event.currentTarget.classList.add('ring-2', 'ring-accent', 'border-accent');
        this.updateNavigation();
    },

    selectPhotographer(id) {
        this.state.photographer = id;
        this.updateHiddenField('hidden-photographer-id', id);

        document.querySelectorAll('#photographers-grid input[name="photographer_id"]').forEach(input => {
            input.checked = (input.value == id);
        });

        document.querySelectorAll('#photographers-grid > div').forEach(div => {
            div.classList.remove('ring-2', 'ring-accent', 'border-accent');
        });

        event.currentTarget.classList.add('ring-2', 'ring-accent', 'border-accent');
        this.updateNavigation();
    },

    toggleAddon(id) {
        const index = this.state.addons.indexOf(String(id));
        if (index > -1) {
            this.state.addons.splice(index, 1);
        } else {
            this.state.addons.push(String(id));
        }
        this.renderAddons();
    },

    renderAddons() {
        document.querySelectorAll('#addons-grid input[name="addons[]"]').forEach(input => {
            const isChecked = this.state.addons.includes(String(input.value));
            input.checked = isChecked;
            input.parentElement.classList.toggle('bg-accent/10', isChecked);
            input.parentElement.classList.toggle('border-accent', isChecked);
        });
    },

    selectTime(time) {
        this.state.time = time;
        this.updateHiddenField('hidden-time-slot', time);

        document.querySelectorAll('#time-slots button').forEach(btn => {
            btn.classList.remove('bg-accent', 'text-white', 'border-accent');
        });

        event.currentTarget.classList.add('bg-accent', 'text-white', 'border-accent');
        this.updateNavigation();
    },

    updateHiddenField(id, value) {
        const field = document.getElementById(id);
        if (field) field.value = value;
    },

    updateNavigation() {
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');

        if (!nextBtn || !prevBtn) return;

        prevBtn.disabled = this.state.step === 1;
        nextBtn.style.display = this.state.step === 4 ? 'none' : 'block';

        let canGoNext = false;
        if (this.state.step === 1 && this.state.service) canGoNext = true;
        if (this.state.step === 2 && this.state.photographer) canGoNext = true;
        if (this.state.step === 3 && this.state.date && this.state.time) canGoNext = true;

        nextBtn.disabled = !canGoNext;

        if (this.state.step === 4) this.renderSummary();
    },

    renderProgress() {
        const bar = document.getElementById('progress-bar');
        if (!bar) return;

        const names = ['Услуга', 'Фотограф', 'Дата', 'Подтверждение'];
        bar.innerHTML = names.map((name, i) => `
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 flex items-center justify-center border-2 ${
                    this.state.step >= i+1 ? 'bg-accent border-accent text-white' : 'border-gray-300'
                }">
                    ${this.state.step > i+1 ? '✓' : i+1}
                </div>
                <span class="text-[10px] mt-1">${name}</span>
            </div>
        `).join('<div class="h-0.5 flex-1 bg-gray-200 mt-5"></div>');
    },

    renderSummary() {
        const content = document.getElementById('summary-content');
        if (!content) return;

        const service = this.data.services.find(s => s.id == this.state.service);
        const photographer = this.data.photographers.find(p => p.id == this.state.photographer);
        const addonsTotal = this.data.addons
            .filter(a => this.state.addons.includes(String(a.id)))
            .reduce((sum, a) => sum + parseFloat(a.price), 0);

        const servicePrice = service ? parseFloat(service.base_price) : 0;
        const totalPrice = servicePrice + addonsTotal;

        content.innerHTML = `
            <div>
                <p class="text-sm text-muted-foreground">Услуга</p>
                <p class="font-bold">${service ? service.name : 'Не выбрана'}</p>
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Фотограф</p>
                <p class="font-bold">${photographer ? photographer.name : 'Не выбран'}</p>
            </div>
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Дата</p>
                    <p class="font-bold">${this.state.date || 'Не выбрана'}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">Время</p>
                    <p class="font-bold">${this.state.time || 'Не выбрано'}</p>
                </div>
            </div>
            <div class="border-t pt-4 flex justify-between text-xl font-bold">
                <span>Итого:</span>
                <span class="text-accent">${totalPrice.toLocaleString()} ₽</span>
            </div>
        `;
    },

    bindEvents() {
        // Кнопки навигации
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.nextStep();
            });
        }
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.prevStep();
            });
        }
        
        // Дата
        const dateInput = document.getElementById('date-input');
        if (dateInput) {
            dateInput.addEventListener('change', (e) => this.setDate(e.target.value));
        }
    },

    nextStep() {
        this.state.step++;
        this.updateSteps();
        this.renderProgress();
        this.updateNavigation();
    },

    prevStep() {
        this.state.step--;
        this.updateSteps();
        this.renderProgress();
        this.updateNavigation();
    },

    updateSteps() {
        document.querySelectorAll('.step-content').forEach((el, i) => {
            el.classList.toggle('active', i + 1 === this.state.step);
        });
    },

    setDate(value) {
        this.state.date = value;
        this.updateHiddenField('hidden-date', value);
        this.updateNavigation();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('booking-form');
    if (!form) return;
    
    // Инициализация с данными из window.bookingData
    if (window.bookingData) {
        BookingForm.init(window.bookingData);
    } else {
        BookingForm.init();
    }
});

// Глобальные функции для доступа из HTML
window.selectService = (id) => BookingForm.selectService(id);
window.selectPhotographer = (id) => BookingForm.selectPhotographer(id);
window.toggleAddon = (id) => BookingForm.toggleAddon(id);
window.selectTime = (time) => BookingForm.selectTime(time);
