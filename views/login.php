<?php

// Підключення до БД (використовуємо той самий код, що й у registration.php)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'eshop';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if (!empty($login) && !empty($password)) {
        try {
            $stmt = $conn->prepare("SELECT id, password, admin FROM users WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $login;
                $_SESSION['admin'] = (bool)$user['admin'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Невірний логін або пароль';
            }
        } catch(PDOException $e) {
            $error = 'Помилка бази даних';
        }
    } else {
        $error = 'Будь ласка, заповніть всі поля';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main>
        <h1 class="login-text">Вхід</h1>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form class="login-form" method="POST" action="">
            <label for="login">Логін:</label>
            <input type="text" id="login" name="login" required><br>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Увійти</button>
        </form>
    </main>
</body>
</html>