<nav class="left-menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php?action=about">About</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><span>Вітаємо, <?= htmlspecialchars($_SESSION['login']) ?></span></li>
            <?php if ($_SESSION['admin']): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="index.php?action=login">Login</a></li>
            <li><a href="index.php?action=registration">Register</a></li>
        <?php endif; ?>
        <li><a href="index.php?action=electronics">Electronics</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="index.php?action=create_electronics">Add good</a></li>
        <?php endif; ?>
    </ul>
</nav>