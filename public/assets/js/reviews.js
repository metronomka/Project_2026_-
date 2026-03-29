/**
 * Отзывы — звёзды рейтинга и слайдер
 */

const Reviews = {
    currentSlide: 0,
    slides: [],

    init() {
        this.slides = document.querySelectorAll('.review-slide');
        
        this.initRatingStars();
        this.initSlider();
    },

    initRatingStars() {
        const ratingContainer = document.getElementById('rating-stars');
        if (!ratingContainer) return;

        const stars = ratingContainer.querySelectorAll('.star-label');
        const icons = ratingContainer.querySelectorAll('.star-icon');
        const radios = ratingContainer.querySelectorAll('input[type="radio"]');

        // Функция обновления звёзд
        const updateStars = (value) => {
            icons.forEach((icon, index) => {
                if (index < value) {
                    icon.classList.remove('text-gray-300');
                    icon.classList.add('text-accent', 'fill-accent');
                } else {
                    icon.classList.remove('text-accent', 'fill-accent');
                    icon.classList.add('text-gray-300');
                }
            });
        };

        // При наведении
        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                updateStars(index + 1);
            });
            
            star.addEventListener('mouseleave', () => {
                const checked = ratingContainer.querySelector('input[type="radio"]:checked');
                if (checked) {
                    updateStars(parseInt(checked.value));
                } else {
                    updateStars(0);
                }
            });
        });

        // При клике
        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                updateStars(parseInt(radio.value));
            });
        });
    },

    initSlider() {
        if (this.slides.length === 0) return;

        // Автопереключение каждые 5 секунд
        if (this.slides.length > 1) {
            setInterval(() => this.nextSlide(), 5000);
        }
    },

    showSlide(index) {
        this.slides.forEach(s => s.classList.remove('active'));
        
        if (index >= this.slides.length) {
            this.currentSlide = 0;
        } else if (index < 0) {
            this.currentSlide = this.slides.length - 1;
        } else {
            this.currentSlide = index;
        }
        
        this.slides[this.currentSlide].classList.add('active');
    },

    nextSlide() {
        this.showSlide(this.currentSlide + 1);
    },

    prevSlide() {
        this.showSlide(this.currentSlide - 1);
    }
};

// Глобальные функции для доступа из HTML
window.nextSlide = () => Reviews.nextSlide();
window.prevSlide = () => Reviews.prevSlide();

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.review-slide') || document.getElementById('rating-stars')) {
        Reviews.init();
    }
});
