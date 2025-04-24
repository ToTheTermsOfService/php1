<?php
$db_host = 'localhost';
$db_user = 'root'; 
$db_pass = ''; 
$db_name = 'eshop'; 

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}

$errors = [];
$regions = [];

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

    if (!preg_match('/^[a-zA-Zа-яА-Я0-9_-]{4,}$/', $login)) {
        $errors['login'] = 'Логін повинен містити не менше 4 символів і може включати лише літери, цифри, "_" та "-".';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,}$/', $password)) {
        $errors['password'] = 'Пароль повинен містити не менше 7 символів, включаючи великі та малі літери та цифри.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Паролі не співпадають.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введіть коректну email-адресу.';
    }

    if (empty($regions)) {
        $errors['region'] = 'Список областей недоступний.';
    } elseif (!is_numeric($region) || $region < 1) {
        $errors['region'] = 'Оберіть область зі списку.';
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
            $stmt->execute([$login, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors['database'] = 'Користувач з таким логіном або email вже існує.';
            } else {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                
                $stmt = $conn->prepare("INSERT INTO users (login, password, email, region) VALUES (?, ?, ?, ?)");
                $stmt->execute([$login, $password_hash, $email, $region]);
                
                header('Location: index.php?action=registration_successful');
                exit;
            }
        } catch(PDOException $e) {
            $errors['database'] = 'Помилка бази даних: ' . $e->getMessage();
        }
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
        <h1 class="reg-text">Реєстрація</h1>
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
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required><br>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="confirm_password">Повторіть пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>

            <label for="email">Електронна пошта:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br>

            <label for="region">Область:</label>
            <select id="region" name="region" required>
                <option value="">Оберіть область</option>
                <?php foreach ($regions as $region_line): ?>
                    <?php list($code, $name) = explode('|', $region_line); ?>
                    <option value="<?= $code ?>" <?= isset($_POST['region']) && $_POST['region'] == $code ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit">Зареєструватися</button>
        </form>
    </main>
</body>
</html>