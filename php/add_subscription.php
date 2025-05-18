<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Подключение к базе данных
    $conn = new PDO("sqlite:../database/users.db");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Пример значений
    $user_id = 1;  // ID пользователя (из таблицы users)
    $type = "Премиум абонемент";
    $start_date = "2025-05-15";
    $end_date = "2026-05-15";
    $total_sessions = 20;
    $used_sessions = 6;

    // SQL-запрос вставки
    $stmt = $conn->prepare("
        INSERT INTO subscriptions (user_id, type, start_date, end_date, total_sessions, used_sessions)
        VALUES (:user_id, :type, :start_date, :end_date, :total_sessions, :used_sessions)
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':type' => $type,
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':total_sessions' => $total_sessions,
        ':used_sessions' => $used_sessions,
    ]);

    echo "Абонемент успешно добавлен!";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
} 
