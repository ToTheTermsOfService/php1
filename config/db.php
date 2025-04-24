<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'eshop';

try {
    $conn = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", 
        $db_user, 
        $db_pass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}
?>