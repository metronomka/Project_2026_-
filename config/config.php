<?php
// Запуск сессии
session_start();

// Параметры подключения к БД
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'metronome_studio';

// Подключение к MySQL через mysqli
$conn = mysqli_connect($host, $user, $pass, $db);

// Проверка подключения
if (!$conn) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}

// Установить кодировку
mysqli_set_charset($conn, 'utf8');

// Константа для базового URL
define('BASE_URL', '/public/');

// ============================================
// ФУНКЦИИ-ХЕЛПЕРЫ
// ============================================

// Экранирование строк + htmlspecialchars
function e($str) {
    global $conn;
    if ($str === null || $str === '') return '';
    return htmlspecialchars(mysqli_real_escape_string($conn, $str));
}

// Простое выполнение запроса
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('Ошибка запроса: ' . mysqli_error($conn) . '<br><pre>' . $sql . '</pre>');
    }
    return $result;
}

// Получить все строки как ассоциативный массив
function fetch_all($result) {
    if (!$result) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    return $rows;
}

// Получить одну строку
function fetch_one($result) {
    if (!$result) return null;
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row;
}

// Получить последний вставленный ID
function last_id() {
    global $conn;
    return mysqli_insert_id($conn);
}

// Количество строк в результате
function num_rows($result) {
    return $result ? mysqli_num_rows($result) : 0;
}
?>
