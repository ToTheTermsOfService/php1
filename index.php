<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'main';

$valid_actions = ['main', 'about', 'registration', 'registration_successful'];
if (!in_array($action, $valid_actions)) {
    $action = 'main';
}

include 'layout/header.php';
include 'layout/left_menu.php';
include "views/$action.php";
include 'layout/footer.php';
?> 