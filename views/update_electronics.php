<?php
require_once 'D:/phpThings/htdocs/dashboard/config/db.php';

// Перевірка прав адміна
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$errors = [];

// Отримання даних товару
$stmt = $conn->prepare("SELECT * FROM electronics WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die("Товар не знайдено");
}

// Встановлення значень за замовчуванням
$defaults = [
    'name' => '',
    'category' => 'Смартфони',
    'price' => 0,
    'description' => '',
    'specs' => '',
    'visible' => 0
];

// Об'єднання з отриманими даними
$item = array_merge($defaults, $item);

// Обробка форми оновлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $specs = trim($_POST['specs'] ?? '');
    $visible = isset($_POST['visible']) ? 1 : 0;

    // Валідація даних
    if (empty($name)) {
        $errors[] = "Введіть назву товару";
    }
    if ($price <= 0) {
        $errors[] = "Невірна ціна";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE electronics SET 
                                  name = ?, 
                                  category = ?, 
                                  price = ?, 
                                  description = ?, 
                                  specs = ?, 
                                  visible = ? 
                                  WHERE id = ?");
            $stmt->execute([
                $name,
                $category,
                $price,
                $description,
                $specs,
                $visible,
                $id
            ]);
            
            header('Location: index.php?action=electronics');
            exit;
        } catch(PDOException $e) {
            $errors[] = "Помилка бази даних: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування товару</title>
    <link rel="stylesheet" href="/dashboard/stylesheets/style.css">
</head>
<body>
    <?php include 'layout/header.php'; ?>
    
    <main class="edit-container">
        <h1>Редагування товару</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="edit-form">
            <div class="form-group">
                <label>Назва:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Категорія:</label>
                <select name="category" required>
                    <option value="Смартфони" <?= $item['category'] === 'Смартфони' ? 'selected' : '' ?>>Смартфони</option>
                    <option value="Ноутбуки" <?= $item['category'] === 'Ноутбуки' ? 'selected' : '' ?>>Ноутбуки</option>
                    <option value="Планшети" <?= $item['category'] === 'Планшети' ? 'selected' : '' ?>>Планшети</option>
                    <option value="Аксесуари" <?= $item['category'] === 'Аксесуари' ? 'selected' : '' ?>>Аксесуари</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Ціна (грн):</label>
                <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['price']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Опис:</label>
                <textarea name="description"><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Характеристики:</label>
                <textarea name="specs"><?= htmlspecialchars($item['specs']) ?></textarea>
            </div>
            
            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="visible" <?= $item['visible'] ? 'checked' : '' ?>>
                    Опубліковано
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-update">Оновити</button>
                <a href="index.php?action=electronics" class="btn-cancel">Скасувати</a>
            </div>
        </form>
    </main>
    
    <?php include 'layout/footer.php'; ?>
</body>
</html>