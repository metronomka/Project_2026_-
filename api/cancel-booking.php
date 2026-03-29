<?php
session_start();
header('Content-Type: application/json');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

// Подключение к БД
include '../config/config.php';

// Получение данных
$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'] ?? null;

if (!$booking_id) {
    echo json_encode(['success' => false, 'error' => 'Не указан ID бронирования']);
    exit;
}

$booking_id = (int)$booking_id;
$user_id = (int)$_SESSION['user_id'];

// Проверка: принадлежит ли бронирование пользователю
$booking = fetch_one(query("SELECT user_id, status FROM bookings WHERE id = $booking_id"));

if (!$booking) {
    echo json_encode(['success' => false, 'error' => 'Бронирование не найдено']);
    exit;
}

// Сравниваем как числа
if ((int)$booking['user_id'] !== $user_id) {
    echo json_encode(['success' => false, 'error' => 'Нет доступа к этому бронированию']);
    exit;
}

if ($booking['status'] !== 'pending') {
    echo json_encode(['success' => false, 'error' => 'Можно отменить только бронирования со статусом "Ожидает подтверждения"']);
    exit;
}

// Отмена бронирования
query("UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id");

echo json_encode(['success' => true]);
?>
