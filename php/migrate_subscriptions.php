<?php
try {
    $db = new PDO('sqlite:../database/users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Создание таблицы subscriptions
    $sql = "
    CREATE TABLE IF NOT EXISTS subscriptions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        type TEXT NOT NULL,
        start_date TEXT NOT NULL,
        end_date TEXT NOT NULL,
        total_sessions INTEGER NOT NULL,
        used_sessions INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );
    ";

    $db->exec($sql);
    echo "Таблица subscriptions успешно создана.";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
