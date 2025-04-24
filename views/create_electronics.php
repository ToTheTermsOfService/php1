<?php
require_once 'D:/phpThings/htdocs/dashboard/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $specs = trim($_POST['specs']);
    $visible = isset($_SESSION['admin']) && $_SESSION['admin'] ? 1 : 0;

    $errors = [];
    if (empty($name)) $errors[] = "Введіть назву товару";
    if ($price <= 0) $errors[] = "Невірна ціна";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO electronics 
                              (name, category, price, description, specs, visible, author_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $description, $specs, $visible, $_SESSION['user_id']]);
        
        header('Location: index.php?action=electronics');
        exit;
    }
}
?>

<form method="POST">
    <div class="add__good">
        <input type="text" name="name" placeholder="Назва" required>
        <select name="category" required>
            <option value="Смартфони">Смартфони</option>
            <option value="Ноутбуки">Ноутбуки</option>
        </select>
        <input type="number" step="0.01" name="price" placeholder="Ціна" required>
        <textarea name="description" placeholder="Опис"></textarea>
        <textarea name="specs" placeholder="Характеристики"></textarea>
        <button type="submit">Додати</button>
    </div>
</form>