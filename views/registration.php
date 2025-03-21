<?php
// Обробка форми
$errors = [];
$regions = [];

// Зчитування областей з файлу
$regions_file = 'regions.txt';
if (file_exists($regions_file)) {
    $regions = file($regions_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
} else {
    $errors['regions'] = 'Файл з областями не знайдено або він порожній.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $region = trim($_POST['region']);

    // Перевірка логіну
    if (!preg_match('/^[a-zA-Zа-яА-Я0-9_-]{4,}$/', $login)) {
        $errors['login'] = 'Логін повинен містити не менше 4 символів і може включати лише літери, цифри, "_" та "-".';
    }

    // Перевірка пароля
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,}$/', $password)) {
        $errors['password'] = 'Пароль повинен містити не менше 7 символів, включаючи великі та малі літери та цифри.';
    }

    // Перевірка підтвердження пароля
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Паролі не співпадають.';
    }

    // Перевірка email
    if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
        $errors['email'] = 'Введіть коректну email-адресу.';
    }

    // Перевірка області
    if (empty($regions)) {
        $errors['region'] = 'Список областей недоступний.';
    } elseif (!is_numeric($region) || $region < 1) {
        $errors['region'] = 'Оберіть область зі списку.';
    }

    // Якщо помилок немає, перенаправляємо на сторінку успіху
    if (empty($errors)) {
        header('Location: index.php?action=registration_successful');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main>
        <h1 class ="reg-text">Реєстрація</h1>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="registration-form" method="POST" action="">
            <label for="login">Логін:</label>
            <input type="text" id="login" name="login" required><br>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="confirm_password">Повторіть пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>

            <label for="email">Електронна пошта:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="region">Область:</label>
            <select id="region" name="region" required>
                <option value="">Оберіть область</option>
                <?php
                $regions = file('regions.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($regions as $region) {
                    list($code, $name) = explode('|', $region);
                    echo "<option value='$code'>$name</option>";
                }
                ?>
            </select><br>

            <button type="submit">Зареєструватися</button>
        </form>
    </main>
</body>
</html>