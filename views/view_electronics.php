<?php
require_once 'D:/phpThings/htdocs/dashboard/config/db.php';

$id = (int)$_GET['id'];
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];

// Використовуємо підготовлений запит для безпеки
$stmt = $conn->prepare("SELECT e.*, u.login as author 
                       FROM electronics e 
                       JOIN users u ON e.author_id = u.id 
                       WHERE e.id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Товар не знайдено або не опубліковано");
}
?>

<div class="product-view">
    <h1 class="product-title"><?= htmlspecialchars($item['name']) ?></h1>
    
    <div class="product-meta">
        <span class="product-author">Автор: <?= htmlspecialchars($item['author']) ?></span>
        <span class="product-date">Додано: <?= date('d.m.Y H:i', strtotime($item['date'])) ?></span>
        <?php if (!$item['visible']): ?>
            <span class="product-status">(Не опубліковано)</span>
        <?php endif; ?>
    </div>
    
    <div class="product-image">
        <!-- Тут може бути зображення товару -->
        <img src="placeholder.jpg" alt="<?= htmlspecialchars($item['name']) ?>">
    </div>
    
    <div class="product-info">
        <div class="product-price">Ціна: <strong><?= number_format($item['price'], 2, '.', ' ') ?> грн</strong></div>
        <div class="product-category">Категорія: <?= htmlspecialchars($item['category']) ?></div>
    </div>
    
    <div class="product-description">
        <h3>Опис:</h3>
        <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
    </div>
    
    <div class="product-specs">
        <h3>Характеристики:</h3>
        <p><?= nl2br(htmlspecialchars($item['specs'])) ?></p>
    </div>
    
    <?php if ($is_admin): ?>
    <div class="product-actions">
        <a href="index.php?action=update_electronics&id=<?= $item['id'] ?>" class="btn-edit">Редагувати</a>
        <a href="index.php?action=delete_electronics&id=<?= $item['id'] ?>" 
           class="btn-delete" onclick="return confirm('Видалити цей товар?')">Видалити</a>
    </div>
    <?php endif; ?>
</div>

<style>
.product-view {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.product-title {
    color: #333;
    margin-bottom: 10px;
}

.product-meta {
    color: #666;
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.product-status {
    color: #ff9800;
    font-weight: bold;
}

.product-image {
    margin-bottom: 20px;
}

.product-image img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.product-info {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}

.product-price {
    font-size: 1.2rem;
    margin-bottom: 10px;
}

.product-category {
    color: #666;
}

.product-description, .product-specs {
    margin-bottom: 20px;
}

.product-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}

.btn-edit, .btn-delete {
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
}

.btn-edit {
    background: #4CAF50;
}

.btn-edit:hover {
    background: #3e8e41;
}

.btn-delete {
    background: #f44336;
}

.btn-delete:hover {
    background: #d32f2f;
}
</style>