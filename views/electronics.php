<?php
require_once 'D:/phpThings/htdocs/dashboard/config/db.php';

if (!isset($conn)) {
    die("Помилка підключення до бази даних");
}

$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];
$sql = "SELECT * FROM electronics ORDER BY date DESC";
$items = $conn->query($sql)->fetchAll();
?>

<div class="electronics-list">
    <?php foreach ($items as $item): ?>
        <div class="electronics-item <?= $item['visible'] ? '' : 'unpublished' ?>">
            <div class="item-image">
                <!-- Тут може бути зображення товару -->
                <img src="placeholder.jpg" alt="<?= htmlspecialchars($item['name']) ?>">
            </div>
            <div class="item-content">
                <h3 class="item-title"><?= htmlspecialchars($item['name']) ?></h3>
                <p class="item-price"><?= number_format($item['price'], 2, '.', ' ') ?> грн</p>
                <p class="item-category">Категорія: <?= htmlspecialchars($item['category']) ?></p>
                
                <div class="item-actions">
                    <a href="index.php?action=view_electronics&id=<?= $item['id'] ?>" class="btn-details">Детальніше</a>
                    
                    <?php if ($is_admin): ?>
                        <a href="index.php?action=update_electronics&id=<?= $item['id'] ?>" class="btn-edit">Редагувати</a>
                        <a href="index.php?action=delete_electronics&id=<?= $item['id'] ?>" 
                           class="btn-delete" onclick="return confirm('Видалити цей товар?')">Видалити</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>