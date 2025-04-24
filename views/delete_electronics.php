<?php
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$conn->query("DELETE FROM electronics WHERE id=$id");

header('Location: index.php?action=electronics');
exit;
?>