/**
 * Карта Яндекс для страницы "О нас"
 */

function initMap() {
    if (!document.getElementById('map')) return;
    
    ymaps.ready(() => {
        const myMap = new ymaps.Map("map", {
            center: [53.288361, 83.567863], // Координаты: г. Барнаул, ул. Спортивная
            zoom: 15,
            controls: ['zoomControl', 'fullscreenControl']
        });

        const myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
            balloonContent: '<strong>Фотостудия МЕТРОНОМ</strong><br/>г. Барнаул, ул. Спортивная, д. 1В',
            hintContent: 'Мы здесь!'
        }, {
            preset: 'islands#yellowDotIcon'
        });

        myMap.geoObjects.add(myPlacemark);
        myMap.behaviors.disable('scrollZoom');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof ymaps !== 'undefined') {
        initMap();
    }
});
